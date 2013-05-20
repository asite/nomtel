<h1>Инкассация: шаг 2 из 2</h1>
<h3>Кассир: <?=$cashier?></h3>
<h3>Баланс кассы: <?=$balance?></h3>
<h3>Инкассатор: <?=$collection->collectorSupportOperator?></h3>
<h3>Сумма инкассации: <?=$collection->sum?></h3>

Код подтверждения инкассации отослан на номер '<?=$collection->collectorSupportOperator->phone?>'.
    <form style="margin-top:20px;" class="form-horizontal" action="" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?=Yii::app()->request->csrfToken?>"/>
        <div class="control-group">
            <label class="control-label" for="code">Код подтверждения</label>
            <div class="controls">
                <input class="span2" type="text" id="code" name="code"/>
            </div>
        </div>

    <?php
    echo '<div class="form-actions">';
    echo CHtml::htmlButton('Подтвердить', array('class'=>'btn btn-primary', 'type'=>'submit'));
    echo '</div>';
    ?>

    </form>
