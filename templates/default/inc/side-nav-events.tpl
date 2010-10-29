<div id="sidebar">
    <ul class="sideNav">
        {foreach from=$events item='event'}
            {if isset($currentEvent) and $currentEvent->getId() == $event->getId()}
                <li class="active">
                    <a href="{$event->getUrlDetail()}">{$event->getName()}</a>
                    <ul class="subNav">
                        <li><a href="" class="new">{getText id="New expenditure"}</a></li>
                        <li><a href="" class="new">{getText id="New repayment"}</a></li>
                        <li><a href="" class="cash-up">{getText id="Cash up"}</a></li>
                    </ul>
                </li>
            {else}
                <li><a href="{$event->getUrlDetail()}">{$event->getName()}</a></li>
            {/if}
        {/foreach}
        <li {if 'events/create-new-event.html'|isCurrentPage}class="active"{/if}><a href="{makeUrl url='events/create-new-event.html'}" class="new">{getText id='New event'}</a></li>
    </ul>
<!-- // .sideNav -->
</div>    
<!-- // #sidebar -->
