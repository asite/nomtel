<?php

Yii::import('application.models._base.BaseNumber');

class Number extends BaseNumber
{
    const STATUS_UNKNOWN = 'UNKNOWN';
    const STATUS_FREE = 'FREE';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_SOLD = 'SOLD';

    const SUPPORT_SMS_STATUS_OFFICE = 'OFFICE';
    const SUPPORT_SMS_STATUS_EMAIL = 'EMAIL';
    const SUPPORT_SMS_STATUS_LK = 'LK';

    const BALANCE_STATUS_CHANGING = 'CHANGING';
    const BALANCE_STATUS_NOT_CHANGING = 'NOT_CHANGING';
    const BALANCE_STATUS_NOT_CHANGING_PLUS = 'NOT_CHANGING_PLUS';
    const BALANCE_STATUS_NO_DATA = 'NO_DATA';
    const BALANCE_STATUS_CLOSED = 'CLOSED';

    const SUPPORT_STATUS_UNAVAILABLE='UNAVAILABLE';
    const SUPPORT_STATUS_CALLBACK='CALLBACK';
    const SUPPORT_STATUS_REJECT='REJECT';
    const SUPPORT_STATUS_ACTIVE='ACTIVE';
    const SUPPORT_STATUS_PREACTIVE='PREACTIVE';
    const SUPPORT_STATUS_SERVICE_INFO='SERVICE_INFO';
    const SUPPORT_STATUS_HELP='HELP';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function recalcNumberBalance($number) {
        $balances=Yii::app()->db->createCommand("
            select brn.balance
            from balance_report br
            left outer join balance_report_number brn on (br.id=brn.balance_report_id and brn.number_id=:number_id)
            order by br.dt desc limit 1,14
        ")->queryColumn(array(':number_id'=>$number->id));

        $missing=0;
        foreach($balances as $balance)
            if ($balance==null) $missing++;else break;

        if ($missing>=10) {
            if ($number->balance_status!=Number::BALANCE_STATUS_CLOSED) {
                $number->balance_status=Number::BALANCE_STATUS_CLOSED;
                $number->balance_status_changed_dt=new EDateTime();
            }
            return;
        }

        if ($missing>=3) {
            if ($number->balance_status!=Number::BALANCE_STATUS_NO_DATA) {
                $number->balance_status=Number::BALANCE_STATUS_NO_DATA;
                $number->balance_status_changed_dt=new EDateTime();
            }
            return;
        }

        $prevBalance=null;
        $seenBalances=0;
        $changing=false;
        foreach($balances as $balance) {
            if ($balance==null) continue;
            if ($prevBalance!=null) {
                if (abs($balance-$prevBalance)>1e-6) $changing=true;
            }
            $seenBalances++;
            $prevBalance=$balance;
            if ($seenBalances==7) break;
        }

        if ($changing) {
            if ($number->balance_status!=Number::BALANCE_STATUS_CHANGING) {
                $number->balance_status=Number::BALANCE_STATUS_CHANGING;
                $number->balance_status_changed_dt=new EDateTime();
            }
        } else {
            if ($prevBalance>1e-6) {
                if ($number->balance_status!=Number::BALANCE_STATUS_NOT_CHANGING_PLUS) {
                    $number->balance_status=Number::BALANCE_STATUS_NOT_CHANGING_PLUS;
                    $number->balance_status_changed_dt=new EDateTime();
                }
            } else {
                if ($number->balance_status!=Number::BALANCE_STATUS_NOT_CHANGING) {
                    $number->balance_status=Number::BALANCE_STATUS_NOT_CHANGING;
                    $number->balance_status_changed_dt=new EDateTime();
                }
            }
        }
    }

    public static function checkNoDataAndClosedBalances() {
        $trx=Yii::app()->db->beginTransaction();
        $ids=Yii::app()->db->createCommand("
                select number_id
                from (
                    select brn.number_id,max(br.dt) as dt
                    from balance_report br
                    join balance_report_number brn on (brn.balance_report_id=br.id)
                    join number n on (n.id=brn.number_id and n.balance_status!='CLOSED')
                    where br.dt>DATE_SUB(NOW(),INTERVAL 1 MONTH)
                    group by brn.number_id
                ) as mytab
                where dt<DATE_SUB(NOW(),INTERVAL 11 DAY)
        ")->queryColumn();

        if (!empty($ids))
            Yii::app()->db->createCommand("
                update number
                set balance_status='CLOSED',balance_status_changed_dt=NOW()
                where id in (".implode(',',$ids).")
            ")->execute();

        $ids=Yii::app()->db->createCommand("
                        select number_id
                from (
                    select brn.number_id,max(br.dt) as dt
                    from balance_report br
                    join balance_report_number brn on (brn.balance_report_id=br.id)
                    join number n on (n.id=brn.number_id and n.balance_status!='NO_DATA')
                    where br.dt>DATE_SUB(NOW(),INTERVAL 1 MONTH)
                    group by brn.number_id
                ) as mytab
                where dt>=DATE_SUB(NOW(),INTERVAL 11 DAY) and dt<DATE_SUB(NOW(),INTERVAL 4 DAY)
        ")->queryColumn();

        if (!empty($ids))
            Yii::app()->db->createCommand("
                update number
                set balance_status='NO_DATA',balance_status_changed_dt=NOW()
                where id in (".implode(',',$ids).")
            ")->execute();

        $trx->commit();

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
        $user->last_password_restore=new EDateTime();
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
                self::STATUS_BLOCKED=>Yii::t('app','BLOCKED'),
                self::STATUS_SOLD=>Yii::t('app','SOLD')
            );
        }

        return $labels;
    }

    public static function getSupportSMSStatusDropDownList($items=array()) {
        $labels=self::getSupportSMSStatusLabels();
        return array_merge($items,$labels);
    }

    public static function getSupportSMSStatusLabels() {
        static $labels;

        if (!$labels) {
            $labels=array(
                self::SUPPORT_SMS_STATUS_OFFICE=>Yii::t('app','OFFICE'),
                self::SUPPORT_SMS_STATUS_EMAIL=>Yii::t('app','EMAIL'),
                self::SUPPORT_SMS_STATUS_LK=>Yii::t('app','LK')
            );
        }

        return $labels;
    }

    public function getSupportSMSStatusLabel($status) {
        $labels=self::getSupportSMSStatusLabels();
        return $labels[$status];
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
                self::SUPPORT_STATUS_REJECT=>'Отказ в регистрации',
                self::SUPPORT_STATUS_PREACTIVE=>'Получены данные',
                self::SUPPORT_STATUS_ACTIVE=>'Получены сканы',
                self::SUPPORT_STATUS_SERVICE_INFO=>'Сервисная информация',
                self::SUPPORT_STATUS_HELP=>'HELP'
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
                self::BALANCE_STATUS_CHANGING => Yii::t('app','BALANCE_STATUS_CHANGING'),
                self::BALANCE_STATUS_NOT_CHANGING => Yii::t('app','BALANCE_STATUS_NOT_CHANGING'),
                self::BALANCE_STATUS_NOT_CHANGING_PLUS => Yii::t('app','BALANCE_STATUS_NOT_CHANGING_PLUS'),
                self::BALANCE_STATUS_NO_DATA => Yii::t('app','BALANCE_STATUS_NO_DATA'),
                self::BALANCE_STATUS_CLOSED => Yii::t('app','BALANCE_STATUS_CLOSED'),
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
           WHEN '".self::BALANCE_STATUS_CHANGING."' THEN 0
           WHEN '".self::BALANCE_STATUS_NOT_CHANGING."' THEN 1
           WHEN '".self::BALANCE_STATUS_NO_DATA."' THEN 2
           WHEN '".self::BALANCE_STATUS_CLOSED."' THEN 3
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
    }
}