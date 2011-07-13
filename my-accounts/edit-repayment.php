<?php
/**
 * Fichier de modification d'une dépense
 *
 * PHP version 5.3
 *
 * This file is part of Rendsmoimatune.
 *
 * Rendsmoimatune is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Rendsmoimatune is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Rendsmoimatune.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category ClassFile
 * @package  Rendsmoimatune
 * @author   needle
 * @license  http://www.gnu.org/copyleft/gpl.html  GPL License 3.0
 * @version  SVN: 145
 * @link     http://www.rendsmoimatune.eu
 */

require_once '../inc/init.php';
require_once '../inc/assignDefaultVar.php';

$em = \Bdf\Core::getInstance()->getEntityManager();
$te = \Bdf\Core::getInstance()->getTemplatesEngine();

$currentUser = \Eu\Rmmt\User::getCurrentUser();
if ($currentUser == null) {
    \Bdf\Session::getInstance()->add('redirect',$_SERVER['REQUEST_URI']);
    header('location: '.\Bdf\Utils::makeUrl('sign-in.html'));
    die();
}

if (isset($_GET['account-id']) and !empty($_GET['account-id'])) {
    $account = \Eu\Rmmt\Account::getRepository()->find($_GET['account-id']);
}

if (!isset($_GET['repayment-id']) or empty($_GET['account-id'])) {
    if (null !== $account) {
        header('location: '.$account->getUrlDetail());
    } else {
        header('location: '.\Bdf\Utils::makeUrl('my-accounts/'));
    }
    die();
} else {
    $repayment = \Eu\Rmmt\Repayment::getRepository()->find($_GET['repayment-id']);
    if (null === $repayment) {
        if (null !== $account) {
            header('location: '.$account->getUrlDetail());
        } else {
            header('location: '.\Bdf\Utils::makeUrl('my-accounts/'));
        }
        die();
    }
}

    
try {

    $repayment->checkEditRight($currentUser);

    if (!isset($_POST['edit-repayment'])) {
        $te->assign("currentAccount",$account);
        $te->assign('repayment', $repayment);
        $te->display('my-accounts/edit-repayment');
    } else {
        try {
            if (empty($_POST['payer-name'])) {
                throw new Eu\Rmmt\Exception\UserInputException(\Bdf\Utils::getText('Payer\'s name is required'), $_POST['payer-name'], 'payer-name');
            }
            $payer = Eu\Rmmt\User::findByIdOrName($_POST['payer-id'], $_POST['payer-name']);
            if (null == $payer) {
                // Create new user
                $payer = Eu\Rmmt\UserFactory::createUnregisteredUser($currentUser, $_POST['payer-name']);
                $em->persist($payer);
                $account->addUser($payer);
            }

            if (empty($_POST['beneficiary-name'])) {
                throw new Eu\Rmmt\Exception\UserInputException(\Bdf\Utils::getText('Beneficiary\'s name is required'), $_POST['beneficiary-name'], 'beneficiary-name');
            }
            $beneficiary = Eu\Rmmt\User::findByIdOrName($_POST['beneficiary-id'], $_POST['beneficiary-name']);
            if (null == $beneficiary) {
                // Create new user
                $beneficiary = Eu\Rmmt\UserFactory::createUnregisteredUser($currentUser, $_POST['beneficiary-name']);
                $em->persist($beneficiary);
                $account->addUser($beneficiary);
            }

            if (!isset($_POST['amount']) OR empty($_POST['amount'])) {
                throw new Eu\Rmmt\Exception\UserInputException(\Bdf\Utils::getText('Amount is required'), $_POST['amount'], 'amount');
            }
            $_POST['amount'] = strtr($_POST['amount'], ',', '.');

            $repayment->setPayer($payer);
            $repayment->setBeneficiary($beneficiary);
            $repayment->setAmount($_POST['amount']);

            $date = null;
            if (isset($_POST['date']) AND !empty($_POST['date'])) {
                $date = DateTime::createFromFormat('d-m-Y', $_POST['date']);

                $repayment->setDate($date);
            }

            $em->flush();
            $messages = array();
            $messages[] = array('type'=>'done','content'=>Bdf\Utils::getText('Repayment saved'));

            $usersString = "";
            foreach(Eu\Rmmt\UserFactory::getNewUsers() as $index => $user) {
                $usersString .= $user->getName();
                if ($index < sizeof(Eu\Rmmt\UserFactory::getNewUsers())-1) {
                   $usersString .= ', '; 
                }
            }

            if (!empty($usersString)) {
                $messages[] = array('type'=>'info','content'=>Bdf\Utils::nGetText('User %1$s has been created. <a href="%2$s">Invite him ?</a>', 'Users %1$s has been created. <a href="%2$s">Invite them ?</a>', sizeof(Eu\Rmmt\UserFactory::getNewUsers()), $usersString, Bdf\Utils::makeUrl('my-parameters/send-invitation.html')));
            }

            \Bdf\Session::getInstance()->add('messages',$messages);
            header('location: '.$account->getUrlDetail());
        } catch(Eu\Rmmt\Exception\UserInputException $e) {
            $te->assign('currentAccount',$account);
            $te->assign('repayment',$repayment);
            $te->assign('_POST',$_POST);
            $te->assign('messages', array(array('type'=>'error','content'=>$e->getMessage())));
            $te->assign('userInputException', $e);
            $te->display('my-accounts/edit-repayment');
        } catch(Exception $e) {
            $te->assign('currentAccount',$account);
            $te->assign('repayment',$repayment);
            $te->assign('_POST',$_POST);
            $te->assign('messages', array(array('type'=>'error','content'=>Bdf\Utils::getText('Internal error').' : '.$e->getMessage())));
            $te->display('my-accounts/edit-repayment');
        }
    }
} catch(Eu\Rmmt\Exception\RightException $e) {
    \Bdf\Session::getInstance()->add('messages', array(array('type'=>'error','content'=>$e->getMessage())));
    header('location: '.$repayment->getAccount()->getUrlDetail());
}

?>