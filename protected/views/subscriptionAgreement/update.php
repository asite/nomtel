<?php

$this->breadcrumbs = array(
    $agreement->adminLabel(SubscriptionAgreement::label(1))
);

?>

<h1><?=$agreement->adminLabel(SubscriptionAgreement::label(1))?></h1>

<div class="w80 cfix">
    <div style="float:left;width:33%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
        'data'=>$person,
        'attributes'=>array(
            'surname',
        ),
    )); ?>
    </div>
    <div style="float:left;width:33%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
        'data'=>$person,
        'attributes'=>array(
            'name',
        ),
    )); ?>
    </div>
    <div style="float:left;width:33%;">
        <?php $this->widget('bootstrap.widgets.TbDetailView',array(
        'data'=>$person,
        'attributes'=>array(
            'middle_name'
        ),
    )); ?>
    </div>
</div>

<script>
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

<style>
    .fileupload-buttonbar {
        display:none;
    }
    td.delete {
        display:none;
    }
</style>
    <?php $form = $this->beginWidget('BaseTbActiveForm', array(
    'id' => 'subscriptionagreement-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit' => true, 'validateOnChange' => false),
    'htmlOptions'=>array('onsubmit'=>'return checkForm(this);')
));
    ?>

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

    <div class="pOS_load_passport cfix" style="padding-bottom:10px;margin-top:10px;">
        Для переоформления номера напишите сообщение в службу поддержки
    </div>

</div>

<?php if ($person_files!='') Yii::app()->clientScript->registerScript('File-form',"UploaderAddFiles('File-form',$person_files);",CClientScript::POS_READY); ?>

<?php if ($agreement_files!='') Yii::app()->clientScript->registerScript('File2-form',"UploaderAddFiles('File2-form',$agreement_files);",CClientScript::POS_READY); ?>
