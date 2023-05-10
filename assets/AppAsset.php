<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        /**
         * Third party css
         */
        // Google Font: Source Sans Pro
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
        //aos animate
        'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css',
        // iCheck
        'https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css',
        // Datetimepicker
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css',
        // Daterange picker
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css',
        // summernote
        'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css',
        // Toastr
        'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css',
        // Select2
        'adminlte/plugins/select2/css/select2.min.css',
        'adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',

        /**
         * Application css
         * Load these at the very bottom of this list
         */
        // Theme style
        'https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css',

        'css/site.css',
    ];

    public $js = [
        /**
         * Third party js
         */
        // Moment
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js',
        // Date range picker
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js',
        // Input mask
        'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js',
        //Summernote
        'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js',
        // Axios
        'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
        // Jquery validation
        'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js',
        'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js',
        // Datetimepicker
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js',
        // Aos
        'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js',
        // Toastr
        'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
        // Select 2
        'adminlte/plugins/select2/js/select2.full.min.js',

        /**
         * Application js
         * Load these at the very bottom of this list
         */
        // AdminLTE App
        'https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js',

        'js/site.js',
        'js/jquery_validation.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap5\BootstrapPluginAsset'
    ];
}
