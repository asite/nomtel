<link rel="stylesheet" href="/static/style.css">

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'operator-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
));
?>
    <div class="modal pOLogin" style="margin-top: -230px;">
        <div class="modal-header cfix">
            <img src="/static/logo.png" alt="" align="right"/><h3 style="line-height: 54px;">Добро пожаловать!</h3>
        </div>
        <div class="modal-body">
              В систему самообслуживания Вашего номера. Здесь Вы можете:
        <ul>
            <li>Решить проблемные вопросы</li>
            <li>Заказать детализацию</li>
            <li>Подключить / Отключить услуги</li>
            <li>Зарегистрировать Ваш номер</li>
        </ul>
        </div>
    </div>
<style> span.error {display:none !important;} </style>
    <div class="modal pOLogin">
        <div class="modal-header" xmlns="http://www.w3.org/1999/html">
            <h3><?php echo Yii::t('app','Login');?></h3>
        </div>
        <div class="modal-body">
            <?php
            $this->widget('bootstrap.widgets.TbAlert', array(
                'block' => true, // display a larger alert block?
                'fade' => true, // use transitions?
                'closeText' => '×', // close link text - if set to false, no close link is displayed
                'alerts' => array( // configurations per alert type
                    'success' => array('block' => true, 'fade' => true, 'closeText' => '×'), // success, info, warning, error or danger
                    'error' => array('block' => true, 'fade' => true, 'closeText' => '×'), // success, info, warning, error or danger
                ),
            ));
            ?>
                    <?=$form->errorSummary($model,'');?>

                    <?=$form->maskFieldRow($model,'number','8 (999) 999-99-99')?>
                    <?=$form->passwordFieldRow($model,'password')?>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="$('form').submit()" style="position:absolute;left:195px;">
                <?php echo Yii::t('app','Enter')?> <i class="icon-chevron-right icon-white"></i>
            </button>
            <?php $this->widget('bootstrap.widgets.TbButton',array('label'=>'Восстановить пароль','buttonType'=>'submit','htmlOptions'=>array('name'=>'restore'))); ?>
        </div>
    </div>

<?php $this->endWidget(); ?>