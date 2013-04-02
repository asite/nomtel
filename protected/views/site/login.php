<form action="?" method="post">
	<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken;?>">
    <div class="modal" style="top:50%;margin-top:-150px;">
        <div class="modal-header" xmlns="http://www.w3.org/1999/html">
            <h3><?php echo Yii::t('app','Please login');?></h3>
        </div>
        <div class="modal-body">
            <div class="form-horizontal">
                <fieldset>
                    <?php if (Yii::app()->user->hasFlash('loginError')) {?>
                        <div class="alert alert-error"><?php echo Yii::app()->user->getFlash('loginError')?></div>
                    <?php }?>
                    <div class="control-group">
                        <label class="control-label" for="loginForm_username"><?php echo Yii::t('app','Username')?></label>
                        <div class="controls">
                            <input type="text" name="loginForm[username]" id="loginForm_username">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="loginForm_password"><?php echo Yii::t('app','Password');?></label>
                        <div class="controls">
                            <input type="password" name="loginForm[password]" id="loginForm_password">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox inline">
                                <input type="checkbox" name="loginForm[remember]" id="loginForm_remember" value="1">
                                <label for="loginForm_remember"><?php echo Yii::t('app','Remember me'); ?></label>
                            </label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="$('form').submit()">
                <?php echo Yii::t('app','Enter')?> <i class="icon-chevron-right icon-white"></i>
            </button>
        </div>
    </div>
</form>
