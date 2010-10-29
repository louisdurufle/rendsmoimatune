<?php
/**
 * Fichier de création d'une nouvelle dépense
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

$em = \Bdf\Core::getInstance()->getEntityManager();
$te = \Bdf\Core::getInstance()->getTemplatesEngine();

$currentUser = \Eu\Rmmt\User::getCurrentUser();
if ($currentUser == null) {
    \Bdf\Session::getInstance()->add('redirect',$_SERVER['REQUEST_URI']);
    header('location: '.\Bdf\Utils::makeUrl('sign-in.html'));
    die();
}

if (!isset($_GET['event-id'])) {
    header('location: '.\Bdf\Utils::makeUrl('events/'));
    die();
} else {
    $event = $em->getRepository("Eu\Rmmt\Event")->find($_GET['event-id']);
    if ($event === null) {
        header('location: '.\Bdf\Utils::makeUrl('events/'));
        die();
    }
}

if (!isset($_POST['create-new-expenditure'])) {
    $te->assign("currentEvent",$event);
    $te->assign('events',$em->getRepository('Eu\Rmmt\Event')->findAll());
    $te->display('events/create-new-expenditure');
} else {

    try {
        if (!isset($_POST['name']) OR empty($_POST['name'])) {
            throw new Eu\Rmmt\Exception\UserInputException(\Bdf\Utils::getText('Name is required'), $_POST['name']);
        }

        if (!isset($_POST['amount']) OR empty($_POST['amount'])) {
            throw new Eu\Rmmt\Exception\UserInputException(\Bdf\Utils::getText('Amount is required'), $_POST['amount']);
        }

        $expenditure = new Eu\Rmmt\Expenditure($event, $_POST['name'], $_POST['amount']);

        $date = null;
        if (isset($_POST['date']) AND !empty($_POST['date'])) {
            $date = DateTime::createFromFormat('d-m-Y', $_POST['date']);

            $expenditure->setDate($date);
        }

        // Store new users here in order to propose invitations
        $newUsers       = array();

        // Payers
        $amountPayed    = 0;

        foreach( array_keys ($_POST['payersId']) as $index ) {
            $id     = $_POST['payersId'][$index];
            $name   = trim ($_POST['payersName'][$index]);
            $amount = (float) $_POST['payersAmount'][$index];
            $metric = $_POST['payersMetric'][$index];

            if (!empty($name)) {
                $unknown = true;
                $payer   = null;

                // Get user
                if (!empty($id) and ctype_digit ($id)) {
                    $user = Eu\Rmmt\User::getRepository()->find((int)$id);

                    // Check inconsistency between id and name
                    if (null !== $user and $user->getName() == $name) {
                        $unknown     = false;
                        $payer       = $user;
                    }
                }

                if ($unknown) {
                    // Create new user
                    $user = new Eu\Rmmt\User(uniqid().'@rendsmoimatune.eu');
                    if (substr_count($name, ' ') > 0) {
                        list($firstName, $lastName) = explode(' ', $name, 2);
                        $user->setFirstName($firstName);
                        $user->setLastName($lastName);
                    } else {
                        $user->setFirstName($name);
                    }
                    $user->setRegistered(false);
                    $payer       = $user;
                    $newUsers[]  = $payer;
                }

                // Create payer
                switch ($metric) {
                    case '%':
                        $amount = round($expenditure->getAmount() * $amount / 100, 2);
                        break;
                    case '€':
                        $amount = $amount;
                        break;
                }

                $expenditure->addPayer($payer, $amount);

                $amountPayed += $amount;
            }
        }

        if ($amountPayed != $expenditure->getAmount()) {
            throw new Eu\Rmmt\Exception\InvalidAmountPayedException($expenditure);
        }

        // Beneficiaries
        $beneficiaries = array();

        foreach( array_keys ($_POST['beneficiariesId']) as $index ) {
            $id   = $_POST['beneficiariesId'][$index];
            $name = trim ($_POST['beneficiariesName'][$index]);

            if (!empty ($name)) {
                $unknown     = true;
                $beneficiary = null;

                if (!empty ($id) and ctype_digit ($id)) {
                    $user = Eu\Rmmt\User::getRepository()->find((int)$id);

                    // Check inconsistency between id and name
                    if (null !== $user and $user->getName() == $name) {
                        $unknown     = false;
                        $beneficiary = $user;
                    }
                }

                if ($unknown) {
                    // Create new user
                    $user = new Eu\Rmmt\User(uniqid().'@rendsmoimatune.eu');
                    if (substr_count($name, ' ') > 0) {
                        list($firstName, $lastName) = explode(' ', $name, 2);
                        $user->setFirstName($firstName);
                        $user->setLastName($lastName);
                    } else {
                        $user->setFirstName($name);
                    }
                    $user->setRegistered(false);
                    $beneficiary = $user;
                    $newUsers[]  = $payer;
                }

                $beneficiaries[] = $beneficiary;
            }
        }

        // Calculate amount due per user
        $amountPerBeneficiary = $expenditure->getAmount() / count($beneficiaries);

        foreach($beneficiaries as $user) {
            $expenditure->addBeneficiary($user, $amountPerBeneficiary);
        }

        $em->persist($expenditure);
        $em->flush();
        header('location: '.$event->getUrlDetail());
    } catch(Eu\Rmmt\Exception\UserInputException $e) {
        $te->assign('currentEvent',$event);
        $te->assign('events',$em->getRepository('Eu\Rmmt\Event')->findAll());
        $te->assign('_POST',$_POST);
        $te->assign('message', array('type'=>'error','content'=>$e->getMessage()));
        $te->display('events/create-new-expenditure');
    } catch(Exception $e) {
        $te->assign('currentEvent',$event);
        $te->assign('events',$em->getRepository('Eu\Rmmt\Event')->findAll());
        $te->assign('_POST',$_POST);
        $te->assign('message', array('type'=>'error','content'=>$e->getMessage()));
        $te->display('events/create-new-expenditure');
    }
}

?>