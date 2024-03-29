<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle);?></title>
    <?php Yii::app()->clientScript->registerCssFile('/static/style.css'); ?>
    <script type="text/javascript" src="/static/script.js"></script>
</head>
<body>
<div class="content">
<div class="inner_container cfix">
<div class="menuLeft well">
    <?php

    if (Yii::app()->user->role == 'agent') {
        $menuLeft = array(
            array('label' => Yii::app()->name, 'url' => $this->createUrl('site/index'), 'active' => $this->route == 'site/index'),
            '',
            array('label' => Yii::t('app', 'My Profile'), 'url' => $this->createUrl('agent/view', array('id' => loggedAgentId())), 'active' => $this->route == 'agent/view'),
            array('label' => Yii::t('app', 'Agents'), 'url' => $this->createUrl('agent/admin'), 'active' => ($this->id == 'agent' && $this->route != 'agent/view'), 'visible'=>isFlag()),
        );
        if (isKrylow()) {
            $menuLeft=array_merge($menuLeft,array(
                array('label' => Yii::t('app', 'addAct'), 'url' => $this->createUrl('sim/delivery'), 'active' => $this->route == 'sim/delivery'),
                array('label' => Yii::t('app', 'addSim'), 'url' => $this->createUrl('sim/add'), 'active' => $this->route == 'sim/add'),
            ));
        }
        $menuLeft=array_merge($menuLeft,array(
            array('label' => Yii::t('app', 'Sim List'), 'url' => $this->createUrl('sim/list'), 'active' => $this->route == 'sim/list'),
            array('label' => Yii::t('app', 'Mass select'), 'url' => $this->createUrl('sim/massselect'), 'active' => $this->route == 'sim/massselect', 'visible'=>isFlag()),
            array('label' => Yii::t('app', 'Support'), 'url' => $this->createUrl('support/number'), 'active' => $this->route == 'support/number'),
            array('label' => BonusReport::model()->label(2), 'url' => $this->createUrl('bonusReport/list'), 'active' => $this->route == 'bonusReport/list', 'visible'=>isFlag('is_bonus')),
            //array('label' => Number::label(2), 'url' => $this->createUrl('number/list'), 'active' => $this->id == 'number'),
            array('label' => 'История отгрузок', 'url' => $this->createUrl('act/list'), 'active' => $this->route == 'act/list', 'visible'=>isFlag()),
            array('label' => 'Сформировать акт', 'url' => $this->createUrl('act/fromParent'), 'active' => $this->route == 'act/fromParent', 'visible'=>isFlag('is_making_parent_invoices')),
            '',
            array('label' => 'Отправка СМС', 'url' => $this->createUrl('sms/send'), 'active' => $this->route == 'sms/send'),
            '',
            array('label' => Yii::t('app', 'Logout').' ('.Yii::app()->user->getState('username').')', 'url' => $this->createUrl('site/logout')),
        ));
    }

    if (preg_match('/^support/',Yii::app()->user->role)) {
        $menuLeft = array(
            array('label' => Number::label(2), 'url' => $this->createUrl('support/numberStatus'), 'active' => $this->route == 'support/numberStatus'),
            array('label' => Yii::t('app','Call back'), 'url' => $this->createUrl('support/callback'), 'active' => $this->route == 'support/callback'),
            array('label' => Yii::t('app','Numbers for Approve'), 'url' => $this->createUrl('support/numberForApprove'), 'active' => $this->route == 'support/numberForApprove'),
            array('label' => Yii::t('app','Operator Numbers'), 'url' => $this->createUrl('support/numberForCalls'), 'active' => $this->route == 'support/numberForCalls'),
            array('label' => Yii::t('app','Statistic'), 'url' => $this->createUrl('support/statistic'), 'active' => $this->route == 'support/statistic'),
            //array('label' => Yii::t('app', 'Sim List'), 'url' => $this->createUrl('sim/list'), 'active' => $this->route == 'sim/list'),
            //array('label' => Number::label(2), 'url' => $this->createUrl('number/list'), 'active' => $this->id == 'number'),
            '',
            array('label' => Yii::t('app', 'Support'), 'url' => $this->createUrl('support/number'), 'active' => $this->route == 'support/number'),
        );

        if (Yii::app()->user->role=='supportAdmin') {
            $menuLeft=array_merge($menuLeft,array(
                '',
                array('label' => 'Список обращений', 'url' => $this->createUrl('ticketAdmin/index'),'active'=>Yii::app()->controller->id=='ticketAdmin'),
                '',
                array('label' => 'Отправка СМС', 'url' => $this->createUrl('sms/send'), 'active' => $this->route == 'sms/send'),
            ));
        }

        if (Yii::app()->user->role=='support') {
            $menuLeft=array_merge($menuLeft,array(
                '',
                array('label' => 'Список обращений', 'url' => $this->createUrl('ticket/index'),'active'=>Yii::app()->controller->id=='ticket'),
                '',
                array('label' => 'Отправка СМС', 'url' => $this->createUrl('sms/send'), 'active' => $this->route == 'sms/send'),
            ));
        }

        if (Yii::app()->user->role=='supportMegafon') {
            $menuLeft=array(
                '',
                array('label' => 'Список обращений', 'url' => $this->createUrl('ticketMegafon/index'),'active'=>$this->route == 'ticketMegafon/index'),
                array('label' => 'История обращений', 'url' => $this->createUrl('ticketMegafon/indexAdmin'),'active'=>$this->route == 'ticketMegafon/indexAdmin'),
                array('label' => 'Список номеров', 'url' => $this->createUrl('supportMegafon/numberList'),'active'=>in_array($this->route,array('supportMegafon/numberList','subscriptionAgreement/update'))),
            );
        }

        if (Yii::app()->user->role=='supportBeeline') {
            $menuLeft=array(
                '',
                array('label' => 'Список номеров', 'url' => $this->createUrl('supportBeeline/numberList'),'active'=>in_array($this->route,array('supportBeeline/numberList'))),
            );
        }

        $menuLeft=array_merge($menuLeft,array(
            '',
            array('label' => Yii::t('app', 'Logout').' ('.Yii::app()->user->getState('username').')', 'url' => $this->createUrl('site/logout')),
        ));
    }

    if (Yii::app()->user->role=='cashier') {
        $menuLeft=array(
            array('label' => Yii::t('app', 'Sell'), 'url' => $this->createUrl('cashier/sellList'),'active'=>preg_match('%^cashier/sell%',$this->route)),
            array('label' => Yii::t('app', 'Service'), 'url' => $this->createUrl('cashier/serviceList'),'active'=>preg_match('%^cashier/service%',$this->route)),
            array('label' => 'Баланс кассы', 'url' => $this->createUrl('cashier/stats'), 'active' => $this->id=='cashier/stats'),
            array('label' => 'Список обращений (Мегафон)', 'url' => $this->createUrl('ticketMegafon/indexAdmin'),'active'=>Yii::app()->controller->id=='ticketMegafon'),
            '',
            array('label' => Yii::t('app', 'Logout').' ('.Yii::app()->user->getState('username').')', 'url' => $this->createUrl('site/logout')),
        );
    }

    if (Yii::app()->user->role == 'number') {
        $menuLeft = array(
            array('label' => Yii::t('app', 'Subscriber data'), 'url' => $this->createUrl('pOSite/index'), 'active' => $this->route == 'pOSite/index'),
            '',
            array('label' => Yii::t('app', 'Your Tariff'), 'url' => $this->createUrl('pOSite/tariff'), 'active' => $this->route == 'pOSite/tariff'),

            array('label' => Yii::t('app', 'news'), 'url' => $this->createUrl('pOSite/static', array('page'=>'news')), 'active' => $_GET['page'] == 'news'),
            array('label' => Yii::t('app', 'commands'), 'url' => $this->createUrl('pOSite/static', array('page'=>'commands')), 'active' => $_GET['page'] == 'commands'),
            array('label' => Yii::t('app', 'internet'), 'url' => $this->createUrl('pOSite/static', array('page'=>'internet')), 'active' => $_GET['page'] == 'internet'),

            '',
            array('label' => Yii::t('app', 'Support'), 'url' => $this->createUrl('pOSupport/index'), 'active' => $this->route == 'pOSupport/index'),
            array('label' => Yii::t('app', 'Support List'), 'url' => $this->createUrl('pOSupport/list'), 'active' => $this->route == 'pOSupport/list'),
            '',
            array('label' => Yii::t('app', 'Order sim'), 'url' => $this->createUrl('pOSite/orderSim'), 'active' => $this->route == 'pOSite/orderSim', 'itemOptions'=>array('class'=>'btn btn-warning user_class')),
            '',
            array('label' => Yii::t('app', 'Logout'), 'url' => $this->createUrl('pOSite/logout')),
        );
    }

    if (Yii::app()->user->role == 'admin' || Yii::app()->user->role == 'supportSuper') {
        $menuLeft = array(
            array('label' => Yii::app()->name, 'url' => $this->createUrl('site/index'), 'active' => $this->route == 'site/index'),
            '',
            array('label' => Yii::t('app', 'My Profile'), 'url' => $this->createUrl('agent/update', array('id' => loggedAgentId())), 'active' => Yii::app()->request->url == $this->createUrl('agent/update', array('id' => loggedAgentId()))),
            array('label' => Yii::t('app', 'MenuSim'), 'itemOptions' => array('class' => 'nav-header')),
            array('label' => Yii::t('app', 'addAct'), 'url' => $this->createUrl('sim/delivery'), 'active' => $this->route == 'sim/delivery'),
            array('label' => Yii::t('app', 'addSim'), 'url' => $this->createUrl('sim/add'), 'active' => $this->route == 'sim/add'),
            array('label' => BlankSim::label(2), 'url' => $this->createUrl('blankSim/admin'), 'active' => $this->id=='blankSim'),
            array('label' => 'Баланс кассы', 'url' => $this->createUrl('cashier/stats'), 'active' => $this->id=='cashier/stats'),
            array('label' => Yii::t('app','Mass change'), 'url' => $this->createUrl('number/massChange'), 'active' => $this->route=='number/massChange'),
            //array('label' => 'Массовое восстановление', 'url' => $this->createUrl('number/bulkRestore'), 'active' => $this->route=='number/bulkRestore'),
            array('label' => Yii::t('app', 'Sim List'), 'url' => $this->createUrl('sim/list'), 'active' => $this->route == 'sim/list'),
            array('label' => Yii::t('app', 'Mass select'), 'url' => $this->createUrl('sim/massselect'), 'active' => $this->route == 'sim/massselect'),
            array('label' => Yii::t('app', 'Mass move'), 'url' => $this->createUrl('sim/massMove'), 'active' => $this->route == 'sim/massmove'),
            '',
            array('label' => SupportOperator::label(2), 'url' => $this->createUrl('supportOperator/admin'), 'active' => $this->id == 'supportOperator'),
            array('label' => Yii::t('app', 'Agents'), 'url' => $this->createUrl('agent/admin'), 'active' => ($this->id == 'agent' && $this->route != 'agent/update')),
            //array('label' => Yii::t('app', 'Load Bonuses'), 'url' => $this->createUrl('bonusReport/load'), 'active' => $this->route == 'bonusReport/load'),
            array('label' => BonusReport::model()->label(2), 'url' => $this->createUrl('bonusReport/list'), 'active' => $this->route == 'bonusReport/list'),
            //array('label' => Yii::t('app', 'Load Balance Report'), 'url' => $this->createUrl('balanceReport/load'), 'active' => $this->route == 'balanceReport/load'),
            array('label' => BalanceReport::model()->label(2), 'url' => $this->createUrl('balanceReport/list'), 'active' => $this->route == 'balanceReport/list'),
            //array('label' => Number::label(2), 'url' => $this->createUrl('number/list'), 'active' => $this->id == 'number'),
            array('label' => 'История отгрузок', 'url' => $this->createUrl('act/list'), 'active' => $this->route == 'act/list'),
            array('label' => Yii::t('app', 'Operators'), 'url' => $this->createUrl('operator/admin'), 'active' => $this->id == 'operator'),
            '',
            array('label' => Yii::t('app', 'Support'), 'url' => $this->createUrl('support/number'), 'active' => $this->route == 'support/number'),
            '',
            array('label' => 'Список обращений (Админ)', 'url' => $this->createUrl('ticketAdmin/index'),'active'=>Yii::app()->controller->id=='ticketAdmin'),
            array('label' => 'Список обращений (Мегафон)', 'url' => $this->createUrl('ticketMegafon/indexAdmin'),'active'=>Yii::app()->controller->id=='ticketMegafon'),
            '',
            array('label' => 'Заявления на восстановление', 'url' => $this->createUrl('megafonAppRestore/list'),'active'=>Yii::app()->controller->id=='megafonAppRestore/list'),
            array('label' => 'Обработка заявления', 'url' => $this->createUrl('megafonAppRestore/process'),'active'=>Yii::app()->controller->id=='megafonAppRestore/process'),
            '',
            array('label' => 'Отправка СМС', 'url' => $this->createUrl('sms/send'), 'active' => $this->route == 'sms/send'),
            '',
            array('label' => Yii::t('app', 'Set Number Region'), 'url' => $this->createUrl('number/setNumberRegion'), 'active' => $this->route == 'number/setNumberRegion'),
            '',
            array('label' => Yii::t('app', 'Logout').' ('.Yii::app()->user->getState('username').')', 'url' => $this->createUrl('site/logout')),
        );
    }

    if (Yii::app()->user->manyRolesAvailable()) {
        $roles=Yii::app()->user->getAvailableRoles();
        $menuRoles=array();
        foreach($roles as $role=>$title)
            if ($role!=Yii::app()->user->getRole())
                $menuRoles[]=array('label'=>$title,'url'=>$this->createUrl('site/changeRole',array('role'=>$role)));

        array_splice($menuLeft,1,0,array(
            array(
                'label'=>$roles[Yii::app()->user->role],
                'items'=>$menuRoles
            ),
        ));
    }
    $this->widget('bootstrap.widgets.TbMenu', array(
        'type' => 'list',
        'items' => $menuLeft,
    ));
    ?>
</div>
<div class="contentRight">

    <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    'homeLink' => CHtml::link(Yii::t('app', 'Home'), $this->createUrl('site/index')),
    'links' => $this->breadcrumbs));
    ?>

    <?php
    $this->widget('bootstrap.widgets.TbAlert', array(
        'block' => true, // display a larger alert block?
        'fade' => true, // use transitions?
        'closeText' => '×', // close link text - if set to false, no close link is displayed
        'alerts' => array( // configurations per alert type
            'success' => array('block' => true, 'fade' => true, 'closeText' => '×'), // success, info, warning, error or danger
            'warning' => array('block' => true, 'fade' => true, 'closeText' => '×'), // success, info, warning, error or danger
            'error' => array('block' => true, 'fade' => true, 'closeText' => '×'), // success, info, warning, error or danger
        ),
    ));

    ?>

    <?php echo $content;?>
</div>
</div>
</div>
<?php  if (Yii::app()->user->role == 'number') {?>
<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = '42083';
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script>
<!-- {/literal} END JIVOSITE CODE -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter20314267 = new Ya.Metrika({id:20314267,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/20314267" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<?php } ?>

<?php include('jivo.php');?>

</body>
</html>
