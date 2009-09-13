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

$default_td['page']['td_defaultpermissions'] = "everyone.show=1\neveryone.view=1\neveryone.edit=0\neveryone.add=0\neveryone.delete=0\nadmin.show=0\nadmin.view=0\nadmin.edit=0\nadmin.add=0\nadmin.delete=0\neditor.show=0\neditor.view=0\neditor.edit=0\neditor.add=0\neditor.delete=0\npublisher.show=0\npublisher.view=0\npublisher.edit=1\npublisher.add=1\npublisher.delete=1\nnotloggedin.show=0\nnotloggedin.view=0\nnotloggedin.edit=0\nnotloggedin.add=0\nnotloggedin.delete=0\nregistered.show=0\nregistered.view=0\nregistered.edit=0\nregistered.add=0\nregistered.delete=0\nsysinstall.show=0\nsysinstall.view=0\nsysinstall.edit=0\nsysinstall.add=0\nsysinstall.delete=0\n";

/* History Tab */

// Last Updated Field
$default_fd['page']['pg_updated'] = array(
        'fd_name' => "Last Updated",
        'fd_type' => "timestamp",
        'fd_default' => "CURRENT_TIMESTAMP",
        'fd_order' => "1",
        'fd_tabname' => "History",
    );

// Revision Field
$default_fd['page']['pg_version'] = array(
        'fd_name' => "Revision",
        'fd_type' => "version",
        'fd_default' => "0",
        'fd_help' => "The revision number of this page, indicates the number of times this record has been changed since it was created.",
        'fd_order' => "2",
        'fd_tabname' => "History",
        'fd_showlabel' => "no",
    );
