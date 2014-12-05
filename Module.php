<?php

namespace Helper;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\EventManager\EventInterface;
use Helper\View\Helper\UserName;

class Module implements ViewHelperProviderInterface,
        ServiceProviderInterface,
        ControllerPluginProviderInterface,
        BootstrapListenerInterface,
        ControllerProviderInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(EventInterface $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $sharedEventManager = $e->getApplication()->getEventManager()->getSharedManager();
        // automatically setting a layout file based on a config file
        // and the requested module
        $sharedEventManager->attach('Zend\Mvc\Controller\AbstractController',
                'dispatch',
                function($e) {
            $controller = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0,
                    strpos($controllerClass, '\\'));
            $config = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);
        /**
         * Attach ZfcUser Module listeners
         */
        $sharedEventManager->attach('ZfcUser\Authentication\Adapter\AdapterChain',
                'authenticate.success',
                array($sm->get('HelperZfcUserListener'), 'onAuthenticateSuccess'));
        $sharedEventManager->attach('ZfcUser\Authentication\Adapter\AdapterChain',
                'authenticate.fail',
                array($sm->get('HelperZfcUserListener'), 'onAuthenticateFail'));
        $sharedEventManager->attach('ZfcUser\Form\Login', 'init',
                array($sm->get('HelperZfcUserListener'), 'onLoginFormInit'));
        $sharedEventManager->attach('ZfcUser\Form\LoginFilter', 'init',
                array($sm->get('HelperZfcUserListener'), 'onLoginFilterInit'));
        /**
         * Attach Goalio Module listeners
         */
        $sharedEventManager->attach('GoalioForgotPassword\Service\Password',
                'resetPassword',
                array($sm->get('HelperGoalioForgotPasswordListener'), 'onResetPassword'));

        // get the view helper manager from the application service manager
        $viewHelperManager = $sm->get('viewHelperManager');
        // get the headTitle helper from the view helper manager
        $headTitle = $viewHelperManager->get('headTitle');
        $headTitle->setSeparator(' - ');
        $headTitle->append('SaniCMS');
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'formElementerrors' => '\Helper\Form\View\Helper\FormElementErrors',
                'flashMessenger' => '\Helper\View\Helper\FlashMessenger',
                'anchor' => '\Helper\View\Helper\Anchor',
            ),
            'factories' => array(
                'UserName' => function($sm) {
                    $firstNameViewHelper = new UserName($sm->getServiceLocator()->get('ApplicationUserMapper'));
                    return $firstNameViewHelper;
                },
                'hashid' => '\Helper\View\Helper\HashIdFactory',
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'PageService' => 'Helper\Service\PageFactory',
                'zfcuser_user_mapper' => function ($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $mapper = new \Helper\ZfcUser\Mapper\User();
                    $mapper->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
                    $entityClass = $options->getUserEntityClass();
                    $mapper->setEntityPrototype(new $entityClass);
                    $mapper->setHydrator(new \Helper\ZfcUser\Mapper\UserHydrator());
                    $mapper->setTableName($options->getTableName());
                    return $mapper;
                },
            ),
        );
    }

    public function getControllerPluginConfig()
    {
        return array(
            'invokables' => array(
                'anchor' => '\Helper\Controller\Plugin\Anchor',
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(),
        );
    }

}
