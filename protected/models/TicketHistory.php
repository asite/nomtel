<?php

Yii::import('application.models._base.BaseTicketHistory');

class TicketHistory extends BaseTicketHistory
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function getStatusDropDownList($items=array()) {
        return Ticket::getStatusDropDownList($items);
    }

    public static function getStatusLabels() {
        return Ticket::getStatusLabels();
    }

    public static function getStatusLabel($status) {
        return Ticket::getStatusLabel($status);
    }

    public function search()
    {
        $data_provider = parent::search();

        $data_provider->setSort(array(
            'defaultOrder' => 'id asc'
        ));
        return $data_provider;
    }

}