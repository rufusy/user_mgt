<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 4/26/2023
 * @time: 9:17 PM
 */

/**
 * @var yii\web\View $this
 * @var string $title
 */

use yii\helpers\Url;

$this->title = $title;
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="page-header">
        <h1>Create a new user</h1>
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
                            New user
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="create-user-form" onsubmit="return false" method="post" action="#">
                            <div class="loader"></div>
                            <div class="error-display alert text-center" role="alert"></div>
                            <div class="form-group row">
                                <label for="username" class="col-sm-3 col-md-3 col-lg-3 offset-md-2 offset-lg-2 text-md-right text-lg-right col-form-label required-control-label">
                                    Username
                                </label>
                                <div class="col-sm-5 col-md-5 col-lg-5">
                                    <input type="text" class="form-control" id="username" name="username">
                                    <small class="text-muted"> Payroll number</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-5 col-md-5 col-lg-5 offset-md-5 offset-lg-5">
                                    <button type="submit" id="btn-create-user" class="btn btn-success">Submit</button>
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
$newUserUrl = Url::to(['/users/save']);

$createUserJs = <<< JS
const newUserUrl = '$newUserUrl'; 
const profileForm = $('#create-user-form');

const profileLoader = $('#create-user-form > .loader');
profileLoader.html(loader);
profileLoader.hide();
        
const profileErrorDisplay =  $('#create-user-form > .error-display');
profileErrorDisplay.hide();

profileForm.validate({
    rules: {
        'username': {
            required: true
        }
    }
});

$('#btn-create-user').click(function (e){
    e.preventDefault();
    if(profileForm.valid()){
        if(confirm('Create new user?')){
            profileErrorDisplay.hide();
            profileLoader.show();
            $.ajax({
                url: newUserUrl,
                type: 'POST',
                data: profileForm.serialize()
            }).done(function (data){
                profileLoader.hide();
                if(data.success){
                    successToaster(data.message);
                }else{
                    profileErrorDisplay.html(data.message) 
                    profileErrorDisplay.show();
                }
            }).fail(function (data){
                profileLoader.hide();
                profileErrorDisplay.html(data.responseText) 
                profileErrorDisplay.show();
            });
        }
    }else{
        profileLoader.hide();
        profileErrorDisplay.html('There were errors below, correct them and try submitting again.');   
        profileErrorDisplay.show();
    }
});
JS;
$this->registerJs($createUserJs, yii\web\View::POS_READY);


