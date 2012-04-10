{include file='inc/header-html.tpl'}
<!-- Additionnal javascript script -->
{include file='inc/js-includes/date-picker.tpl'}
{include file='inc/js-includes/manage-repayment-users.tpl'}
{include file='inc/header.tpl'}
{include file='inc/side-nav-my-accounts.tpl'}
<h2>
    <a href="{makeUrl url='my-accounts/'}">{getText id='My accounts'}</a>
     &raquo; <a href="{$currentAccount->getUrlDetail()}">{$currentAccount->getName()|htmlProtect}</a>
     &raquo; <a href="{$repayment->getUrlView()}">{$repayment->getDescription()|htmlProtect}</a>
</h2>
{include file='inc/main.tpl'}
<form action="{$repayment->getUrlEdit()}" method="post" class="jNice">
    <fieldset>
        <p class="inline-label"><label class="medium-inline">{getText id='Payer\'s name'}</label><label class="medium-inline">{getText id='Beneficiary\'s name'}</label></p>
        <p>
        <input type="hidden" name="payer-id" value="{if isset($_POST.{'payer-id'})}{$_POST.{'payer-id'}|htmlProtect}{else}{$repayment->getPayer()->getId()|htmlProtect}{/if}" />
            <input type="text" class="text-medium payer-name{if isset($userInputException) && $userInputException->getInputName() == 'payer-name'} user-input-exception{/if}" rel="{$currentAccount->getUrlAutocompleteUser()}" name="payer-name" value="{if isset($_POST.{'payer-name'})}{$_POST.{'payer-name'}|htmlProtect}{else}{$repayment->getPayer()->getName()|htmlProtect}{/if}" />
            <span class="inter-input">{getText id='gives to'}</span>
            <input type="hidden" name="beneficiary-id" value="{if isset($_POST.{'beneficiary-id'})}{$_POST.{'beneficiary-id'}|htmlProtect}{else}{$repayment->getBeneficiary()->getId()|htmlProtect}{/if}" />
            <input type="text" class="text-medium beneficiary-name{if isset($userInputException) && $userInputException->getInputName() == 'beneficiary-name'} user-input-exception{/if}" rel="{$currentAccount->getUrlAutocompleteUser()}" name="beneficiary-name" value="{if isset($_POST.{'beneficiary-name'})}{$_POST.{'beneficiary-name'}|htmlProtect}{else}{$repayment->getBeneficiary()->getName()|htmlProtect}{/if}" />
        </p>
        <p><label>{getText id='Amount'} :</label><input type="text" id="expenditure-amount" class="text-medium{if isset($userInputException) && $userInputException->getInputName() == 'amount'} user-input-exception{/if}" name="amount" value="{if isset($_POST.amount)}{$_POST.amount|htmlProtect}{else}{$repayment->getAmount()|formatMoneyInput}{/if}" /><span class="inter-input">€</span></p>
        <p><label>{getText id='Date'} :</label><input type="text" class="text-medium date" name="date" value="{if isset($_POST.date)}{$_POST.date|htmlProtect}{else}{$repayment->getDate()->format('d-m-Y')}{/if}" /></p>
        <input type="submit" name="edit-repayment" value="{getText id='Save'}" />
        <input type="reset" name="reset-repayment" value="{getText id='Reset'}" />
    </fieldset>
</form>
{include file='inc/footer.tpl'}
