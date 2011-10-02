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
 * @author   Paul Fariello <paul.fariello@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html  GPL License 3.0
 * @version  SVN: 145
 * @link     http://www.Rendsmoimatune.fr
 */

namespace Eu\Rmmt\Exception;

use Bdf\Utils;
use Eu\Rmmt\User;
use Eu\Rmmt\MergeRequest;

/**
 * MergeAuthorizationException
 *
 * @category Class
 * @package  Eu\Rmmt\Exception
 * @author   Paul Fariello <paul.fariello@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html  GPL License 3.0
 * @link     http://www.rendsmoimatune.eu
 */
class MergeAuthorizationException extends \Exception {
    private $_mergeRequest;
    private $_requiredAgreement = array();

    /**
     * Constructeur
     *
     * @return MergeAuthorizationException
     */
    public function __construct(MergeRequest $mergeRequest) {
      $this->_mergeRequest = $mergeRequest;
      parent::__construct(Utils::getText('You can\'t merge user %1$s with user %2$s on your own.', $this->_mergeRequest->getFirstUser()->getName(), $this->_mergeRequest->getSecondUser()->getName()));
    }
    
    public function addRequiredAgreement(User $user)
    {
        $this->_requiredAgreement[] = $user;
        if (sizeof($this->_requiredAgreement) == 1) {
            $this->message = Utils::getText(
                'You can\'t merge user %1$s with user %2$s on your own. You need the agreement of %3$s. For this purpose an email has been sent to %4$s.',
                $this->_mergeRequest->getFirstUser()->getName(),
                $this->_mergeRequest->getSecondUser()->getName(),
                $this->_requiredAgreement[0]->isRegistered()?$this->_requiredAgreement[0]->getName():$this->_requiredAgreement[0]->getCreator()->getName(),
                $this->_requiredAgreement[0]->isRegistered()?$this->_requiredAgreement[0]->getEmail():$this->_requiredAgreement[0]->getCreator()->getEmail()
            );
        } elseif (sizeof($this->_requiredAgreement) == 2) {
            $this->message = Utils::getText(
                'You can\'t merge user %1$s with user %2$s on your own. You need the agreement of %3$s and %4$s. For this purpose email have been sent respectively to %5$s and %6$s.',
                $this->_mergeRequest->getFirstUser()->getName(),
                $this->_mergeRequest->getSecondUser()->getName(),
                $this->_requiredAgreement[0]->isRegistered()?$this->_requiredAgreement[0]->getName():$this->_requiredAgreement[0]->getCreator()->getName(),
                $this->_requiredAgreement[1]->isRegistered()?$this->_requiredAgreement[1]->getName():$this->_requiredAgreement[1]->getCreator()->getName(),
                $this->_requiredAgreement[0]->isRegistered()?$this->_requiredAgreement[0]->getEmail():$this->_requiredAgreement[0]->getCreator()->getEmail(),
                $this->_requiredAgreement[1]->isRegistered()?$this->_requiredAgreement[1]->getEmail():$this->_requiredAgreement[1]->getCreator()->getEmail()
            );
        } else {
            // Should never happen
        }
    }

    public function getRequiredAgreement()
    {
        return $this->_requiredAgreement;
    }
}
