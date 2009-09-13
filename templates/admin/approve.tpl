{include file="admin/header.tpl"}

{foreach $newVersions new}
<table class="approve">
    <thead>
        <tr>
            <th style='width: 16.66%'>Table</th>
            <td style='width: 16.66%'>{$new.table}</td>
            <th style='width: 16.66%'>Record ID</th>
            <td style='width: 16.66%'>{tif $new.recordid $new.recordid 'New Record'}</td>
            <th style='width: 16.66%'>New Version</th>
            <td style='width: 16.66%'>{$new.version}</td>
        </tr>
        <tr>
            <th>Date</th>
            <td>{$new.date}</td>
            <th>User</th>
            <td>{$new.username}</td>
            <td colspan="2"></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="6">Changes</th>
        </tr>
{foreach $new.diff field r}
        <tr>
            <th>{$field}</th>
            <td colspan="5" class="revisiondiff">
                {$r|nl2br}
            </td>
        </tr>
{/foreach}
{if $new.delete}
        <tr>
            <td colspan="6" style="background: red; text-align: center; padding: 5px;">
                Delete Record
            </td>
        </tr>
{/if}
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">
                <input type="button" value="Approve" onclick="approveVersion($(this), '{$new.versionhistoryid}');"/>
                <input type="button" value="Decline" onclick="declineVersion($(this), '{$new.versionhistoryid}');"/>
            </td>
        </tr>
    </tfoot>
</table>

{/foreach}

<script type="text/javascript">/* <![CDATA[ */{literal}

function declineVersion(button, id)
{
    message = prompt('Reason for declining this verson?');
    if (message == null) {
        /* Cancel clicked */
        return;
    }

    var table = button.parent().parent().parent().parent();
    $('tbody', table).html('<tr><th colspan="6"><h1 style="color:white">Declining...</h1></th></tr>');
    $('tfoot', table).hide();

    jQuery.post(
        'json/decline.php',
        {
            id: id,
            message: message
        },
        function (data) {
            if (data['result']) {
                $('tbody', table).html('<tr><th colspan="6"><h1 style="color:white">Declined</h1></th></tr>');
                $(table).fadeOut(3000);
                location.reload();
            } else {
                alert(data['message']);
            }
        },
        'json'
    );

}

function approveVersion(button, id)
{
    button.attr('disabled', 'disabled');

    var table = button.parent().parent().parent().parent();
    $('tbody', table).html('<tr><th colspan="6"><h1 style="color:white">Approving...</h1></th></tr>');
    $('tfoot', table).hide();

    jQuery.post(
        'json/approve.php',
        {
            id: id
        },
        function (data) {
            if (data['result']) {
                $('tbody', table).html('<tr><th colspan="6"><h1 style="color:white">Approved</h1></th></tr>');
                $(table).fadeOut(3000);
                location.reload();
            } else {
                alert(data['message']);
            }
        },
        'json'
    );
}

{/literal}/* ]]> */</script>

{include file="admin/footer.tpl"}