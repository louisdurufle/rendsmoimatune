{include file='inc/header-html.tpl'}
<!-- Additionnal javascript script -->
{include file='inc/js-includes/date-picker.tpl'}
{include file='inc/js-includes/manage-expenditure-users.tpl'}
{include file='inc/header.tpl'}
{include file='inc/side-nav-my-accounts.tpl'}
<h2>
    <a href="{makeUrl url='my-accounts/'}">{getText id='My accounts'}</a>
     &raquo; <a href="{$currentAccount->getUrlDetail()}">{$currentAccount->getName()|htmlProtect}</a>
     &raquo; <a href="{$expenditure->getUrlView()}">{$expenditure->getTitle()|htmlProtect}</a>
</h2>
{include file='inc/main.tpl'}
    {include file='my-accounts/edit-expenditure-form.tpl'}
{include file='inc/footer.tpl'}
