<?php

Yii::import('application.models._base.BaseTariff');

class Tariff extends BaseTariff
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function search() {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'title asc'
        ));
        return $data_provider;
    }
}