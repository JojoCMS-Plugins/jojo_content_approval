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
$newValues = unserialize($newRow['data']);
$tableName = $newRow['table'];
$recordid = $newRow['recordid'];
$message = Jojo::getFormData('message', '');

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

/* Change the revision info */
Jojo::updateQuery('UPDATE {versionhistory} SET status = ? WHERE `versionhistoryid` = ?', array(strftime('Declined %x %X - ') . $message, $id));
echo json_encode(array(
                    'result' => true,
                    'message' => ''
                    )
    );
exit;
