<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Description of IndexController
 *
 * @author Bitmarshals Digital <sanicms@bitmarshals.co.ke>
 */
class IndexController extends AbstractActionController
{

    /**
     * Check if user is logged in or redirect to login. If loged in redirect the
     * user to the default dashboard for his/her most priviledged role
     * 
     * @return \Zend\Http\Response
     */
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        } else {
            //@todo get user role dynamically
            $role = 'branch_admin';
            // switch starting with the most priviledged role
            switch ($role) {
                case '':
                    break;
                case 'sani_admin':
                    break;
                case 'branch_admin':
                    // present the branch dashboard (basically /b/)
                    $this->redirect()->toRoute('branch/dashboard');
                    break;
                default:
                    // present the member dashboard (basically /a/)
                    // must be account/dashboard else will redirect improperly
                    $this->redirect()->toRoute('account/dashboard');
                    break;
            }
        }
    }

}
