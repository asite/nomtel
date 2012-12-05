<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle);?></title>
    <?php Yii::app()->clientScript->registerCssFile('/static/style.css'); ?>
</head>
<body>
	<div class="content">
		<div class="inner_container">
			<?php $this->widget('bootstrap.widgets.TbNavbar', array(
				'fixed'=>false,
                'type'=>'inverse',
				'brand'=>Yii::app()->name,
				'brandUrl'=>$this->createUrl('site/index'),
				'collapse'=>true,
				'items'=> array(
                    array(
                        'class'=>'bootstrap.widgets.TbMenu',
                        'items'=>array(
                            array(
                                'label'=>Yii::t('app','Agents'),
                                'url'=>$this->createUrl('agent/admin')
                            )
                        )
                    ),
                    array(
                        'class'=>'bootstrap.widgets.TbMenu',
                        'items'=>array(
                            array(
                              'label'=>Yii::t('app','MenuSim'),
                              'url'=>'#',
                              'items'=>
                                array(
                                  array('label'=>Yii::t('app','addDeliveryReport'), 'url'=>$this->createUrl('sim/delivery')),
                                  array('label'=>Yii::t('app','addSim'), 'url'=>$this->createUrl('sim/add'))
                                )
                            )
                        )
                    ),
                    array(
                        'class'=>'bootstrap.widgets.TbMenu',
                        'htmlOptions'=>array('class'=>'pull-right'),
                        'items'=>array(
                            array(
                                'label'=>Yii::t('app','Logout'),
                                'url'=>$this->createUrl('site/logout')
                            )
                        )
                    )
                )
			)); ?>

            <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
				'homeLink'=>CHtml::link(Yii::t('app','Home'),$this->createUrl('site/index')),
				'links'=>$this->breadcrumbs));
			?>

			<?php echo $content;?>
		</div>
	</div>
</body>
</html>
