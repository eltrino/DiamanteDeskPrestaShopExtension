<!--
/**
 * Copyright (c) 2014 Eltrino LLC (http://eltrino.com)
 *
 * Licensed under the Open Software License (OSL 3.0).
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://opensource.org/licenses/osl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 */
 -->
{if !empty($related_tickets)}
    <h3 class="page-heading bottom-indent">{l s='Related tickets to this order'}</h3>
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
        {foreach from=$related_tickets item=ticket name=myLoop}
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
                    <a href="{$link->getModuleLink('diamantedesk','mytickets')}?ticket={$ticket->id}">View</a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}
<h3 class="page-heading bottom-indent">{l s='Send a ticket about this order'}</h3>

<div class="box">
    <form action="{$link->getModuleLink('diamantedesk','mytickets')|escape:'html':'UTF-8'}" method="post" class="std"
          id="sendTicket">

        <p class="form-group">
            <label for="subject">{l s='Subject'}<sup>*</sup></label>
            <input class="is_required validate form-control" type="text" name="subject" id="subject" "/>
        </p>

        <p class="form-group">
            <label for="description">{l s='Description'}<sup>*</sup></label>
            <textarea class="validate form-control" id="description" name="description" cols="26"
                      rows="3"></textarea>
        </p>

        <div>&nbsp;</div>
        <div class="submit">
            <input type="hidden" name="id_order" value="{$order->id|intval}"/>
            <input type="submit" class="unvisible" name="submitTicket" value="{l s='Send'}"/>
            <button type="submit" name="submitTicket" class="button btn btn-default button-medium"><span>{l s='Send'}<i
                            class="icon-chevron-right right"></i></span></button>
        </div>
    </form>
</div>