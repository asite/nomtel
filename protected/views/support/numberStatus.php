<?php

$this->breadcrumbs = array(
    Number::label(2)
);
?>

<h1><?=Yii::t('app','Number')?></h1>

<script>
    function checkForm(form) {
        // check if there any active upload
        var uploadIsActive=false;
        $('form').each(function(){
            if ($(this).data('fileupload')) {
                if ($(this).data('fileupload')._active) uploadIsActive=true;
                var ids='';
                $(this).find('input[name="file_id"]').each(function(){
                    if (ids!='') ids+=',';
                    ids+=$(this).val();
                });
                $("#"+$(this).attr('id')+'-field').val(ids);
            }
        });

        if (uploadIsActive) {
            alert('Загрузка файлов на сервер еще не закончена');
            return false;
        }
        return true;
    }
</script>

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'report-form',
    'type' => 'horizontal',
    'htmlOptions'=>array('onsubmit'=>'return checkForm(this);')
));
?>

<input type="hidden" name="person_files" id="File-form-field" value=""/>

<?php echo $form->errorSummary($number); ?>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-160">
            <?php echo $form->maskFieldRow($number,'number','8 (999) 999-99-99',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
        <div class="form-container-item form-label-width-160">
            &nbsp;<?php $d=$this->widget('bootstrap.widgets.TbButton',array('label'=>'Найти','buttonType'=>'submit','htmlOptions'=>array('name'=>'findNumber'))); ?>
        </div>
    </div>

    <div style="clear:both"></div>
<?php if ($showStatusForm) { ?>
<script>
function statusProcessVisibleItems() {
    var status=$("#NumberSupportStatus_status").val();
    if (status=='') return;

    $("#callback, #active, #scans, .form-actions").hide();
    switch (status) {
        case 'CALLBACK':
            $("#callback, .form-actions").show();
            break;
        case 'ACTIVE':
            $("#active, #scans, .form-actions").show();
            checkUploadSize();
            break;
        default:
            $("#report-form").submit();
    }
}

function checkUploadSize() {
    var height_required=$("#scans").height();
    var height_actual=$("#scans_shadow").height();
    if ($('#File-form .files tr').size()==0) height_required-=50;
    if (height_actual!=height_required) $("#scans_shadow").height(height_required);

    var top_required=$("#scans_shadow").position().top;
    var top_actual=$("#scans").position().top;
    if (top_actual!=top_required) $("#scans").css('top',top_required);
}

function setStatus(status) {
    $("#NumberSupportStatus_status").val(status);
    statusProcessVisibleItems();
}

function UploaderAddFiles(id,files) {
    var form=$("#"+id);
    var uploader=form.data('fileupload');

    var download=uploader._renderDownload(files);

    download.appendTo(uploader.options.filesContainer);
    uploader._forceReflow(download);
    uploader._transition(download);
}

</script>
<?php
    if ($numberObj->status==Number::STATUS_FREE) {
        foreach(Number::getSupportStatusLabels() as $status_key=>$status_label) {
            $d=$this->widget('bootstrap.widgets.TbButton',array(
                'label'=>$status_label,
                'htmlOptions'=>array('onclick'=>'setStatus("'.$status_key.'")')
            ));
            echo "&nbsp;";
        }
    } else {
            $d=$this->widget('bootstrap.widgets.TbButton',array(
                'label'=>'Полная форма договора',
                'url'=>$this->createUrl('subscriptionAgreement/update',array('number_id'=>$numberObj->id))
            ));
            echo "&nbsp;";
            $d=$this->widget('bootstrap.widgets.TbButton',array(
                'label'=>Number::getSupportStatusLabel('SERVICE_INFO'),
                'htmlOptions'=>array('onclick'=>'setStatus("SERVICE_INFO")')
            ));
   }
?>


<?php echo $form->hiddenField($status, 'status') ?>

<div style="margin-top:10px;"></div>

<?php echo $form->errorSummary(array($status,$person)); ?>

<div id="callback" style="display:none;">
        <?php echo $form->PickerDateTimeRow($status,'callback_dt',array('class'=>'span2')); ?>
        <?php echo $form->textFieldRow($status,'callback_name',array('class'=>'span3')); ?>
    </div>

    <div id="active" style="display:none;">

            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-220">
                    <?php echo $form->textFieldRow($status,'getting_passport_variant',array('class'=>'span4')); ?>
                    <?php echo $form->textFieldRow($status,'number_region_usage',array('class'=>'span4')); ?>
                </div>
            </div>

        <div id="scans_shadow" style="clear:both;"></div>

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
                    <?php echo $form->maskFieldRow($person,'passport_issue_date','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
            </div>

            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-140">
                    <?php echo $form->textFieldRow($person,'passport_issuer',array('class'=>'span7','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
                </div>
            </div>

            <div class="form-container-horizontal">
                <div class="form-container-item form-label-width-140">
                     <?php echo $form->maskFieldRow($person,'birth_date','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
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


    </div>

    <div class="form-actions" style="display:none;">
    <?php $d=$this->widget('bootstrap.widgets.TbButton',array('label'=>Yii::t('app','Save'),'buttonType'=>'submit','type'=>'primary','icon'=>'ok white')); ?>
    </div>

<?php } ?>
<?php
$this->endWidget();
?>

<div id="scans" style="display:none;position:absolute;">   <?php //top:330px; ?>
<h3>Сканы паспорта</h3>
<?php
$this->widget('bootstrap.widgets.TbFileUpload', array(
    'url' => $this->createUrl("file/upload",array('name'=>'File')),
    'model' => new File(),
    'attribute' => 'url',
    'multiple' => true,
    'formView'=>'application.views.fileupload.form',
    'uploadView'=>'application.views.fileupload.upload',
    'downloadView'=>'application.views.fileupload.download',
    'options' => array(
        'maxFileSize' => 1024*1024*10,
        'acceptFileTypes' => 'js:/(\.|\/)(jpe?g|png)$/i',
        'autoUpload' => true,
        'previewSourceMaxFileSize' => 1024*1024*10,
        'previewMaxWidth' => Yii::app()->params['thumbs']['uploader']['width'],
        'previewMaxHeight' => Yii::app()->params['thumbs']['uploader']['height'],
        'limitConcurrentUploads' => 2,
    )));
?>
</div>

<?php if ($person_files!='') Yii::app()->clientScript->registerScript('File-form',"UploaderAddFiles('File-form',$person_files);",CClientScript::POS_READY); ?>
<script>$(function(){statusProcessVisibleItems();});</script>
<script>setInterval('checkUploadSize()',250);</script>
