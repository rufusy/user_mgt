<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/8/2023
 * @time: 8:18 PM
 */

/**
 * @var yii\web\View $this
 * @var string $title
 * @var yii\data\ActiveDataProvider $rolesDataProvider
 * @var app\models\search\RolesSearch $rolesSearchModel
 */

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

$this->title = $title;
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="page-header">
        <h1>Roles</h1>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?php
                $nameCol = [
                    'attribute' => 'name',
                    'label' => 'NAME',
                    'vAlign' => 'middle',
                ];
                $descriptionCol = [
                    'attribute' => 'description',
                    'label' => 'DESCRIPTION',
                    'vAlign' => 'middle',
                ];
                $actionsCol = [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{edit}',
                    'contentOptions' => [
                        'style'=>'white-space:nowrap;',
                        'class'=>'kartik-sheet-style kv-align-middle'
                    ],
                    'buttons' => [
                        'edit' => function($url, $model){
                            return Html::a('<i class="fa fa-edit" aria-hidden="true">&nbsp;</i>edit role, view permissions',
                                Url::to(['/roles/edit', 'name' => $model['name']]),
                                [
                                    'title' => 'Edit role and view permissions',
                                    'class' => 'btn-link action-text-info'
                                ]);
                        },
                    ]
                ];

                $gridColumns = [
                    ['class' => 'kartik\grid\SerialColumn'],
                    $nameCol,
                    $descriptionCol,
//                    $actionsCol
                ];

                $toolbar = [
                    [
                        'content' =>
                            Html::button('<i class="fas fa-plus"></i> Create role', [
                                'title' => 'Create a new role',
                                'id' => 'new-role-btn',
                                'class' => 'btn btn-success btn-spacer btn-sm',
                            ]),
                        'options' => ['class' => 'btn-group mr-2']
                    ],
                    '{export}',
                    '{toggleData}',
                ];

                try{
                    echo GridView::widget([
                        'id' => 'roles-grid',
                        'dataProvider' => $rolesDataProvider,
                        'filterModel' => $rolesSearchModel,
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
                            'label' => 'Export roles'
                        ],
                        'panel' => [
                            'heading' => 'Roles'
                        ],
                        'persistResize' => false,
                        'itemLabelSingle' => 'role',
                        'itemLabelPlural' => 'roles',
                    ]);
                }catch (Exception $ex){
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

<div id="roles-modal" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="roles-modal-title" class="modal-title">
                    New role
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="roles-form" method="post" action="#" onsubmit="return false;" enctype="multipart/form-data">
                    <div class="loader"></div>
                    <div class="error-display alert text-center" role="alert"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name" class="required-control-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" rows="3" id="description" name="description"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" id="roles-submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$saveRoleUrl = Url::to(['/roles/save']);
$rolesJs = <<< JS
const saveRoleUrl = '$saveRoleUrl';
const rolesForm = $('#roles-form');
const rolesLoader = $('#roles-form > .loader');
rolesLoader.html(loader);
rolesLoader.hide();
const rolesErrorDisplay =  $('#roles-form > .error-display');
rolesErrorDisplay.hide();
rolesForm.validate({
    rules: {
        'name': {
            required: true
        }
    }
});

$('#roles-grid-pjax').on('click', '#new-role-btn', function (e){
    e.preventDefault();
    rolesLoader.hide();
    rolesErrorDisplay.hide();
    rolesForm[0].reset();
    $('#roles-modal').modal('show');
});

$('#roles-submit').click(function (e){
    if(rolesForm.valid()){
        if(confirm('Create user role?')){
            rolesErrorDisplay.hide();
            rolesLoader.show();
            $.ajax({
                url: saveRoleUrl,
                type: 'POST',
                data: rolesForm.serialize()
            }).done(function (data){
                rolesLoader.hide();
                if(!data.success){
                    rolesErrorDisplay.html(data.message) 
                    rolesErrorDisplay.show();
                }
            }).fail(function (data){
                rolesLoader.hide();
                rolesErrorDisplay.html(data.responseText) 
                rolesErrorDisplay.show();
            });
        }
    }else{
        rolesLoader.hide();
        rolesErrorDisplay.html('There were errors below, correct them and try submitting again.');
        rolesErrorDisplay.show(); 
    }
});
JS;
$this->registerJs($rolesJs, yii\web\View::POS_READY);



