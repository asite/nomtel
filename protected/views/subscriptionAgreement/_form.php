<script>
    function checkPassport() {
        <?php if ($checkPassport) { ?>

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
                    for(i in data.fields) {
                        var field=$("#"+i);
                        if (field.val()=='') field.val(data.fields[i]);
                    }
                    UploaderAddFiles("File-form",data.person_files);
                },
                'json'
        );
        <?php } ?>
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

<div class="form">

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

    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'url'=>$this->createUrl('',array('id'=>$agreement->id,'sim_id'=>$sim->id,'fullForm'=>1)),
        'label'=>'Полная форма'
    ));?>

    <?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'subscriptionagreement-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
    'htmlOptions'=>array('onsubmit'=>'return checkForm(this);')
));
    ?>

    <?php if ($fullForm) { ?>
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

    <h3><?=Yii::t('app','Subscription Agreement Number')?> <?=CHtml::encode($agreement->defined_id)?></h3>

    <script>
        function download() {
            var data={
                YII_CSRF_TOKEN:'<?=Yii::app()->request->csrfToken?>',
                id:<?=$agreement->id?>,
                sim_id:<?=$sim->id?>
            };
            $('#subscriptionagreement-form input').each(function(){
                data[$(this).attr('name')]=$(this).val();
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

    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'link', 'url'=>$this->createUrl('getBlank',array('id'=>$agreement->id)),'label'=>Yii::t('app','Download agreement blank'))); ?>

    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 'htmlOptions'=>array('onclick'=>'download()'),'label'=>Yii::t('app','Download agreement'))); ?>
    <?php } else { ?>

    <div class="pOS_load_passport cfix" style="padding-bottom:10px;margin-top:10px;">
        <a href="/static/passport.jpg" target="_blank"><img style="margin-left: 30px;" src="/static/passport.jpg" width="130" align="right" alt=""></a>
        Для упрощения процедуры регистрации, вы можете загрузить изображения паспорта и
        договора абонента самостоятельно здесь. После проверки данных Абоненту поступит СМС
        сообщение об успешной регистрации.
    </div>

    <?php } ?>

    <input type="hidden" name="person_files" id="File-form-field" value=""/>
    <input type="hidden" name="agreement_files" id="File2-form-field" value=""/>
    <?php
    $this->endWidget();
    ?>

    <h3>Изображения документа</h3>
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
    <h3>Изображения договора</h3>
    <?php
    $this->widget('bootstrap.widgets.TbFileUpload', array(
        'url' => $this->createUrl("file/upload",array('name'=>'File2')),
        'htmlOptions'=>array('data-id'=>'File2-form'),
        'model' => new File2(),
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

    <?php
    echo '<div class="form-actions">';
    echo CHtml::htmlButton('<i class="icon-ok icon-white"></i> '.Yii::t('app', 'Save'), array('class'=>'btn btn-primary', 'type'=>'button','onclick'=>'$("#subscriptionagreement-form").submit()'));
    echo '</div>';
    ?>
</div>

<?php if ($person_files!='') Yii::app()->clientScript->registerScript('File-form',"UploaderAddFiles('File-form',$person_files);",CClientScript::POS_READY); ?>

<?php if ($agreement_files!='') Yii::app()->clientScript->registerScript('File2-form',"UploaderAddFiles('File2-form',$agreement_files);",CClientScript::POS_READY); ?>
