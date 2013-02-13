<?php

Yii::import('application.models._base.BaseNumber');

class Number extends BaseNumber
{
    const STATUS_UNKNOWN = 'UNKNOWN';
    const STATUS_FREE = 'FREE';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_BLOCKED = 'BLOCKED';

    const BALANCE_STATUS_NORMAL = 'NORMAL';
    const BALANCE_STATUS_POSITIVE_STATIC = 'POSITIVE_STATIC';
    const BALANCE_STATUS_NEGATIVE_STATIC = 'NEGATIVE_STATIC';
    const BALANCE_STATUS_POSITIVE_DYNAMIC = 'POSITIVE_DYNAMIC';
    const BALANCE_STATUS_NEGATIVE_DYNAMIC = 'NEGATIVE_DYNAMIC';
    const BALANCE_STATUS_NEW = 'NEW';
    const BALANCE_STATUS_MISSING = 'MISSING';

    const SUPPORT_STATUS_UNAVAILABLE='UNAVAILABLE';
    const SUPPORT_STATUS_CALLBACK='CALLBACK';
    const SUPPORT_STATUS_REJECT='REJECT';
    const SUPPORT_STATUS_ACTIVE='ACTIVE';
    const SUPPORT_STATUS_PREACTIVE='PREACTIVE';
    const SUPPORT_STATUS_SERVICE_INFO='SERVICE_INFO';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getNumberFromFormatted($formattedNumber) {
        $formattedNumber=preg_replace('%[^0-9]%','',$formattedNumber);
        $formattedNumber=preg_replace('%^8%','',$formattedNumber);
        return $formattedNumber;
    }

    public function getFormattedNumber() {
        return preg_replace('%^(\d\d\d)(\d\d\d)(\d\d)(\d\d)$%','8 ($1) $2-$3-$4',$this->number);
    }

    public function restorePassword() {
        $user=$this->user;
        if (!$user) {
            $user=new User();
            $user->username=$this->number;
            $user->status=ModelLoggableBehavior::STATUS_ACTIVE;
            $user->save();
            $this->user_id=$user->id;
            $this->save();
        }
        $password=rand(100000,999999);
        $user->password=$password;
        $user->encryptPwd();
        $user->save();
        Sms::send($this->number,"Ваш новый пароль для личного кабинета $password, личный кабинет находится по адресу www.500099.ru");
    }

    public static function getStatusDropDownList($items=array()) {
        $labels=self::getStatusLabels();
        return array_merge($items,$labels);
    }

    public static function getStatusLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::STATUS_UNKNOWN=>Yii::t('app','UNKNOWN'),
                self::STATUS_FREE=>Yii::t('app','FREE'),
                self::STATUS_ACTIVE=>Yii::t('app','ACTIVE'),
                self::STATUS_BLOCKED=>Yii::t('app','BLOCKED')
            );
        }

        return $labels;
    }

    public function getStatusLabel($status) {
        $labels=self::getStatusLabels();
        return $labels[$status];
    }

    public static function getSupportStatusDropDownList($items=array()) {
        $labels=self::getSupportStatusLabels();
        return array_merge($items,$labels);
    }

    public static function getSupportStatusLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::SUPPORT_STATUS_UNAVAILABLE=>'Недоступен',
                self::SUPPORT_STATUS_CALLBACK=>'Перезвонить',
                self::SUPPORT_STATUS_REJECT=>'Отказ по телефону',
                self::SUPPORT_STATUS_PREACTIVE=>'Получены данные',
                self::SUPPORT_STATUS_ACTIVE=>'Получены сканы',
                self::SUPPORT_STATUS_SERVICE_INFO=>'Сервисная информация'
            );
        }

        return $labels;
    }

    public function getSupportStatusLabel($status) {
        $labels=self::getSupportStatusLabels();
        return $labels[$status];
    }

    public static function getBalanceStatusDropDownList($items=array()) {
        $labels=self::getBalanceStatusLabels();
        return array_merge($items,$labels);
    }

    public static function getBalanceStatusLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::BALANCE_STATUS_NORMAL => Yii::t('app','BALANCE_STATUS_NORMAL'),
                self::BALANCE_STATUS_POSITIVE_STATIC => Yii::t('app','BALANCE_STATUS_POSITIVE_STATIC'),
                self::BALANCE_STATUS_NEGATIVE_STATIC => Yii::t('app','BALANCE_STATUS_NEGATIVE_STATIC'),
                self::BALANCE_STATUS_POSITIVE_DYNAMIC => Yii::t('app','BALANCE_STATUS_POSITIVE_DYNAMIC'),
                self::BALANCE_STATUS_NEGATIVE_DYNAMIC => Yii::t('app','BALANCE_STATUS_NEGATIVE_DYNAMIC'),
                self::BALANCE_STATUS_NEW => Yii::t('app','BALANCE_STATUS_NEW'),
                self::BALANCE_STATUS_MISSING => Yii::t('app','BALANCE_STATUS_MISSING')
            );
        }

        return $labels;
    }

    public function getBalanceStatusLabel($status) {
        $labels=self::getBalanceStatusLabels();
        return $labels[$status];
    }

    public function getBalanceStatusOrder() {
        return "(CASE balance_status
           WHEN '".self::BALANCE_STATUS_POSITIVE_DYNAMIC."' THEN 0
           WHEN '".self::BALANCE_STATUS_POSITIVE_STATIC."' THEN 1
           WHEN '".self::BALANCE_STATUS_NEGATIVE_DYNAMIC."' THEN 2
           WHEN '".self::BALANCE_STATUS_NEGATIVE_STATIC."' THEN 3
           WHEN '".self::BALANCE_STATUS_MISSING."' THEN 4
           WHEN '".self::BALANCE_STATUS_NORMAL."' THEN 5
           WHEN '".self::BALANCE_STATUS_NEW."' THEN 6
        END)";
    }

    public function addNumber($sim) {
        if ($sim->number) {
            $number = self::model()->findByAttributes(array('number'=>$sim->number));
            if (empty($number)) {
                $number = new Number;
                $number->sim_id = $sim->id;
                $number->number = $sim->number;
                $number->personal_account = $sim->personal_account;
                $number->status = self::STATUS_FREE;
                $number->save();
            }
            if ($number->status!=self::STATUS_FREE) {
                $number->status=self::STATUS_FREE;
                $number->save();
            }
            NumberHistory::addHistoryNumber($number->id,'SIM {Sim:'.$sim->id.'} добавлена в базу');
        }
    }

    public function getSupportStatusArray() {
        return array(
            self::SUPPORT_STATUS_UNAVAILABLE=>0,
            self::SUPPORT_STATUS_CALLBACK=>0,
            self::SUPPORT_STATUS_REJECT=>0,
            self::SUPPORT_STATUS_ACTIVE=>0,
            self::SUPPORT_STATUS_SERVICE_INFO=>0
        );
    }
}