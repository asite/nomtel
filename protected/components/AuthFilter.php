<?php

class AuthFilter extends CFilter {

	protected function preFilter($filterChain) {
		if (Yii::app()->user->isGuest) {
			Yii::app()->request->redirect(Yii::app()->createUrl('site/login'));
			return false;
		}

		return true;
	}

}