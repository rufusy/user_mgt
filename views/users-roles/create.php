<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/2/2023
 * @time: 9:50 AM
 */

/**
 * @var yii\web\View $this
 * @var string $title
 * @var string $username
 * @var AuthItem[] $roles
 * @var AuthAssignment[] $assignedRoles
 */

use app\models\AuthAssignment;
use app\models\AuthItem;
use yii\helpers\Url;

$this->title = $title;
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="page-header">
        <h1>Assign and manage user roles</h1>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8 offset-md-2 offset-lg-2">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            Assign roles
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="assign-role-form" onsubmit="return false" method="post" action="#">
                            <div class="loader"></div>
                            <div class="error-display alert text-center" role="alert"></div>
                            <div class="form-group row">
                                <label for="username" class="col-sm-3 col-md-3 col-lg-3 offset-md-2 offset-lg-2 text-md-right text-lg-right col-form-label required-control-label">
                                    Roles
                                </label>
                                <div class="col-sm-5 col-md-5 col-lg-5">
                                    <select class="form-control select2bs4" name="roles[]" multiple="multiple" data-placeholder="Select roles" style="width: 100%;">
                                        <?php foreach ($roles as $role):?>
                                        <option value="<?=$role['name']?>"><?=$role['name']?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-5 col-md-5 col-lg-5 offset-md-5 offset-lg-5">
                                    <button type="submit" id="assign-role-btn" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-8 offset-md-2 offset-lg-2">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            Manage roles
                        </h3>
                    </div>
                    <div class="card-body">

                        <div class="callout callout-info">
                            <h6>User has the following roles. Uncheck and submit to remove.</h6>
                        </div>

                        <form id="delete-user-role-form" onsubmit="return false" method="post" action="#" style="margin-top: 30px;">
                            <div class="loader"></div>
                            <div class="error-display alert text-center" role="alert"></div>
                            <div class="form-group row">
                                <?php foreach ($assignedRoles as $assignedRole):
                                    $role = $assignedRole['item_name'];
                                ?>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input assigned-role" type="checkbox" id="<?=$role?>" name="<?=$role?>" checked>
                                            <label for="<?=$role?>" class="custom-control-label"><?=$role?></label>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-5 col-md-5 col-lg-5 offset-md-5 offset-lg-5">
                                    <button type="submit" id="delete-user-role-btn" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$assignUserRolesUrl = Url::to(['users-roles/save']);
$deleteUserRolesUrl = Url::to(['users-roles/delete']);

$assignRolesJs = <<< JS
const assignUserRolesUrl = '$assignUserRolesUrl'; 
const deleteUserRolesUrl = '$deleteUserRolesUrl'; 
const username = '$username';

const assignRolesForm = $('#assign-role-form');
const assignRolesLoader = $('#assign-role-form > .loader');
assignRolesLoader.html(loader);
assignRolesLoader.hide();
const assignRolesErrorDisplay = $('#assign-role-form > .error-display');
assignRolesErrorDisplay.hide();

const deleteRolesLoader = $('#delete-user-role-form > .loader');
deleteRolesLoader.html(loader);
deleteRolesLoader.hide()
const deleteRolesErrorDisplay = $('#delete-user-role-form > .error-display');
deleteRolesErrorDisplay.hide();

assignRolesForm.validate({
    rules: {
        'roles': {
            required: true
        }
    }
});

$('#assign-role-btn').click(function (e){
    e.preventDefault();
    if(assignRolesForm.valid()){
        if(confirm('Grant user roles?')){
            assignRolesErrorDisplay.hide();
            assignRolesLoader.show();
            let formData = assignRolesForm.serializeArray();
            formData.push({name: 'username', value: username})
            $.ajax({
                url: assignUserRolesUrl,
                type: 'POST',
                data: formData
            }).done(function (data){
                assignRolesLoader.hide();
                if(!data.success){
                    assignRolesErrorDisplay.html(data.message) 
                    assignRolesErrorDisplay.show();
                }
            }).fail(function (data){
                assignRolesLoader.hide();
                assignRolesErrorDisplay.html(data.responseText) 
                assignRolesErrorDisplay.show();
            });
        }
    }else{
        assignRolesLoader.hide();
        assignRolesErrorDisplay.html('There were errors below, correct them and try submitting again.');   
        assignRolesErrorDisplay.show();
    }
});

$('#delete-user-role-btn').click(function (e){
    e.preventDefault();
    let rolesToRemove = [];
    //https://stackoverflow.com/questions/8465821/find-all-unchecked-checkboxes-in-jquery
    $('input.assigned-role:checkbox:not(:checked)').each(function (e){
        rolesToRemove.push($(this).attr('name'));
    });
    
    if(rolesToRemove.length === 0){
        deleteRolesLoader.hide();
        deleteRolesErrorDisplay.html('No roles have been unchecked for removal from this user.');   
        deleteRolesErrorDisplay.show();
    }else{
        if(confirm('Remove roles from this user?')){
            deleteRolesErrorDisplay.hide();
            deleteRolesLoader.show();
            $.ajax({
                url: deleteUserRolesUrl,
                type: 'POST',
                data: {'username': username, 'roles' : rolesToRemove}
            }).done(function (data){
                deleteRolesLoader.hide();
                if(!data.success){
                    deleteRolesErrorDisplay.html(data.message) 
                    deleteRolesErrorDisplay.show();
                }
            }).fail(function (data){
                deleteRolesLoader.hide();
                deleteRolesErrorDisplay.html(data.responseText) 
                deleteRolesErrorDisplay.show();
            });
        }
    }
});

JS;
$this->registerJs($assignRolesJs, yii\web\View::POS_READY);




