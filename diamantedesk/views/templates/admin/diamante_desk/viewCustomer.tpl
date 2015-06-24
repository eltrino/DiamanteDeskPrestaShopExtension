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
<div class="tickets row">
    {if $tickets && count($tickets)}
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-admindiamantedesk"></i>
                    {l s='Related Tickets'}
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{l s='Ticket Id'}</th>
                            <th>{l s='Subject'}</th>
                            <th>{l s='Date'}</th>
                            <th>{l s='Priority'}</th>
                            <th>{l s='Status'}</th>
                            <th></th>
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
                                    <a href="{$diamantedesk_server_address}desk/tickets/view/{$ticket->key}"
                                       target="_blank">{l s='View'}</a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    {/if}
</div>

<script>
    jQuery(document).ready(function () {
        jQuery('.tickets').prependTo('#start_products');
    });
</script>