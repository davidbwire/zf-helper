<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Goalio\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of IndexController
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class IndexController extends AbstractActionController
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Decodes the user id and dispatches the actual controller for password
     * reset
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function resetPasswordAction()
    {
        // get the hashed_user_id
        $hashedUserId = $this->params()->fromRoute('hashed_user_id', null);
        //  normalize it to get the actual user_id
        $userId = array_shift($this->hashid()->decode($hashedUserId));
        // get the recovery token
        $token = $this->params()->fromRoute('token', null);
        // let goalio do it's work
        return $this->forward()->dispatch('goalioforgotpassword_forgot',
                        array('userId' => $userId, 'token' => $token));
    }

}
