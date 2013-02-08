<?php

$this->breadcrumbs = array(
    Yii::t('app','Creating subscription agreement')
);

?>

<h1><?php echo Yii::t('app','Creating subscription agreement'); ?></h1>

<?php
$this->renderPartial('_form', array(
    'sim'=>$sim,
    'agreement'=>$agreement,
    'person'=>$person,
    'checkPassport'=>true
));
?>