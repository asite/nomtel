<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle);?></title>
    <?php Yii::app()->clientScript->registerCssFile('/static/style.css'); ?>
</head>
<body>
	<div class="content">
		<div class="inner_container cfix">
            <div class="menuLeft well">
              <?php
                if (isAdmin()) {
                  $menuLeft = array(
                    array('label'=>Yii::app()->name, 'url'=>$this->createUrl('site/index'),'active'=>$this->route=='site/index'),
                    '',
                    array('label'=>Yii::t('app','MenuSim'), 'itemOptions'=>array('class'=>'nav-header')),
                    array('label'=>Yii::t('app','addDeliveryReport'), 'url'=>$this->createUrl('sim/delivery'),'active'=>$this->route=='sim/delivery'),
                    array('label'=>Yii::t('app','addSim'), 'url'=>$this->createUrl('sim/add'),'active'=>$this->route=='sim/add'),
                    array('label'=>Yii::t('app','Sim List'), 'url'=>$this->createUrl('sim/list'),'active'=>$this->route=='sim/list'),
                    array('label'=>Yii::t('app','Mass select'), 'url'=>$this->createUrl('sim/massselect'),'active'=>$this->route=='sim/massselect'),
                    '',
                    array('label'=>Yii::t('app','Agents'), 'url'=>$this->createUrl('agent/admin'),'active'=>$this->route=='agent/admin'),
                    array('label'=>Yii::t('app','Load Bonuses'), 'url'=>$this->createUrl('bonusReport/load'),'active'=>$this->route=='bonusReport/load'),
                    array('label'=>BonusReport::model()->label(2), 'url'=>$this->createUrl('bonusReport/list'),'active'=>$this->route=='bonusReport/list'),
                    array('label'=>DeliveryReport::model()->label(2), 'url'=>$this->createUrl('deliveryReport/list'),'active'=>$this->route=='deliveryReport/list'),
                    array('label'=>Yii::t('app','Operators'), 'url'=>$this->createUrl('operator/admin'),'active'=>$this->route=='operator/admin'),
                    '',
                    array('label'=>Yii::t('app','messages'), 'itemOptions'=>array('class'=>'nav-header')),
                    //array('label'=>Yii::t('app','message to agent up'), 'url'=>$this->createUrl('message/agentUp'),'active'=>$this->route=='message/agentUp'),
                    array('label'=>Yii::t('app','message to agent down'), 'url'=>$this->createUrl('message/agentDown'),'active'=>$this->route=='message/agentDown'),
                    '',
                    array('label'=>Yii::t('app','Logout'), 'url'=>$this->createUrl('site/logout')),
                  );
                } else {
                  $menuLeft = array(
                    array('label'=>Yii::app()->name, 'url'=>$this->createUrl('site/index'),'active'=>$this->route=='site/index'),
                    '',
                    array('label'=>Yii::t('app','My Profile'), 'url'=>$this->createUrl('agent/view',array('id'=>loggedAgentId())),'active'=>$this->route=='agent/view'),
                    array('label'=>Yii::t('app','Agents'), 'url'=>$this->createUrl('agent/admin'),'active'=>$this->route=='agent/admin'),
                    array('label'=>Yii::t('app','Sim List'), 'url'=>$this->createUrl('sim/list'),'active'=>$this->route=='sim/list'),
                      array('label'=>BonusReport::model()->label(2), 'url'=>$this->createUrl('bonusReport/list'),'active'=>$this->route=='bonusReport/list'),
                    array('label'=>DeliveryReport::model()->label(2), 'url'=>$this->createUrl('deliveryReport/list'),'active'=>$this->route=='deliveryReport/list'),
                    '',
                    array('label'=>Yii::t('app','messages'), 'itemOptions'=>array('class'=>'nav-header')),
                    array('label'=>Yii::t('app','message to agent up'), 'url'=>$this->createUrl('message/agentUp'),'active'=>$this->route=='message/agentUp'),
                    array('label'=>Yii::t('app','message to agent down'), 'url'=>$this->createUrl('message/agentDown'),'active'=>$this->route=='message/agentDown'),
                    '',
                    array('label'=>Yii::t('app','Logout'), 'url'=>$this->createUrl('site/logout')),
                  );
                }

                $this->widget('bootstrap.widgets.TbMenu', array(
                    'type'=>'list',
                    'items' => $menuLeft,
                ));
              ?>
            </div>
            <div class="contentRight">


			<?php
            /*
if (isAdmin()) {
$menu=array(
    array(
        'class'=>'bootstrap.widgets.TbMenu',
        'items'=>array(
            array(
                'label'=>Yii::t('app','MenuSim'),
                'url'=>'#',
                'items'=>
                array(
                    array('label'=>Yii::t('app','addDeliveryReport'), 'url'=>$this->createUrl('sim/delivery')),
                    array('label'=>Yii::t('app','addSim'), 'url'=>$this->createUrl('sim/add')),
                    array('label'=>Yii::t('app','Sim List'), 'url'=>$this->createUrl('sim/list')),
                    array('label'=>Yii::t('app','Mass select'), 'url'=>$this->createUrl('sim/massselect'))
                )
            )
        )
    ),
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
                'label'=>Yii::t('app','Load Bonuses'),
                'url'=>$this->createUrl('bonus/load')
            )
        )
    ),
    array(
        'class'=>'bootstrap.widgets.TbMenu',
        'items'=>array(
            array(
                'label'=>DeliveryReport::model()->label(2),
                'url'=>$this->createUrl('deliveryReport/list')
            )
        )
    ),
    array(
        'class'=>'bootstrap.widgets.TbMenu',
        'items'=>array(
            array(
                'label'=>Yii::t('app','Operators'),
                'url'=>$this->createUrl('operator/admin')
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
);
} else {
    $menu=array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                array(
                    'label'=>Yii::t('app','My Profile'),
                    'url'=>$this->createUrl('agent/view',array('id'=>loggedAgentId()))
                )
            )
        ),
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
                    'label'=>Yii::t('app','Sim List'),
                    'url'=>$this->createUrl('sim/list'))
                )
        ),
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                array(
                    'label'=>DeliveryReport::model()->label(2),
                    'url'=>$this->createUrl('deliveryReport/list')
                )
            )
        ),
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                array(
                    'label'=>Yii::t('app','messages'),
                    'url'=>'#',
                    'items'=>
                    array(
                        array('label'=>Yii::t('app','message to agent up'), 'url'=>$this->createUrl('message/agentUp')),
                        array('label'=>Yii::t('app','message to agent down'), 'url'=>$this->createUrl('message/agentDown'))
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
    );

}

            $this->widget('bootstrap.widgets.TbNavbar', array(
				'fixed'=>false,
                'type'=>'inverse',
				'brand'=>Yii::app()->name,
				'brandUrl'=>$this->createUrl('site/index'),
				'collapse'=>true,
				'items'=> $menu
			));
        */
            ?>

            <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
				'homeLink'=>CHtml::link(Yii::t('app','Home'),$this->createUrl('site/index')),
				'links'=>$this->breadcrumbs));
			?>

            <?php
            $this->widget('bootstrap.widgets.TbAlert', array(
                'block'=>true, // display a larger alert block?
                'fade'=>true, // use transitions?
                'closeText'=>'×', // close link text - if set to false, no close link is displayed
                'alerts'=>array( // configurations per alert type
                    'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
                    'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
                ),
            ));

            ?>

            <?php echo $content;?>
            </div>
		</div>
	</div>
</body>
</html>
