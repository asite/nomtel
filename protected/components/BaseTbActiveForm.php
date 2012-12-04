<?php

Yii::import('bootstrap.widgets.TbActiveForm');


class BaseTbActiveForm extends TbActiveForm {
    public function PickerDateRow($model,$field,$htmlOptions=array(),$options=array()) {
        $this->pickerRow('date',$model,$field,$htmlOptions,$options);
    }

    public function PickerTimeRow($model,$field,$htmlOptions=array(),$options=array()) {
        $this->pickerRow('time',$model,$field,$htmlOptions,$options);
    }

    public function PickerDateTimeRow($model,$field,$htmlOptions=array(),$options=array()) {
        $this->pickerRow('datetime',$model,$field,$htmlOptions,$options);
    }

    protected function pickerRow($mode,$model,$field,$htmlOptions=array(),$options=array()) {
        echo $this->textFieldRow($model,$field,$htmlOptions);
        CHtml::resolveNameID($model, $field,$htmlOptions);
        $id=$htmlOptions['id'];
		$cs = Yii::app()->getClientScript();

        $cs->registerCoreScript('jquery');
		$cs->registerCoreScript('jquery.ui');
        $cs->registerScriptFile($cs->getCoreScriptUrl().'/jui/js/jquery-ui-i18n.min.js');
        $cs->registerCssFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css');

		$assets = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.extensions.datetimepicker.assets'));
		$cs->registerCssFile($assets.'/jquery-ui-timepicker-addon.css');
		$cs->registerScriptFile($assets.'/jquery-ui-timepicker-addon.js',CClientScript::POS_END);

        $lang=Yii::app()->language;
        if ($lang!='en') $cs->registerScriptFile($assets."/localization/jquery-ui-timepicker-$lang.js",CClientScript::POS_END);
        $encodedOptions=CJavaScript::encode($options);
   		$js = "jQuery('#{$id}').{$mode}picker(jQuery.extend(jQuery.datepicker.regional['$lang'],{changeMonth:true,changeYear:true,dateFormat:'dd.mm.yy'},  $encodedOptions));";
		$cs->registerScript(__CLASS__.'#'.$id, $js);
    }

    /*
        public function imageUrlFieldRow($model, $field, $options = array()) {
            $res = '<div class="FileUrlFieldValue">';
            $res.=$model->$field != '' ? '<a target="_blank" href="'.$model->$field.'"><img src="' . Thumb::createUrl($model->$field,$options['thumbType']) . '" /></a>' : Yii::t('app', 'No Image yet uploaded');
            $res.='</div>';

            unset($options['thumbType']);
            $res = str_replace('<div class="controls">', '<div class="controls">' . $res, $this->fileFieldRow($model, $field, $options));

            return $res;
        }

        public function fileUrlFieldRow($model, $field, $options = array()) {
            $res = '<div class="FileUrlFieldValue">';
            $res.=$model->$field != '' ? '<a href="' . $model->$field . '">' . $model->$field . '</a>' : Yii::t('app', 'No File yet uploaded');
            $res.='</div>';

            $res = str_replace('<div class="controls">', '<div class="controls">' . $res, $this->fileFieldRow($model, $field, $options));

            return $res;
        }

        public function htmlAreaRow($model,$field,$htmlOptions=array(),$options=array()) {
            echo $this->textAreaRow($model,$field,$htmlOptions);
            $url=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.extensions.wysiwyg.assets'));
            Yii::app()->getClientScript()->registerScriptFile($url.'/tinymce/jquery.tinymce.js');
            CHtml::resolveNameID($model, $field,$htmlOptions);

            $options=array_merge(array(
                'script_url'=>"$url/tinymce/tiny_mce.js",
                'language'=>Yii::app()->language,

                'file_browser_callback'=>'elFinderBrowser',
                // General options
                'theme'=>'advanced',
                'plugins'=>'autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist',

                // Theme options
                'theme_advanced_buttons1'=>'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect',
                'theme_advanced_buttons2'=>'cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,code,|,forecolor,backcolor',
                'theme_advanced_buttons3'=>'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,fullscreen',
                'theme_advanced_toolbar_location'=>'top',
                'theme_advanced_toolbar_align'=>'left',
                'theme_advanced_statusbar_location'=>'bottom',
                'theme_advanced_resizing'=>true,
                'width'=>570,
                'height'=>300
            ),$options);

            $js="$('#{$htmlOptions['id']}').tinymce(".CJavaScript::encode($options).");";
            Yii::app()->getClientScript()->registerScript("tinymce_{$htmlOptions['id']}",$js,  CClientScript::POS_LOAD);

            Yii::app()->getClientScript()->registerScript('elFinderBrowser',"
    function elFinderBrowser (field_name, url, type, win) {
      var elfinder_url = '$url/elfinder.html';    // use an absolute path!
      tinyMCE.activeEditor.windowManager.open({
        file: elfinder_url,
        title: 'elFinder 2.0',
        width: 900,
        height: 450,
        resizable: 'yes',
        inline: 'yes',    // This parameter only has an effect if you use the inlinepopups plugin!
        popup_css: false, // Disable TinyMCE's default popup CSS
        close_previous: 'no'
      }, {
        window: win,
        input: field_name
      });
      return false;
    }			",CClientScript::POS_END);
        }
    */

    /**
     * Renders a checkbox list for a model attribute.
     * This method is a wrapper of {@link GxHtml::activeCheckBoxList}.
     * #MethodTracker
     * This method is based on {@link CActiveForm::checkBoxList}, from version 1.1.7 (r3135). Changes:
     * <ul>
     * <li>Uses GxHtml.</li>
     * </ul>
     * @see CActiveForm::checkBoxList
     * @param CModel $model The data model.
     * @param string $attribute The attribute.
     * @param array $data Value-label pairs used to generate the check box list.
     * @param array $htmlOptions Addtional HTML options.
     * @return string The generated check box list.
     */
    public function checkBoxList($model, $attribute, $data, $htmlOptions = array()) {
        $html = GxHtml::activeCheckBoxList($model, $attribute, $data, $htmlOptions);
        $html = preg_replace("%<input.*?</label>%", "<label class='checkbox'>$0</label>", $html);
        $html = str_replace("<br/>", "", $html);

        return $html;
    }

}