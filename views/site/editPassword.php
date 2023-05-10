<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/8/2023
 * @time: 11:49 PM
 */

/**
 * @var yii\web\View $this
 * @var string $title
 * @var string $code
 * @var string $username
 */

$this->title = $title;
?>

<div class="login-box">
    <div data-aos="fade-left">
        Login
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <div id="login-form">
                <form id="update-password-form" onsubmit="return false" method="post" action="#">
                    <div class="loader"></div>
                    <div class="error-display alert text-center" role="alert">
                    </div>

                    <div class="form-group">
                        <small class="text-muted">Password must be 8 to 20 characters</small><br>
                        <small class="text-muted">Password must contain at least one uppercase</small><br>
                        <small class="text-muted">Password must contain at least one lowercase</small><br>
                        <small class="text-muted">Password must contain at least one digit</small>
                    </div>

                    <div class="form-group">
                        <label for="new-password" class="required-control-label">
                            Choose a new password
                        </label>
                        <input type="password" class="form-control" id="new-password" name="newPassword" value="" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password" class="required-control-label">
                            Re-enter new password
                        </label>
                        <input type="password" class="form-control" id="confirm-password" name="confirmPassword" value="" required>
                    </div>

                    <div class="form-group row">
                        <button id="btn-update-password" class="btn btn-success">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<?php
use yii\helpers\Url;
$updatePasswordUrl = Url::to(['/site/update-password']);
$updatePasswordJs = <<< JS
const username = '$username';
const code = '$code';
const updatePasswordUrl = '$updatePasswordUrl'; 
const passwordForm = $('#update-password-form');

const passwordLoader = $('#update-password-form > .loader');
passwordLoader.html(loader);
passwordLoader.hide();
        
const passwordErrorDisplay =  $('#update-password-form > .error-display');
passwordErrorDisplay.hide();

passwordForm.validate({
    rules: {
        'oldPassword': {
            required: true
        },
        'newPassword': {
            required: true,
            passwordStrength: true
        },
        'confirmPassword': {
            required: true,
            equalTo: '#new-password'
        }
    },
    messages: {
        'confirmPassword': {
            equalTo: 'Please re-enter the new password again'
        }
    }
});

$('#btn-update-password').click(function (e){
    e.preventDefault();
    if(passwordForm.valid()){
        if(confirm('Change password?')){
            passwordErrorDisplay.hide();
            passwordLoader.html('processing...');
            passwordLoader.show();
            
            let formData = passwordForm.serializeArray();
            formData.push({name: 'username', value: username})
            formData.push({name: 'code', value: code});
            
           $.ajax({
                url: updatePasswordUrl,
                type: 'POST',
                data: formData
           }).done(function (data){
                if(data.success){
                    passwordForm.trigger("reset");
                    successToaster(data.message);
                    passwordLoader.html(data.message);
                }else{
                    passwordLoader.hide();
                    passwordErrorDisplay.html(data.message) 
                    passwordErrorDisplay.show();
                }
           }).fail(function (data){
                passwordLoader.hide();
                passwordErrorDisplay.html(data.responseText) 
                passwordErrorDisplay.show();
           });
        }
    }else{
         passwordLoader.hide();
         passwordErrorDisplay.html('There were errors below, correct them and try submitting again.');   
         passwordErrorDisplay.show();
    }
});
JS;
$this->registerJs($updatePasswordJs, yii\web\View::POS_READY);