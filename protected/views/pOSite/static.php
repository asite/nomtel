<?  $value = "html_$page"; ?>

<h2><?php echo Yii::t('app', $page); ?></h2>


<div class="number_info">
     <? echo $sim->operator->$value?>
</div>

<?php if ($page=='internet'): ?>
    <a href="<?php echo $this->createUrl('pOSite/internet',array('type'=>'connect')); ?>" class="btn btn-success"><?php echo Yii::t('app','enable internet'); ?></a>
    <a href="<?php echo $this->createUrl('pOSite/internet',array('type'=>'disconnect')); ?>" class="btn btn-danger" style="margin-left: 40px;"><?php echo Yii::t('app','disable internet'); ?></a>
<?php endif; ?>