<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Plugin_Approve extends Jojo_Plugin
{
    public static function preDeleteHook($table)
    {
        if (!$table->getFieldByType('Jojo_Field_Version')) {
            /* Not a versioned table */
            return;
        }

        /* Find the next version number for this record */
        $row = Jojo::selectRow('SELECT MAX(version) as currentVersion FROM {versionhistory} WHERE `table` = ? AND recordid = ?',
                array($table->getTableName(), $table->getRecordID()));
        $newVersion = $row['currentVersion'] + 1;

        /* Save the new row and other stuff into the version history table */
        $query = 'INSERT INTO {versionhistory} SET
                            `table` = ?,
                            `recordid` = ?,
                            `version` = ?,
                            `user` = ?,
                            `date` = NOW(),
                            `data` = ?,
                            `status` = ?';
        Jojo::insertQuery($query, array(
                              $table->getTableName(),
                              $table->getRecordID(),
                              $newVersion,
                              $_USERID ? $_USERID : 0,
                              'delete',
                              'pending'));

        $frajax = new frajax(true);
        $frajax->script('parent.$("#error").html("<h4>Awaiting Approval</h4>Awaiting approval for delete.").fadeIn("slow");');
        $frajax->sendFooter();
        exit;
    }


    function _getContent()
    {
        global $smarty, $_USERGROUPS;

        /* Create the Diff object. */
        $dir = realpath(dirname(__FILE__) . '/../../../');
        require_once $dir . '/external/text_diff/Diff.php';
        require_once $dir . '/external/text_diff/Diff/Renderer.php';
        require_once $dir . '/external/text_diff/Diff/Renderer/inline.php';
        $renderer = new Text_Diff_Renderer_inline();

        /* Get revisions awaiting approval */
        $fields = array();
        $newVersions = Jojo::selectQuery('SELECT vh.*, concat(us_firstname, " ", us_lastname) as username FROM {versionhistory} vh LEFT JOIN {user} u ON (vh.user = u.userid) WHERE vh.status = "pending" ORDER BY vh.`table`, vh.`recordid`, vh.`version`');
        foreach ($newVersions as $j => $row) {
            /* Get info about this table */
            $table = Jojo_Table::singleton($row['table']);
            if (!isset($fields[$row['table']])) {
                $fields[$row['table']]['names'] = Jojo::selectAssoc('SELECT fd_field, fd_name FROM {fielddata} WHERE fd_table = ?', $table->getTableName());
                $fields[$row['table']]['hidden'] = Jojo::selectAssoc('SELECT fd_field, fd_name FROM {fielddata} WHERE fd_table = ? AND fd_type = "hidden"', $table->getTableName());
            }

            if ($row['data'] == 'delete') {
                /* Request to delete row */
                $newVersions[$j]['delete'] = true;
                continue;
            }

            /* Get the rows to diff */
            $newRow = unserialize($row['data']);
            $previousRow = Jojo::selectRow('SELECT data FROM {versionhistory} WHERE `table` = ? AND `recordid` = ? AND `version` < ? ORDER BY `version` DESC', array($row['table'], $row['recordid'], $row['version']));
            if ($previousRow) {
                /* Found previous version in version history */
                $previousRow = unserialize($previousRow['data']);
            } else {
                /* Look for previous version in the table */
                $previousRow = Jojo::selectRow('SELECT * FROM {' . $row['table'] . '} WHERE `' . $table->getOption('primarykey') . '` = ?', $row['recordid']);
            }


            /* Work out which columns changed between versions */
            $changes = array();
            foreach ($newRow as $k => $v) {
                if (isset($fields[$row['table']]['hidden'][$k])) {
                    /* Skip the version field as this will always change, and hidden fields */
                    continue;
                }
                if (!isset($previousRow[$k]) || $newRow[$k] != $previousRow[$k]) {
                    $field = $table->getField($k);
                    if ($field) {
                        $field->setValueFromDB($newRow[$k]);
                        $new =  str_replace("\r", "", $field->displayView());

                        if (isset($previousRow[$k])) {
                            $field->setValueFromDB($previousRow[$k]);
                        } else {
                            $field->setValueFromDB('');
                        }
                        $old =  str_replace("\r", "", $field->displayView());
                    } else {
                        $new = str_replace("\r", "", $newRow[$k]);
                        $old = (isset($previousRow[$k])) ? str_replace("\r", "", $previousRow[$k]) : '';
                    }

                    if ($new != $old) {
                        $new = preg_split("/[\n\r]/", $new);
                        $old = preg_split("/[\n\r]/", $old);
                        $changes[$fields[$row['table']]['names'][$k]] = $renderer->render(new Text_Diff('auto', array($old, $new)));
                    }
                }
            }
            $newVersions[$j]['diff'] = $changes;
        }
        $smarty->assign('newVersions', $newVersions);

        Jojo_Plugin_Admin::adminMenu();
        $content['content'] = $smarty->fetch('admin/approve.tpl');

        return $content;
    }

}