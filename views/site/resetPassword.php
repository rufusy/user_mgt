<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/8/2023
 * @time: 10:14 PM
 */

/* @var $this yii\web\View */
/* @var $model app\models\ForgotPasswordForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">
            <div style="padding: 20px; ">
                <img class="mx-auto d-block" style="height: 100px;" src="<?=Yii::getAlias('@web');?>/img/ndu-arms.png" alt="Logo">
            </div>

            <p class="login-options-title">
                Follow instructions sent to your email address
            </p>

            <?php
            $form = ActiveForm::begin([
                'action' => Url::to(['/site/password-change-code']),
            ]);

            echo $form->field($model, 'username')
                ->textInput(['class' => 'form-control'])
                ->label('Username', ['class' => 'required-control-label'])
                ->hint('Type in your payroll number', ['tag' => 'small', 'class' => 'text-muted']);
            ?>

            <div class="row">
                <div class="col-4"></div>
                <div class="col-8">
                    <button type="submit" class="btn btn-success btn-block">
                        Submit
                    </button>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <p class="mb-1">
                <?= Html::a('Sign in', ['/site/login'], ['title' => 'Sign in', 'class' => 'btn-link']); ?>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->