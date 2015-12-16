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
    <a href="{$link->getModuleLink('diamantedesk','mytickets')|escape:'html':'UTF-8'}"
       xmlns="http://www.w3.org/1999/html">
        {l s='My Tickets'}
    </a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='View Ticket: '}<b> {$ticket->key}</b></span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
{if isset($success)}
    <p class="alert alert-success">{$success}</p>
{/if}
<h1 class="page-heading bottom-indent">{l s='View ticket:'} {$ticket->key}</h1>
<div class="block-center" id="block-history">


    <section class="page-product-box">
        <h3 class="page-product-heading">{l s='Subject:'} {$ticket->subject}</h3>
        <table class="table-data-sheet">
            <tbody>
            <tr>
                <td>{l s='Status'}</td>
                <td>{l s=$ticket->status}</td>
            </tr>

            <tr>
                <td>{l s='Priority'}</td>
                <td>{l s=$ticket->priority}</td>
            </tr>

            <tr>
                <td>{l s='Description'}</td>
                <td>{$ticket->description}</td>
            </tr>
            </tbody>
        </table>
    </section>

    {if !empty($ticket->comments)}
        <section class="page-product-box">

            <h3 id="#idTab5" class="idTabHrefShort page-product-heading">{l s='Comments'}:</h3>

            <div id="idTab5">
                <div id="product_comments_block_tab">
                    {foreach from=$ticket->comments item=comment}
                        <div class="comment row" itemprop="review" itemscope="" itemtype="http://schema.org/Review">
                            <div class="comment_author col-sm-2">
                                <div class="comment_author_infos">
                                    <strong itemprop="author">{$comment->authorName}</strong>
                                    <em>{$comment->created_at|date_format:'%Y-%m-%d %H:%M:%S'}</em>
                                </div>
                            </div>
                            <!-- .comment_author -->
                            <div class="comment_details col-sm-10">
                                <p itemprop="reviewBody">{$comment->content}</p>
                            </div>
                            <!-- .comment_details -->
                        </div>
                    {/foreach}
                </div>
                <!-- #product_comments_block_tab -->
            </div>
        </section>
    {else}
        <p>
            <b>{l s='There are no comments.'}</b>
        </p>
    {/if}
</div>
<div class="clearfix main-page-indent">
    <a onclick="showCommentTicketForm(this);return false;"
       title="{l s='Add comment'}"
       class="btn btn-default button button-medium"><span>{l s='Add Comment'}<i
                    class="icon-chevron-right right"></i></span></a>
</div>

<div class="box" id="comment_form" style="display: none;">
    <form method="post" class="std" id="add_comment">
        <div class="form-group required">
            <label for="comment">{l s='Comment text'}<sup>*</sup></label>
            <textarea class="validate form-control" id="comment" name="comment" cols="26" rows="3"></textarea>
        </div>
        <p class="submit2">
            <input type="hidden" name="token" value="{$token}"/>
            <input type="hidden" name="ticket" value="{$ticket->id}"/>
            <input type="hidden" name="ticketStatus" value="{$ticket->status}"/>
            <button type="submit" name="submitComment" id="submitComment" class="btn btn-default button button-medium">
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
    function showCommentTicketForm(button) {
        jQuery(button).parent().hide();
        jQuery('#comment_form').show();
    }
</script>