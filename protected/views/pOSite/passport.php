Уважаемый Абонент! Ваш номер обслуживается ООО "Номтел". ФЗ 126 "О связи" обязывает нас знать конечного пользователя номера.
Просим пройти процедуру регистрации. С вами свяжется наш оператор и оповестит вас о успешном подтверждении данных.

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

    function checkUploadSize() {
        var height_required=$("#scans").height();
        var height_actual=$("#scans_shadow").height();
        if ($('#File-form .files tr').size()==0) height_required-=50;
        if (height_actual!=height_required) $("#scans_shadow").height(height_required);

        var top_required=$("#scans_shadow").position().top;
        var top_actual=$("#scans").position().top;
        if (top_actual!=top_required) $("#scans").css('top',top_required);
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

<?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'report-form',
    'type' => 'horizontal',
    'htmlOptions'=>array('onsubmit'=>'return checkForm(this);')
));
?>

<?php echo $form->errorSummary($person); ?>

<input type="hidden" name="person_files" id="File-form-field" value=""/>

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
        <div class="form-container-item form-label-width-80">
            <?php echo $form->textFieldRow($person,'passport_series',array('class'=>'span2','maxlength'=>10,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
            <?php echo $form->textFieldRow($person,'passport_number',array('class'=>'span2','maxlength'=>20,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
        <div class="form-container-item form-label-width-60">
            <?php echo $form->radioButtonListRow($person,'sex',Person::getSexLabels(),array('errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
        <div class="form-container-item form-label-width-140">
            <?php echo $form->maskFieldRow($person,'birth_date','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-140">
            <?php echo $form->textareaRow($person,'birth_place',array('class'=>'span6','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-140">
            <?php echo $form->textareaRow($person,'passport_issuer',array('class'=>'span6','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-140">
            <?php echo $form->maskFieldRow($person,'passport_issue_date','99.99.9999',array('class'=>'span2','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
        <div class="form-container-item form-label-width-200">
            <?php echo $form->textFieldRow($person,'passport_issuer_subdivision_code',array('class'=>'span2','maxlength'=>200,'errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>

    <div class="form-container-horizontal">
        <div class="form-container-item form-label-width-140">
            <?php echo $form->textareaRow($person,'registration_address',array('class'=>'span6','errorOptions'=>array('hideErrorMessage'=>true))); ?>
        </div>
    </div>
</fieldset>

<div id="scans_shadow" style="clear:both;"></div>

<div class="form-actions">
    <?php $d=$this->widget('bootstrap.widgets.TbButton',array('label'=>Yii::t('app','Save'),'buttonType'=>'submit','type'=>'primary','icon'=>'ok white')); ?>
</div>

<?php
$this->endWidget();
?>

<div id="scans" style="position:absolute; width: 760px;">
<div class="pOS_load_passport cfix">
    <a href="/static/passport.jpg" target="_blank"><img style="margin-left: 30px;" src="/static/passport.jpg" width="130" align="right" alt=""></a>
    Ваши данные необходимо подтвердить копиями страниц паспорта с данными и с адресом регистрации.
    <h3>Сканы паспорта</h3>
</div>
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
<script>setInterval('checkUploadSize()',250);</script>
