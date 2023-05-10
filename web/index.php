<?php

use yii\helpers\VarDumper;

require __DIR__ . '/../config/constants.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

function dd($v): void
{
    if(YII_ENV_DEV) {
        VarDumper::dump($v, 10, true);
        exit();
    }
}

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
