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

<!-- MODULE DiamanteDesk -->
<style>
    .icon-diamante-desk {
        background-image: url("{$module_template_dir}img/AdminDiamanteDeskDark.png");
        background-position: center center;
        background-size: 99%;
    }
</style>
<li class="lnk_wishlist">
    <a href="{$link->getModuleLink('diamantedesk','mytickets')|escape:'html':'UTF-8'}" title="{l s='My Tickets' mod='diamantedesk'}">
        <i class="icon-diamante-desk"></i>
        <span>{l s='My Tickets' mod='diamantedesk'}</span>
    </a>
</li>
<!-- END : MODULE DiamanteDesk -->