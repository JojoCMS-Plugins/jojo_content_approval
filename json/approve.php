<?php

/* Get the new version */
$id = Jojo::getFormData('id', false);
$newRow = Jojo::selectRow('SELECT * FROM {versionhistory} WHERE `versionhistoryid` = ?', array($id));
if (!$newRow) {
    echo json_encode(array(
                        'result' => false,
                        'message' => 'Version not found.'
                        )
        );
    exit;
}
$tableName = $newRow['table'];
$recordid = $newRow['recordid'];
$version = $newRow['version'];

$delete = false;
if ($newRow['data'] == 'delete') {
    $delete = true;
} else {
    $newValues = unserialize($newRow['data']);
}


if ($recordid && $delete) {
    /* Do we have permission to delete this record? */
    $perms = new Jojo_Permissions();
    $perms->getPermissions($tableName, $recordid);
    if (!$perms->hasPerm($_USERGROUPS, 'delete')) {
        echo json_encode(array(
                            'result' => false,
                            'message' => 'You do not have permission to delete this record.'
                            )
            );
        exit;
    }
} elseif ($recordid) {
    /* Do we have permission to edit this record? */
    $perms = new Jojo_Permissions();
    $perms->getPermissions($tableName, $recordid);
    if (!$perms->hasPerm($_USERGROUPS, 'edit')) {
        echo json_encode(array(
                            'result' => false,
                            'message' => 'You do not have permission to edit this record.'
                            )
            );
        exit;
    }
} else {
    /* Do we have permission to add to this table */
    $perms = new Jojo_Permissions();
    $perms->getPermissions($tableName, $recordid);
    if (!$perms->hasPerm($_USERGROUPS, 'add')) {
        echo json_encode(array(
                            'result' => false,
                            'message' => 'You do not have permission to add to this table.'
                            )
            );
        exit;
    }
}

/* Get that table and record */
$table = Jojo_Table::singleton($tableName);
if ($recordid) {
    $table->getRecord($recordid);
}

if ($recordid && $delete) {
    if ($table->deleteRecord() == true) {
        /* Deleted sucessfully */
        Jojo::runHook('admin_action_delete_success', array($table));
        echo json_encode(array(
                        'result' => true,
                        'message' => ''
                        )
        );
    } else {
        /* Error deleting */
        echo json_encode(array(
                        'result' => false,
                        'message' => 'Error deleting record'
                        )
        );
    }
    exit;
}

/* Save the changes */
foreach($newValues as $f => $v) {
    $table->getField($f)->setValueFromDB($v);
}
$table->getFieldByType('Jojo_Field_Version')->setValueFromDB($version);
$table->saveRecord();

/* Change the revision info */
if ($recordid) {
    Jojo::updateQuery('UPDATE {versionhistory} SET status = ? WHERE `versionhistoryid` = ?', array(strftime('Approved %x %X'), $id));
} else {
    Jojo::updateQuery('UPDATE {versionhistory} SET status = ?, recordid = ? WHERE `versionhistoryid` = ?', array(strftime('Approved %x %X'), $table->getRecordID(), $id));
}
echo json_encode(array(
                    'result' => true,
                    'message' => ''
                    )
    );
exit;
