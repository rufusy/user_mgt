<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 * @date: 5/10/2023
 * @time: 11:36 PM
 */

/**
 * @var string $recipient
 * @var string $code
 * @var string $username
 */
?>

<tr>
    <td class="email-body" width="100%" cellpadding="0" cellspacing="0"
        style="word-break: break-word; margin: 0; padding: 0; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; width: 100%; -premailer-width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; background-color: #FFFFFF;"
        bgcolor="#FFFFFF">
        <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation"
               style="width: 570px; -premailer-width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; background-color: #FFFFFF; margin: 0 auto; padding: 0;"
               bgcolor="#FFFFFF">
            <!-- Body content -->
            <tr>
                <td class="content-cell"
                    style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 35px;">
                    <div class="f-fallback">
                        <p style="margin-top: 0; color: #333333; font-size: 22px; font-weight: bold; text-align: left;">
                            <?= $recipient ?>,
                        </p>

                        <p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">
                            Please click on this link to reset your password:
                            <a href="<?= Yii::$app->params['editPasswordUrl'] . '?username=' . $username . '&token=' . $code ?>">
                                Reset password</a>
                        </p>

                        <p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">
                            If the above link doesn't work, copy the following link into your browser:
                        </p>

                        <p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">
                            <?= Yii::$app->params['editPasswordUrl'] . '?username=' . $username . '&token=' . $code ?>
                        </p>

                        <p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">
                            <strong>This is an autogenerated message. Please don't reply to it.</strong>
                        </p>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>