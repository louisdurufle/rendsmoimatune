{include file='inc/header-html.tpl' title='Sign in'}
<!-- Additionnal javascript script -->
{include file='inc/header.tpl'}
{include file='inc/side-nav-sign-in.tpl'}
<h2><a href="{makeUrl url='sign-in.html'}">{getText id='Sing-in'}</a></h2>
{include file='inc/main.tpl'}
{include file='inc/message.tpl'}
<h3>{getText id='Sign in'}</h3>
{include file='inc/basic-authentication-form.tpl'}
{include file='inc/footer.tpl'}