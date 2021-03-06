<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Goalio\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Helper\Util\TokenGenerator;
use GoalioForgotPassword\Service\Password as PasswordService;
use ZfcUser\Service\User as UserService;
use GoalioForgotPassword\Mapper\Password as PasswordMapper;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use GoalioForgotPassword\Form\Forgot;
use Zend\View\Model\ViewModel;
use Helper\Service\SimpleMailer;
use Zend\Validator\Digits as DigitsValidator;
use Zend\I18n\Validator\PhoneNumber as PhoneNumberValidator;
use Zend\I18n\Validator\IsInt as IsIntValidator;
use Helper\Util\PasswordManager;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;

/**
 * Description of IndexController
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class IndexController extends AbstractActionController
{

    /**
     *
     * @var \ZfcUser\Service\User
     */
    protected $userService;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     *
     * @var \GoalioForgotPassword\Form\Forgot
     */
    protected $forgotForm;

    /**
     *
     * @var \GoalioForgotPassword\Mapper\Password
     */
    protected $passwordMapper;

    /**
     *
     * @var \GoalioForgotPassword\Service\Password
     */
    protected $passwordService;

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
        $userId = $this->params()->fromRoute('user_id', null);
        // get the recovery token
        $token = $this->params()->fromRoute('token', null);
        // let goalio do it's work
        return $this->forward()->dispatch('goalioforgotpassword_forgot',
                        array('action' => 'reset', 'userId' => $userId, 'token' => $token));
    }

    /**
     * Bypass the reset password action because it does not use proper token
     * generation technique
     * 
     * @return \Helper\Goalio\Controller\ViewModel
     */
    public function requestResetPasswordLinkAction()
    {
        

        $vm = new ViewModel();

        $goalioPasswordService = $this->getPasswordService();
        $goalioPasswordService->cleanExpiredForgotRequests();
        $sl = $this->serviceLocator;

        $request = $this->getRequest();
        $form = $this->getForgotForm();
        // remove the + sign
        $emailOrPhone = str_replace('+', '', $this->params()->fromPost('email'));
        $digitsValidator = new DigitsValidator();
        $isProbablyPhone = $digitsValidator->isValid($emailOrPhone);
        if ($isProbablyPhone) {
            $phonevalidator = new PhoneNumberValidator(['country' => 'KE',
                'allowed_types' => ['mobile'], 'allow_possible' => true]);
            $passWordManager = new PasswordManager($sl->get('DbAdapter'),
                    $sl->get('Logger'));

            $userMapper = $sl->get('ApplicationUserMapper');
            $userId = $userMapper->getUserIdByUsername($emailOrPhone);
            if ($userId && $phonevalidator->isValid((int) $emailOrPhone)) {
                $plainTextPassword = trim($passWordManager->generatePlainTextPassword(10));
                $updatePassResult = $passWordManager
                        ->updatePassword($userId, $plainTextPassword);
                if ($updatePassResult === true) {
                    $smsService = $sl->get('SmsService');
                    $smsService->send('254' . (int) $emailOrPhone,
                            'Your password has been reset. '
                            . 'Your new password is ' . $plainTextPassword);
                    $this->flashMessenger()->addSuccessMessage('Your password has been sent to you.');
                } else {
                    $this->flashMessenger()->addErrorMessage('Password reset failed.');
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Password reset failed.');
            }
            return $this->redirect()->toRoute('login');
        }

        if ($request->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $email = $form->get('email')->getValue();
                $userId = $this->findUserIdByEmail($email);
                //only send request when email is found
                if (!empty($userId)) {
                    //Invalidate all prior request for a new password
                    $this->getPasswordMapper()->cleanPriorForgotRequests($userId);
                    $logger = $sl->get('LoggerService');
                    $simpleMailer = $sl->get('SimpleMailer');
                    $tokenGenerator = new TokenGenerator($logger);
                    $now = new \DateTime('now');
                    $rTime = $now->format('Y-m-d H:i:s');

                    $aData = [];
                    $aData['user_id'] = $userId;
                    // generate token twice for greater security
                    $aData['request_key'] = $tokenGenerator->generateToken() .
                            $tokenGenerator->generateToken();
                    $aData['request_time'] = $rTime;
                    $aData['create_time'] = time();
                    $this->getPasswordMapper()->persist($aData,
                            'user_password_reset');

                    // create a viewmodel and make sure it has a template
                    $viewModelEmail = $vm;
                    $viewModelEmail->setTemplate(
                            'helper/simple-mailer/email_template_reset_password');
                    $viewModelEmail->requestKey = $aData['request_key'];
                    $viewModelEmail->userId = $userId;
                    // retreive the html
                    $attachments = $simpleMailer
                            ->attachHtmlFromViewModel($viewModelEmail);

                    // add attachements and send
                    $simpleMailer->send($simpleMailer->generateEmailMessage($email,
                                    'Password Recovery', $attachments));
                    $vm = new ViewModel(array('email' => $email));
                } else {
                    $vm = new ViewModel(array('email' => $email, 'error' => 1));
                }
                // tell user that the password has been sent
                $vm->setTemplate('helper/simple-mailer/password_sent');
                return $vm;
            }
        }
        $vm->forgotForm = $form;
        $vm->setTemplate('helper/simple-mailer/request_password_reset');
        return $vm;
    }

    /**
     * Retreive Goalio password service
     *
     * @return \GoalioForgotPassword\Service\Password
     */
    public function getPasswordService()
    {
        if (!$this->passwordService) {
            $this->passwordService = $this->serviceLocator
                    ->get('goalioforgotpassword_password_service');
        }
        return $this->passwordService;
    }

    /**
     *
     * @return \GoalioForgotPassword\Form\Forgot
     */
    public function getForgotForm()
    {
        if (!$this->forgotForm) {
            $this->setForgotForm($this->serviceLocator
                            ->get('goalioforgotpassword_forgot_form'));
        }
        return $this->forgotForm;
    }

    /**
     *
     * @param Forgot $forgotForm
     */
    public function setForgotForm(Forgot $forgotForm)
    {
        $this->forgotForm = $forgotForm;
    }

    /**
     *
     * @param PasswordMapper $passwordMapper
     * @return \Helper\Goalio\Controller\IndexController
     */
    public function setPasswordMapper(PasswordMapper $passwordMapper)
    {
        $this->passwordMapper = $passwordMapper;
        return $this;
    }

    /**
     *
     * @return PasswordMapper
     */
    public function getPasswordMapper()
    {
        if (null === $this->passwordMapper) {
            $this->setPasswordMapper($this->serviceLocator
                            ->get('goalioforgotpassword_password_mapper'));
        }

        return $this->passwordMapper;
    }

    public function findUserIdByEmail($email)
    {
        $sql = new Sql($this->serviceLocator->get('dbAdapter'));
        $select = $sql->select('user')
                ->columns(['id'])
                ->where(array('email' => $email));
        $result = $sql->prepareStatementForSqlObject($select)
                ->execute();
        if ($result->count()) {
            return $result->current()['id'];
        }
        return null;
    }

}
