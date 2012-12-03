<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{CHtml::encode($this->pageTitle)}</title>
</head>
<body>
	<div class="container">
		<div class="inner_container">
			{$d=$this->widget('bootstrap.widgets.TbNavbar', [
				'fixed'=>false,
                'type'=>'inverse',
				'brand'=>Yii::app()->name,
				'brandUrl'=>$this->createUrl('site/index'),
				'collapse'=>true,
				'items'=> [
                    [
                        'class'=>'bootstrap.widgets.TbMenu',
                        'htmlOptions'=>['class'=>'pull-right'],
                        'items'=>[
                            [
                                'label'=>Yii::t('app','Logout'),
                                'url'=>$this->createUrl('site/logout')
                            ]
                        ]
                    ]
                ]
			])}

			{$d=$this->widget('bootstrap.widgets.TbBreadcrumbs', [
				'homeLink'=>CHtml::link(Yii::t('app','Home'),$this->createUrl('site/index')),
				'links'=>$this->breadcrumbs])
			}

			{$content}
		</div>
	</div>
</body>
</html>
