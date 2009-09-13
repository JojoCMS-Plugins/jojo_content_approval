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

$_provides['fieldTypes'] = array(
        'version'          => 'Version History',
        );

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Approve'                 => 'Content Approval - Admin page',
        );

Jojo::addHook('admin_action_pre_delete', 'predeletehook', 'Approve');

