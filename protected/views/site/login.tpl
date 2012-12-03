<form action="?" method="post">
	<input type="hidden" name="YII_CSRF_TOKEN" value="{Yii::app()->request->csrfToken}">
    <div class="modal" style="margin-top:-150px;   ">
        <div class="modal-header" xmlns="http://www.w3.org/1999/html">
            <h3>{Yii::t('app','Please login')}</h3>
        </div>
        <div class="modal-body">
            <div class="form-horizontal">
                <fieldset>
                    {if Yii::app()->user->hasFlash('loginError')}
                        <div class="alert alert-error">{Yii::app()->user->getFlash('loginError')}</div>
                    {/if}
                    <div class="control-group">
                        <label class="control-label" for="loginForm_username">{Yii::t('app','Username')}</label>
                        <div class="controls">
                            <input type="text" name="loginForm[username]" id="loginForm_username">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="loginForm_password">{Yii::t('app','Password')}</label>
                        <div class="controls">
                            <input type="password" name="loginForm[password]" id="loginForm_password">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox inline">
                                <input type="checkbox" name="loginForm[remember]" id="loginForm_remember" value="1">
                                <label for="loginForm_remember">{Yii::t('app','Remember me')}</label>
                            </label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="$('form').submit()">
                {Yii::t('app','Enter')} <i class="icon-chevron-right icon-white"></i>
            </button>
        </div>
    </div>
</form>
