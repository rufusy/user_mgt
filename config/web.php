<?php

use yii\symfonymailer\Mailer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@views' => '@app/views',
        '@userRolesUploadUrl' => '@app/uploads/user_roles/',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'larrXSm0Y_VQ8PcmuTinKBOdgXWGo5OB',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession'	=> true,
            'authTimeout' => 3600
        ],
        'authManager' => [
            'class' => 'app\components\DbManager',
            // uncomment if you want to cache RBAC items hierarchy
            // 'cache' => 'cache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            /**
             * https://github.com/symfony/symfony-docs/issues/17115
             */
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => false,
            'transport' => [
//                'dsn' => 'smtp://d38acd23973124:4badb45ed6fd76@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login',
//                'dsn' => 'gmail://smisadmin@uonbi.ac.ke:lziunystxuhwunjh@default',
                'dsn' => 'gmail://ndukenyadev@uonbi.ac.ke:jbycuzbmswtoahpg@default'
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'home' => '/site/index',
                'login' => '/site/login',
                'logout' => '/site/logout',
            ],
        ],
        'formatter' => [
            'defaultTimeZone' => 'Africa/Nairobi',
            'dateFormat' => 'd-M-Y',
            'datetimeFormat' => 'd-M-Y H:i:s'
        ],
        'assetManager' => [
            /**
             * Yii loads assets from locally installed directories.
             * To try and improve on performance, we want to load these assets from CDNs where possible.
             */
            'appendTimestamp' => true,
            'forceCopy' => YII_DEBUG,
//            'linkAssets' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js',
                    ]
                ],
                'yii\jui\JuiAsset' => [
                    'css' => [
                        'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css'
                    ],
                    'js' => [
                        'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'
                    ]
                ],
                'yii\bootstrap5\BootstrapAsset' => [
                    'css' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap5\BootstrapPluginAsset' => [
                    'js' => [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js'
                    ],
                    'depends' => [
                        'yii\jui\JuiAsset',
                    ]
                ],
                /**
                 * Yii comes with some js assets under vendor/yiisoft/yii2/assets
                 * To improve on performance, we combine and minify these files
                 */
                'yii\web\YiiAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ],
                'yii\widgets\ActiveFormAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ],
                'yii\validators\ValidationAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ],
                'yii\grid\GridViewAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ],
                ' yii\captcha\CaptchaAsset' => [
                    'css' => [], 'js' => [], 'depends' => ['app\assets\AllYiiAssets']
                ]
            ],
        ]
    ],
    'params' => $params,
    'modules' => [
        'gridview' => ['class' => 'kartik\grid\Module'],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
