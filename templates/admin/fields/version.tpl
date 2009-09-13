<h3>Current Version: {$value}</h3>

{foreach from=$revisions item=r}{if $r.version != 0}
    <h3{if $value == $r.version} style='color:green'{/if}>Revision {$r.version} - {$r.date|date_format} by {$r.user}
        {if $value == $r.version} - current version
        {elseif $value < $r.version && $r.status == 'pending'}<span style="color:red"> - pending approval</span>
        {else} - {$r.status}
        {/if}
</h3>
{foreach $r.changelog field r}
    <h4>{$field}</h4>
    <div class="revisiondiff">
        {$r|nl2br|htmlentities}
    </div>
{/foreach}{/if}
{foreachelse}
    There are no recorded revisions to this content
{/foreach}

