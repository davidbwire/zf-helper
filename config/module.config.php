<?php

return array(
    'controllers' => array(
        'invokables' => array(),
        'factories' => [
            'helperGoalioIndexController' => '\Helper\Goalio\Controller\IndexControllerFactory'
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
            'request_password_reset' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/forgot-password',
                    'defaults' => array(
                        'controller' => 'HelperGoalioIndexController',
                        'action' => 'requestResetPasswordLink',
                    ),
                ),
            ),
            'reset_password' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/reset-password/:user_id/:token',
                    'defaults' => array(
                        // route via HelperGoalioIndexController to decode
                        'controller' => 'HelperGoalioIndexController',
                        'action' => 'resetPassword',
                    )
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
            'goalio-forgot-password/forgot/forgot' => __DIR__ . '/../view/helper/simple-mailer/request_password_reset.phtml',
            'goalio-forgot-password/forgot/reset' => __DIR__ . '/../view/helper/simple-mailer/reset_password.phtml',
            'goalio-forgot-password/forgot/passwordchanged' => __DIR__ . '/../view/helper/simple-mailer/password_changed.phtml',
        ),
    ),
    'service_manager' => [
        'invokables' => [
            'loggerService' => '\Helper\Service\Logger',
            'smsService' => '\Helper\Service\Sms',
        ],
        'aliases' => [
            'logger' => 'loggerService',
            'simpleMailerService' => 'simpleMailer',
        ],
        'shared' => [
            'simpleMailer' => false,
        ],
        'factories' => [
            'applicationUserMapper' => \NtMapper\Mapper\UserMapperFactory::class,
            'fileUploadService' => '\Helper\Service\FileUploadServiceFactory',
            'simpleMailer' => '\Helper\Service\SimpleMailerFactory',
            'defaultNavigation' => '\Zend\Navigation\Service\DefaultNavigationFactory',
            'helperUserHasRoleMapper' => '\Helper\Mapper\Junction\UserHasRoleMapperFactory',
            'helperHistoryLoginMapper' => '\Helper\Mapper\HistoryLoginMapperFactory',
            'helperHistoryFailedLoginMapper' => '\Helper\Mapper\HistoryFailedLoginMapperFactory',
            'helperRoleMapper' => '\Helper\Mapper\RoleMapperFactory',
            'helperArticleMapper' => '\Helper\Mapper\ArticleMapperFactory',
            'helperZfcUserListener' => '\Helper\ZfcUser\EventManager\ZfcUserListenerFactory',
            'helperGoalioForgotPasswordListener' => '\Helper\Goalio\EventManager\GoalioForgotPasswordListenerFactory'
        ],
    ],
    'controller_plugins' => [
        'aliases' => [
            'logger' => 'log'
        ],
        'factories' => [
            'log' => '\Helper\Controller\Plugin\LoggerFactory',
            'hashid' => '\Helper\Controller\Plugin\HashIdFactory',
            'assignRole' => '\Helper\Controller\Plugin\AssignRoleFactory',
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'renderImage' => \Helper\View\Helper\RenderImage::class,
        ],
        'factories' => [
            \Helper\View\Helper\RenderImage::class => Helper\View\Helper\RenderImageFactory::class
        ]
    ]
);
