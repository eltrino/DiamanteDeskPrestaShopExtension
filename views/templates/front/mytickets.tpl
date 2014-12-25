{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" xmlns="http://www.w3.org/1999/html">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='My Tickets'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
{if isset($success)}
    <p class="success">{$success}</p>
{/if}
<h1 class="page-heading bottom-indent">{l s='My Tickets'}</h1>
<p class="info-title">{l s='Here are the tickets you\'ve submited since your account was created.'}</p>
<div class="block-center" id="block-history">
    {if $tickets && count($tickets)}
        <table id="order-list" class="table table-bordered footab">
            <thead>
            <tr>
                <th class="first_item" data-sort-ignore="true">{l s='Ticket Id'}</th>
                <th class="item">{l s='Subject'}</th>
                <th data-hide="phone" class="item">{l s='Date'}</th>
                <th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Priority'}</th>
                <th class="item">{l s='Status'}</th>
                <th data-sort-ignore="true" data-hide="phone,tablet" class="last_item"></th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$tickets item=ticket name=myLoop}
                <tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
                    <td class="history_link bold">
                        {$ticket->id|intval}
                    </td>
                    <td class="bold">
                        {$ticket->subject}
                    </td>
                    <td>
                        {$ticket->created_at|date_format:'%Y-%m-%d %H:%M:%S'}
                    </td>
                    <td>
                        {$ticket->priority}
                    </td>
                    <td>
                        {$ticket->status}
                    </td>
                    <td>
                        <a href="?ticket={$ticket->id}">View</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <div id="block-ticket-detail" class="unvisible">&nbsp;</div>
    {else}
        <p class="alert alert-warning">{l s='You have not submit any tickets.'}</p>
    {/if}
</div>
<div class="clearfix main-page-indent">
    <a onclick="showCreateTicketForm(this);return false;"
       title="{l s='Create a new Ticket'}"
       class="btn btn-default button button-medium"><span>{l s='Submit a new Ticket'}<i
                    class="icon-chevron-right right"></i></span></a>
</div>

<div class="box" id="ticket_form" style="display: none;">
    <form method="post" class="std" id="add_ticket">
        <div class="required form-group">
            <label for="subject">{l s='Subject'}<sup>*</sup></label>
            <input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}"
                   type="text" name="subject" id="subject"
                   value="{if isset($smarty.post.subject)}{$smarty.post.subject}{else}{/if}"/>
        </div>
        <div class="form-group required">
            <label for="description">{l s='Description'}<sup>*</sup></label>
            <textarea class="validate form-control" id="description" name="description" cols="26"
                      rows="3">{if isset($smarty.post.description)}{$smarty.post.description}{/if}</textarea>
        </div>
        <p class="submit2">
            <input type="hidden" name="token" value="{$token}"/>
            <button type="submit" name="submitTicket" id="submitTicket" class="btn btn-default button button-medium">
				<span>
					Submit
					<i class="icon-chevron-right right"></i>
				</span>
            </button>
        </p>
    </form>
</div>

<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small"
           href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account'}
			</span>
        </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{$base_dir}">
            <span><i class="icon-chevron-left"></i> {l s='Home'}</span>
        </a>
    </li>
</ul>

<script>
    function showCreateTicketForm(button) {
        jQuery(button).parent().hide();
        jQuery('#ticket_form').show();
    }
</script>