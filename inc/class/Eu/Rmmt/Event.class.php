<?php
/**
 * Fichier de classe
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
 * @version  SVN: $revision$
 * @link
 */

namespace Eu\Rmmt;
use DateTime;
use Bdf\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Event
 *
 * @category Class
 * @package
 * @author   needle
 * @license  http://www.gnu.org/copyleft/gpl.html  GPL License 3.0
 * @link
 */
class Event
{
    private $_id;
    private $_name;
    private $_startDate;
    private $_endDate;
    private $_expenditures;
    private $_users;
    private $_repayments;

    public function  __construct($name) {
        $this->_name = $name;
        $this->_users = new ArrayCollection();
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getStartDate() {
        return $this->_startDate;
    }

    public function setStartDate(DateTime $startDate)
    {
        $this->_startDate = $startDate;
    }

    public function getEndDate() {
        return $this->_endDate;
    }

    public function setEndDate(DateTime $endDate)
    {
        $this->_endDate = $endDate;
    }

    public function getExpenditures() {
        return $this->_expenditures;
    }

    public function addExpenditure(Expenditure $expenditure)
    {
        $this->_expenditures->add($expenditure);
    }

    public function removeExpenditure(Expenditure $expenditure)
    {
        $this->_expenditures->removeElement($expenditure);
    }

    public function getUsers() {
        return $this->_users;
    }

    public function addUser(User $user) {
        $this->_users->add($user);
    }

    public function removeUser(User $user) {
        $this->_users->removeElement($user);
    }

    public function getRepayments() {
        return $this->_repayments;
    }

    public function addRepayments(Repayment $repayment) {
        $this->_repayments->add($repayment);
    }

    public function removeRepayments(Repayment $repayment) {
        $this->_repayments->removeElement($repayment);
    }

    public function getUrlDetail()
    {
        return Utils::makeUrl('events/'.Utils::urlize($this->_name).'-'.$this->_id.'/');
    }

    public function getUrlNewExpenditure()
    {
        return Utils::makeUrl('events/'.Utils::urlize($this->_name).'-'.$this->_id.'/create-new-expenditure.html');
    }

    public function getUrlNewRepayment()
    {
        return Utils::makeUrl('events/'.Utils::urlize($this->_name).'-'.$this->_id.'/create-new-repayment.html');
    }

    public function getUrlDeleteExpenditure(Expenditure $expenditure)
    {
        return Utils::makeUrl('events/'.Utils::urlize($this->_name).'-'.$this->_id.'/delete-'.Utils::urlize($expenditure->getTitle()).'-'.$expenditure->getId().'.html');
    }

    public function getUrlEditExpenditure(Expenditure $expenditure)
    {
        return Utils::makeUrl('events/'.Utils::urlize($this->_name).'-'.$this->_id.'/edit-'.Utils::urlize($expenditure->getTitle()).'-'.$expenditure->getId().'.html');
    }
}