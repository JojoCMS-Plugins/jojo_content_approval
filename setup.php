<?php

// Approve Content Changes
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'Jojo_Plugin_Approve'")) {
    echo "Adding <b>Approve Content Changes</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Approve Content Changes', pg_link = 'Jojo_Plugin_Approve', pg_url = 'admin/approve', pg_parent = ?, pg_order=1", array($_ADMIN_CONTENT_ID));
}

// Content Publisher user group
if (!Jojo::selectRow("SELECT * FROM {usergroups} WHERE groupid = 'publisher'")) {
    echo "Adding <b>Content Publisher</b> usergroup.<br />";
    Jojo::insertQuery("INSERT INTO {usergroups} SET groupid = 'publisher', gr_name = 'Content Publisher'");
}

// Content Editor user group
if (!Jojo::selectRow("SELECT * FROM {usergroups} WHERE groupid = 'editor'")) {
    echo "Adding <b>Content Editor</b> usergroup.<br />";
    Jojo::insertQuery("INSERT INTO {usergroups} SET groupid = 'editor', gr_name = 'Content Editor'");
}

// Assign admin users to editor groups if there's no editors
if (!Jojo::selectRow("SELECT * FROM {usergroup_membership} WHERE groupid = 'editor'")) {
    Jojo::insertQuery("INSERT INTO {usergroup_membership} SELECT DISTINCT userid, 'editor' as groupid FROM {usergroup_membership} WHERE groupid = 'admin'");
}

// Assign admin users to publisher groups if there are no publishers
if (!Jojo::selectRow("SELECT * FROM {usergroup_membership} WHERE groupid = 'publisher'")) {
    Jojo::insertQuery("INSERT INTO {usergroup_membership} SELECT DISTINCT userid, 'publisher' as groupid FROM {usergroup_membership} WHERE groupid = 'admin'");
}
