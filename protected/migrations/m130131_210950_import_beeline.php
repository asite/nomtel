<?php

class m130131_210950_import_beeline extends CDbMigration
{
    public function readCSV($filename) {
        $csv=array();
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000)) !== FALSE) {
                $csv[]=$data;
            }
            fclose($handle);
        }
        return $csv;
    }

	public function safeUp()
	{
        $csv=$this->readCSV(dirname(__FILE__).'/'.preg_replace('%\.php$%','.csv',basename(__FILE__)));
        array_shift($csv);

        $tariffs=array();
        foreach(Tariff::model()->findAllByAttributes(array('operator_id'=>Operator::OPERATOR_BEELINE_ID)) as $tariff)
            $tariffs[$tariff->title]=$tariff->id;

        $tariffMap=array(
            '08INTERN'=>'Интернациональный 2012',
            '08STAND'=>'Неактивированный тариф',
            '08WELN'=>'Урал Добро пожаловать 2008',
            '72_VSE_L'=>'Всё включено L 2012',
            '72_VSE_XL'=>'Всё включено XL 2012',
            '72ALLRUS'=>'ТЮМ Вся Россия',
            '72ALLRUSN'=>'ТЮМ Вся Россия+',
            '72FLAME'=>'Зажигай',
            '72GO'=>'Go! 2012',
            '72INCL'=>'ТЮМ Все включено Л 2011',
            '72INCUNL'=>'ТЮМ Все включено Безлимит 2011',
            '72INCUNLN'=>'ТЮМ Все включено Безлимит',
            '72INCXL'=>'ТЮМ Все включено ХЛ 2011',
            '72LIDER'=>'Лидер Общения',
            '72MONSTER'=>'ТЮМ Монстр общения 2011',
            '72MONSTR'=>'ТЮМ Монстр общения',
            '72NODOUBT'=>'ТЮМ Ноль сомнений',
            '72URAL'=>'ТЮМ Уральский',
            '72VSE_XXL'=>'Все включено XXL 2012',
            '72WORLD'=>'ТЮМ Мир Билайн + ф',
            '72WORLDN'=>'ТЮМ Мир Билайн + ф',
            '72VISISNG'=>'Содружество СНГ',
            '72BIZPART'=>'Бизнес-партнеры',
            'TUM_PPGO'=>'TUM_PPGO',
        );

        $stat=array('cnt'=>0);
        foreach($csv as $row) {
            $csv_number=trim($row[0]);
            $csv_tariff=trim($row[1]);
            $csv_status=trim($row[5]);
            $csv_code_word=trim($row[6]);


            $tariff=$tariffs[$tariffMap[$csv_tariff]];
            if (!$tariff) {
                var_dump($row);
                echo __LINE__."\n";
                die;
            }

            $number=new Number();
            $number->number=$csv_number;

            switch ($csv_status) {
                case 'Свободный':
                    $number->status=Number::STATUS_FREE;
                    break;
                default:
                    var_dump($row);
                    echo __LINE__."\n";
                    die;
            }

            $sim=new Sim();
            $sim->save();
            $sim->parent_id=$sim->id;
            $sim->operator_id=Operator::OPERATOR_BEELINE_ID;

            $sim->tariff_id=$tariff;
            $sim->company_id=1; // Матави
            $sim->number=$csv_number;
            $sim->parent_agent_id=1;
            $sim->number_price=0;
            $sim->sim_price=100;
            $sim->save();
            $number->codeword=$csv_code_word;
            $number->sim_id=$sim->id;

            $number->save();

            NumberHistory::addHistoryNumber($number->id,'SIM {Sim:'.$sim->id.'} добавлена в базу','База');

            $stat['cnt']++;
            if ($stat['cnt']%1000==0) {
                echo "processed {$stat['cnt']} of ".count($csv)."\n";
            }
        }
        var_dump($stat);
	}

	public function safeDown()
	{
		echo "m130131_210950_import_beeline does not support migration down.\n";
		return false;
	}
}