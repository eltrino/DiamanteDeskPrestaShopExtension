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
{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" xmlns="http://www.w3.org/1999/html">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='My Tickets'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
{if isset($success)}
    <p class="alert alert-success">{$success}</p>
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

    {if $start!=$stop}
        {assign var='requestPage' value={$link->getModuleLink('diamantedesk','mytickets')|escape:'html':'UTF-8'}}

        <ul class="pagination">
            {if $p != 1}
                {assign var='p_previous' value=$p-1}
                <li id="pagination_previous"><a
                            href="{$link->goPage($requestPage, $p_previous)}">&laquo;&nbsp;{l s='Previous'}</a></li>
            {else}
                <li id="pagination_previous" class="disabled"><span>&laquo;&nbsp;{l s='Previous'}</span></li>
            {/if}

            {if $start-$range<$range}
                {section name=pagination_start start=1 loop=$start step=1}
                    <li>
                        <a href="{$link->goPage($requestPage, $smarty.section.pagination_start.index)}">{$smarty.section.pagination_start.index|escape:'htmlall':'UTF-8'}</a>
                    </li>
                {/section}
            {/if}

            {if $start>3}
                <li><a href="{$link->goPage($requestPage, 1)}">1</a></li>
                <li class="truncate">...</li>
            {/if}
            {section name=pagination start=$start loop=$stop+1 step=1}
                {if $p == $smarty.section.pagination.index}
                    <li class="current"><span>{$p|escape:'htmlall':'UTF-8'}</span></li>
                {else}
                    <li>
                        <a href="{$link->goPage($requestPage, $smarty.section.pagination.index)}">{$smarty.section.pagination.index|escape:'htmlall':'UTF-8'}</a>
                    </li>
                {/if}
            {/section}
            {if $pages_nb>$stop+2}
                <li class="truncate">...</li>
                <li><a href="{$link->goPage($requestPage, $pages_nb)}">{$pages_nb|intval}</a></li>
            {/if}

            {if $stop+$range>$pages_nb-1}
                {section name=pagination_start start=$stop+1 loop=$pages_nb+1 step=1}
                    <li>
                        <a href="{$link->goPage($requestPage, $smarty.section.pagination_start.index)}">{$smarty.section.pagination_start.index|escape:'htmlall':'UTF-8'}</a>
                    </li>
                {/section}
            {/if}

            {if $pages_nb > 1 AND $p != $pages_nb}
                {assign var='p_next' value=$p+1}
                <li id="pagination_next"><a href="{$link->goPage($requestPage, $p_next)}">{l s='Next'}&nbsp;&raquo;</a>
                </li>
            {else}
                <li id="pagination_next" class="disabled"><span>{l s='Next'}&nbsp;&raquo;</span></li>
            {/if}
        </ul>
    {/if}

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
            <input class="is_required validate form-control"
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