<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2009 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package Jojo_VersionHistory
 */

$table = 'versionhistory';
$query = "
    CREATE TABLE {versionhistory} (
      `versionhistoryid` bigint(20) NOT NULL auto_increment,
      `table` varchar(255) NOT NULL,
      `recordid` varchar(255) default NULL,
      `version` int(11) NOT NULL,
      `user` varchar(255) NOT NULL,
      `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
      `data` longtext NOT NULL,
      `status` varchar(50) default NULL,
      PRIMARY KEY  (`versionhistoryid`),
      KEY `table` (`table`,`version`),
      KEY `status` (`status`)
    ) TYPE=MyISAM;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);