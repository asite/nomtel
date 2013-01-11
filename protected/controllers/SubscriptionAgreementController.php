<?php

class SubscriptionAgreementController extends BaseGxController {

    public function actionCreate($sim_id) {

        $sim=$this->loadModel($sim_id,'Sim');
        $this->checkPermissions('createSubscriptionAgreement',array('sim'=>$sim));

        $agreement=new SubscriptionAgreement();

        $this->render('create',array(
            'sim'=>$sim,
            'agreement'=>$agreement
        ));
    }
}