<?php
class DocumentGenerator extends PHPWord
{

    public static function generate($template,$filename,$data,$storeToDisk=false) {
        // disable web logging
        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute || $route instanceof CProfileLogRoute) {
                $route->enabled = false;
            }
        }
        Yii::app()->db->enableProfiling = false;

        $PHPWord = new self();

        $tempFileName=tempnam(Yii::getPathOfAlias('webroot.var.temp'),'phpword_');

        // open file in temp directory, phpword is creating temporary file in document folder
        copy(Yii::getPathOfAlias('application.data').'/'.$template,$tempFileName);
        $document = $PHPWord->loadTemplate($tempFileName);

        // assign variable values
        foreach($data as $key=>$val)
            if ($val instanceof CActiveRecord) {
                foreach($val->attributes as $attrName=>$attrVal)
                    $document->setValue($key.'_'.$attrName,$attrVal);
            } else {
                $document->setValue($key,$val);
            }

        // delete unitialized variables
        foreach($document->getVariables() as $key)
            $document->setValue($key,'');

        if (!$storeToDisk) {
            $document->save($tempFileName);

            $file=file_get_contents($tempFileName);

            unlink($tempFileName);

            header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Pragma: private');
            header('Content-Disposition: attachment; filename="'.$filename.'"');

            echo $file;
            Yii::app()->end();
        } else {
            $fullFileName=Yii::getPathOfAlias('webroot.var.temp').'/'.$filename;
            $document->save($fullFileName);
            return $fullFileName;
        }
    }

}
