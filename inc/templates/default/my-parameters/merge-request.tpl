{include file='inc/header-html.tpl' title='Dashboard'}
<!-- Additionnal javascript script -->
{include file='inc/header.tpl'}
{include file='inc/side-nav-my-parameters.tpl'}
<h2><a href="{makeUrl url='my-parameters/'}">{getText id='My parameters'}</a> &raquo; <a href="{makeUrl url='my-parameters/merge-requests.html'}">{getText id='Merge requests'}</a></h2>
{include file='inc/main.tpl'}

					<h3>{getText id='Merge request of users %1$s and %2$s' arg1=$mergeRequest->getFirstUser()->getName() arg2=$mergeRequest->getSecondUser()->getName()}</h3>
                    {if isset($mergeAuthorizationException)}
                        <p>{getText id="We just sent e-mail to the concerned persons, please wait for their agreements."}</p>
                    {/if}
{include file='inc/footer.tpl'}
