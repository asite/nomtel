<?php

$this->breadcrumbs = array(
    Yii::t('app','Creating subscription agreement')
);

?>

<h1><?php echo Yii::t('app','Creating subscription agreement'); ?></h1>

<script>
function checkPassport() {
    var passport_series=$("#Person_passport_series");
    var passport_number=$("#Person_passport_number");

    if (passport_series.val()=='' || passport_number.val()=='') return;

    $.post(
        '<?=$this->createUrl('passportSuggest')?>',
        {
            passport_series:passport_series.val(),
            passport_number:passport_number.val(),
            YII_CSRF_TOKEN:'<?=Yii::app()->request->csrfToken?>'
        },
        function(data) {
            for(i in data) {
                var field=$("#"+i);
                if (field.val()=='') field.val(data[i]);
            }
        },
        'json'
    );
}
</script>
<?php
ob_start();
$this->widget('bootstrap.widgets.TbFileUpload', array(
    'url' => $this->createUrl("file/upload"),
    'model' => new File(),
    'attribute' => 'url',
    'multiple' => true,
    'options' => array(
        'maxFileSize' => 1024*1024*10,
        'acceptFileTypes' => 'js:/(\.|\/)(gif|jpe?g|png)$/i',
        'autoUpload' => true,
        'previewSourceMaxFileSize' => 1024*1024*10,
        'limitConcurrentUploads' => 2
    )));
$personUpload=ob_get_clean();
?>

<div class="form">

    <?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'subscriptionagreement-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
    //'htmlOptions'=>array('enctype'=>'multipart/form-data')
));
    ?>

    <p class="note">
        <?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
    </p>

    <?php echo $form->errorSummary($person); ?>

    <fieldset>
        <legend><?php echo Yii::t('app','Subscriber');?></legend>
        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($person,'surname',array('class'=>'span2','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($person,'name',array('class'=>'span2','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($person,'middle_name',array('class'=>'span2','maxlength'=>100,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
            <div class="form-container-item form-label-width-100">
                <?php echo $form->textFieldRow($person,'phone',array('class'=>'span2','maxlength'=>50,'errorOptions'=>array('hideErrorMessage'=>true))); ?>

                <?php echo $form->textFieldRow($person,'email',array('class'=>'span2','maxlength'=>50,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
    </fieldset>

    <fieldset>
        <legend><?php echo Yii::t('app','Passport');?></legend>
        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($person,'passport_series',array('class'=>'span1','maxlength'=>10,'onchange'=>'checkPassport()','errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
            <div class="form-container-item form-label-width-80">
                <?php echo $form->textFieldRow($person,'passport_number',array('class'=>'span2','maxlength'=>20,'onchange'=>'checkPassport()','errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
            <div class="form-container-item form-label-width-120">
                <?php echo $form->pickerDateRow($person,'passport_issue_date',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
        </div>

        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($person,'passport_issuer',array('class'=>'span7','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
        </div>

        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->pickerDateRow($person,'birth_date',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($person,'birth_place',array('class'=>'span3','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
        </div>

        <div class="form-container-horizontal">
            <div class="form-container-item form-label-width-140">
                <?php echo $form->textFieldRow($person,'registration_address',array('class'=>'span7','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            </div>
        </div>
    </fieldset>

    <h3><?=Yii::t('app','Subscription Agreement Number')?> <?=CHtml::encode($agreement->defined_id)?></h3>

    <script>
        function download() {
            var data={
                YII_CSRF_TOKEN:'<?=Yii::app()->request->csrfToken?>',
                id:<?=$agreement->id?>,
                sim_id:<?=$sim->id?>
            };
            $('#subscriptionagreement-form input').each(function(){
                   data[$(this).name]=$(this).val();
            });
            $.post(
                    '<?=$this->createUrl('saveFormInfo')?>',
                    data,
                    function(data) {
                        window.location.href=data.url;
                    },
                    'json'
            );
        }
    </script>

    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'link', 'url'=>$this->createUrl('getBlank',array('id'=>$agreement->id)),'label'=>'Blank')); ?>

    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'htmlOptions'=>array('onclick'=>'download()'),'label'=>'Document')); ?>

    <?php
    echo '<div class="form-actions">';
    echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'submit'));
    echo '&nbsp;&nbsp;&nbsp;'.CHtml::htmlButton('<i class="icon-remove"></i> '.Yii::t('app', 'Cancel'), array('class'=>'btn', 'type'=>'button', 'onclick'=>'window.location.href="'.$this->createUrl('admin').'"'));
    echo '</div>';
    $this->endWidget();
    ?>

    <?=$personUpload?>

</div>