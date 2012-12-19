<?php

class AuthFilter extends CFilter {

	protected function preFilter($filterChain) {
		if (Yii::app()->user->isGuest) {
			Yii::app()->request->redirect(Yii::app()->createUrl('site/login'));
			return false;
		}

        if (!Yii::app()->user->getState('isAdmin') &&
            !preg_match('%^(site/|agent/|deliveryReport/(list|view|report)|sim/)%',Yii::app()->controller->route)) {
            Yii::app()->request->redirect(Yii::app()->createUrl('site/index'));
            return false;
        }

		return true;
	}

}