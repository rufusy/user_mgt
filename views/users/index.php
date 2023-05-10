<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 4/26/2023
 * @time: 7:11 PM
 */

/**
 * @var yii\web\View $this
 * @var string $title
 * @var yii\data\ActiveDataProvider $usersDataProvider
 * @var app\models\search\UsersSearch $usersSearchModel
 */

use app\helpers\SmisHelper;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

$this->title = $title;
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="page-header">
        <h1>Manage users</h1>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?php
                $usernameCol = [
                    'attribute' => 'username',
                    'label' => 'USERNAME',
                    'vAlign' => 'middle',
                ];
                $surnameCol = [
                    'attribute' => 'surname',
                    'label' => 'SURNAME',
                    'vAlign' => 'middle',
                    'value' => function($model){
                        return $model['employee']['surname'];
                    }
                ];
                $otherNamesCol = [
                    'attribute' => 'other_names',
                    'label' => 'OTHER NAMES',
                    'vAlign' => 'middle',
                    'value' => function($model){
                        return $model['employee']['other_names'];
                    }
                ];
                $titleCol = [
                    'attribute' => 'title',
                    'label' => 'TITLE',
                    'vAlign' => 'middle',
                    'value' => function($model){
                        return $model['employee']['title'];
                    }
                ];
                $phoneNumberCol = [
                    'attribute' => 'phone_number',
                    'label' => 'PHONE NO.',
                    'vAlign' => 'middle',
                    'value' => function($model){
                        return $model['employee']['phone_number'];
                    }
                ];
                $deptCol = [
                    'attribute' => 'dept_code',
                    'label' => 'DEPT CODE',
                    'vAlign' => 'middle',
                    'value' => function($model){
                        return $model['employee']['dept_code'];
                    }
                ];
                $emailCol = [
                    'attribute' => 'email',
                    'label' => 'EMAIL',
                    'vAlign' => 'middle',
                    'value' => function($model){
                        return $model['employee']['email'];
                    }
                ];
                $createdAtCol = [
                    'attribute' => 'created_at',
                    'label' => 'CREATED AT',
                    'vAlign' => 'middle',
                    'value' => function($model){
                        return SmisHelper::formatDate($model['created_at'], 'd-m-Y');
                    }
                ];
                $lastLoginCol = [
                    'attribute' => 'last_login_at',
                    'label' => 'LAST LOGIN AT',
                    'vAlign' => 'middle',
                    'value' => function($model){
                        if(empty($model['last_login_at'])){
                            return '--';
                        }
                        return SmisHelper::formatDate($model['last_login_at'], 'd-m-Y h:m');
                    }
                ];
                $actionsCol = [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{edit} | {roles}',
                    'contentOptions' => [
                        'style'=>'white-space:nowrap;',
                        'class'=>'kartik-sheet-style kv-align-middle'
                    ],
                    'buttons' => [
                        'edit' => function($model){
                            return Html::a('<i class="fa fa-edit" aria-hidden="true">&nbsp;</i>edit',
                                Url::to(['/users']),
                                [
                                    'title' => 'Edit user',
                                    'class' => 'btn-link action-text-info'
                                ]);
                        },
                        'roles' => function($url, $model){
                            return Html::a('<i class="fa fa-edit" aria-hidden="true">&nbsp;</i>roles',
                                Url::to([
                                    '/users-roles/create',
                                    'username' => $model['username']
                                ]),
                                [
                                    'title' => 'Manage user roles',
                                    'class' => 'btn-link action-text-info'
                                ]);
                        }
                    ]
                ];

                $gridColumns = [
                    ['class' => 'kartik\grid\SerialColumn'],
                    $titleCol,
                    $surnameCol,
                    $otherNamesCol,
                    $usernameCol,
                    $phoneNumberCol,
                    $emailCol,
                    $deptCol,
                    $createdAtCol,
                    $lastLoginCol,
                    $actionsCol
                ];

                $toolbar = [
                    [
                        'content' =>
                            Html::a('<i class="fas fa-plus""></i> Create user',
                                Url::to(['/users/create']),
                                [
                                    'title' => 'Create a new user',
                                    'class' => 'btn btn-success btn-spacer'
                                ]
                            ). '&nbsp' .
                            Html::a('<i class="fas fa-download""></i> User role template',
                                Url::to(['/users-roles/download-user-role-template']),
                                [
                                    'title' => 'Download user roles template',
                                    'class' => 'btn btn-success btn-spacer',
                                    'target' => '_blank',
                                    'data-pjax' => '0'
                                ]
                            ). '&nbsp' .
                            Html::button('<i class="fas fa-check""></i> Bulk assign roles', [
                                'title' => 'user roles to users in an excel file',
                                'id' => 'bulk-assign-roles-btn',
                                'class' => 'btn btn-success btn-spacer btn-sm',
                            ]) . '&nbsp' .
                            Html::button('<i class="fas fa-x""></i> Bulk remove roles', [
                                'title' => 'Remove roles from users in an excel file',
                                'id' => 'bulk-remove-roles-btn',
                                'class' => 'btn btn-danger btn-spacer btn-sm',
                            ]),
                        'options' => ['class' => 'btn-group mr-2']
                    ],
                    '{export}',
                    '{toggleData}',
                ];

                try{
                    echo GridView::widget([
                        'id' => 'users-grid',
                        'dataProvider' => $usersDataProvider,
                        'columns' => $gridColumns,
                        'headerRowOptions' => ['class' => 'kartik-sheet-style grid-header'],
                        'filterRowOptions' => ['class' => 'kartik-sheet-style grid-header'],
                        'pjax' => true,
                        'responsiveWrap' => false,
                        'condensed' => true,
                        'hover' => true,
                        'striped' => false,
                        'bordered' => false,
                        'toolbar' => $toolbar,
                        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
                        'export' => [
                            'fontAwesome' => true,
                            'label' => 'Export users'
                        ],
                        'panel' => [
                            'heading' => 'Users'
                        ],
                        'persistResize' => false,
                        'itemLabelSingle' => 'user',
                        'itemLabelPlural' => 'users',
                    ]);
                } catch (Throwable $ex) {
                    $message = $ex->getMessage();
                    if(YII_ENV_DEV) {
                        $message = $ex->getMessage() . ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
                    }
                    throw new ServerErrorHttpException($message, 500);
                }
                ?>
            </div>
        </div>
    </div>
</section>

<!-- This form is used to upload an Excel file for both assignment and removal of user roles-->
<div id="user-roles-modal" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="user-roles-modal-title" class="modal-title">
                    Bulk user roles
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ol>
                    <li>Download user role Excel template from this page.</li>
                    <li>Save the file as Excel workbook (.xlsx extension) then upload.</li>
                </ol>
                <form id="user-roles-form" method="post" action="#" onsubmit="return false;" enctype="multipart/form-data">
                    <div class="loader"></div>
                    <div class="error-display alert text-center" role="alert"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="user-roles" class="required-control-label">File</label>
                                <input type="file" class="form-control" id="user-roles" name="user-roles" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" id="user-roles-submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * A single form is used to upload file for user roles assignment and removal.
 * The only difference is the url where the form submits for each upload.
 */

$saveFromExcelUrl = Url::to(['/users-roles/save-from-excel']);
$deleteFromExcelUrl = Url::to(['/users-roles/delete-from-excel']);

$usersJs = <<< JS
let uploadUrl = '';
const userRolesForm = $('#user-roles-form');
const userRolesLoader = $('#user-roles-form > .loader');
userRolesLoader.html(loader);
userRolesLoader.hide();
const userRolesErrorDisplay =  $('#user-roles-form > .error-display');
userRolesErrorDisplay.hide();
userRolesForm.validate({
    rules: {
        'user-roles': {
            required: true
        }
    }
});

$('#users-grid-pjax').on('click', '#bulk-assign-roles-btn', function (e){
    e.preventDefault();
    showUserRolesModal('Bulk assign user roles');
    uploadUrl = '$saveFromExcelUrl';
});

$('#users-grid-pjax').on('click', '#bulk-remove-roles-btn', function (e){
    e.preventDefault();
    showUserRolesModal('Bulk remove user roles');
    uploadUrl = '$deleteFromExcelUrl';
});

function showUserRolesModal(title){
    userRolesLoader.hide();
    userRolesErrorDisplay.hide();
    userRolesForm[0].reset();
    $('#user-roles-modal-title').html(title);
    $('#user-roles-modal').modal('show');
}

$('#user-roles-submit').click(function (e){
    e.preventDefault();
    submitUsersRolesFile();
});

function submitUsersRolesFile(){
    if(userRolesForm.valid()){
        if(confirm('Upload file?')){
            userRolesErrorDisplay.hide();
            userRolesLoader.show();
            let formData = new FormData(userRolesForm[0]);
            $.ajax({
                url: uploadUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false     
            }).done(function (data){
                userRolesLoader.hide();
                if(!data.success){
                    userRolesErrorDisplay.html(data.message) 
                    userRolesErrorDisplay.show();
                }
            }).fail(function (data){
                userRolesLoader.hide();
                userRolesErrorDisplay.html(data.responseText) 
                userRolesErrorDisplay.show();
            });
        }
    }else{
        userRolesLoader.hide();
        userRolesErrorDisplay.html('There were errors below, correct them and try submitting again.');   
        userRolesErrorDisplay.show();
    }
}
JS;
$this->registerJs($usersJs, yii\web\View::POS_READY);






