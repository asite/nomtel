<link rel="stylesheet" href="/static/style.css">

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'operator-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
));
?>
<style>.modal {top:45%;}</style>
    <div class="modal pOLogin" style="margin-top: -230px;">
        <div class="modal-header cfix">
            <a href="http://nomtel.ru"><img src="/static/logo.png" alt="" align="right"/></a><h3 style="line-height: 54px;">Добро пожаловать!</h3>
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
    <!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = '42083';
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script>
<!-- {/literal} END JIVOSITE CODE -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter20314267 = new Ya.Metrika({id:20314267,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/20314267" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<?php $this->endWidget(); ?>