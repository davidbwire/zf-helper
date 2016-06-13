<?php

return array(
    'controllers' => array(
        'invokables' => array(),
        'factories' => [
            'HelperGoalioIndexController' => '\Helper\Goalio\Controller\IndexControllerFactory'
        ]
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'zfcuser',
                        'action' => 'login',
                    ),
                ),
            ),
            'sign_in' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/sign-in',
                    'defaults' => array(
                        'controller' => 'zfcuser',
                        'action' => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'zfcuser',
                        'action' => 'logout',
                    ),
                ),
            ),
            'sign_out' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/sign-out',
                    'defaults' => array(
                        'controller' => 'zfcuser',
                        'action' => 'logout',
                    ),
                ),
            ),
            /**
             * Aliases for goalio password recovery
             */
            'forgot_password' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/forgot-password',
                    'defaults' => array(
                        'controller' => 'goalioforgotpassword_forgot',
                        'action' => 'forgot',
                    ),
                ),
            ),
            'reset_password' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/reset-password/:hashed_user_id/:token',
                    'defaults' => array(
                        // route via HelperGoalioIndexController to decode
                        'controller' => 'HelperGoalioIndexController',
                        'action' => 'resetPassword',
                    ),
                    'constraints' => array(
                        'hashed_user_id' => '[A-Za-z0-9]+',
                        'token' => '[A-F0-9]+',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'helper' => __DIR__ . "/../view",
            'zfcuser' => __DIR__ . '/../view',
            'bjyauthorize' => __DIR__ . '/../view',
            'goalioforgotpassword' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'zfc-user/user/login' => __DIR__ . '/../view/zfc-user/user/login.phtml',
        ),
    ),
    'service_manager' => [
        'invokables' => [
            'LoggerService' => '\Helper\Service\Logger',
            'SmsService' => '\Helper\Service\Sms'
        ],
        'factories' => [
            'DefaultNavigation' => '\Zend\Navigation\Service\DefaultNavigationFactory',
            'HelperUserHasRoleMapper' => '\Helper\Mapper\Junction\UserHasRoleMapperFactory',
            'HelperHistoryLoginMapper' => '\Helper\Mapper\HistoryLoginMapperFactory',
            'HelperHistoryFailedLoginMapper' => '\Helper\Mapper\HistoryFailedLoginMapperFactory',
            'HelperRoleMapper' => '\Helper\Mapper\RoleMapperFactory',
            'HelperPageMapper' => '\Helper\Mapper\PageMapperFactory',
            'HelperZfcUserListener' => '\Member\EventManager\ZfcUserListenerFactory',
            'HelperGoalioForgotPasswordListener' => '\Helper\Goalio\EventManager\GoalioForgotPasswordListenerFactory'
        ],
    ],
    'controller_plugins' => [
        'aliased' => [
            'logger' => 'log'
        ],
        'factories' => [
            'log' => '\Helper\Controller\Plugin\LoggerFactory',
            'hashid' => '\Helper\Controller\Plugin\HashIdFactory',
            'AssignRole' => '\Helper\Controller\Plugin\AssignRoleFactory',
        ],
    ],
);
