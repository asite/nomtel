<?php

class m130307_090207_add_tables_comments extends CDbMigration
{
	public function up()
	{
        $this->execute("
ALTER TABLE `act`
CHANGE `id` `id` int(11) NOT NULL AUTO_INCREMENT FIRST,
CHANGE `agent_id` `agent_id` int(11) NOT NULL COMMENT 'агент, со счета которого списываются средства' AFTER `id`,
CHANGE `type` `type` enum('SIM','NORMAL') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'тип акта: SIM - передача SIM; NORMAL - просто списание средств за что либо' AFTER `agent_id`,
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'время создания акта' AFTER `type`,
CHANGE `sum` `sum` decimal(14,2) NOT NULL COMMENT 'сумма' AFTER `dt`,
CHANGE `comment` `comment` mediumtext COLLATE 'utf8_general_ci' NULL COMMENT 'комментарий' AFTER `sum`,
COMMENT='Хранит акты агентов. Под актом понимается как передача симок агенту, так и просто списание средств за что-либо. Т.е. акт это что-то, приводящее к списанию средств со счета агента';
        ");

        $this->execute("
ALTER TABLE `agent`
CHANGE `parent_id` `parent_id` int(11) NULL COMMENT 'агент - родитель' AFTER `id`,
CHANGE `user_id` `user_id` int(11) NULL COMMENT 'соответствующая агенту запись в таблице пользователей' AFTER `parent_id`,
CHANGE `name` `name` varchar(100) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'имя' AFTER `user_id`,
CHANGE `surname` `surname` varchar(100) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'фамилия' AFTER `name`,
CHANGE `middle_name` `middle_name` varchar(100) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'отчество' AFTER `surname`,
CHANGE `phone_1` `phone_1` varchar(50) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'телефон 1' AFTER `middle_name`,
CHANGE `phone_2` `phone_2` varchar(50) COLLATE 'utf8_general_ci' NULL COMMENT 'телефон 2' AFTER `phone_1`,
CHANGE `phone_3` `phone_3` varchar(50) COLLATE 'utf8_general_ci' NULL COMMENT 'телефон 3' AFTER `phone_2`,
CHANGE `city` `city` varchar(100) COLLATE 'utf8_general_ci' NULL COMMENT 'город' AFTER `phone_3`,
CHANGE `email` `email` varchar(100) COLLATE 'utf8_general_ci' NULL COMMENT 'email' AFTER `city`,
CHANGE `skype` `skype` varchar(100) COLLATE 'utf8_general_ci' NULL COMMENT 'скайп' AFTER `email`,
CHANGE `icq` `icq` varchar(20) COLLATE 'utf8_general_ci' NULL COMMENT 'ася' AFTER `skype`,
CHANGE `passport_series` `passport_series` varchar(10) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'серия паспорта' AFTER `icq`,
CHANGE `passport_number` `passport_number` varchar(20) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'номер паспорта' AFTER `passport_series`,
CHANGE `passport_issue_date` `passport_issue_date` date NOT NULL COMMENT 'дата выдачи паспорта' AFTER `passport_number`,
CHANGE `passport_issuer` `passport_issuer` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'орган, выдавший паспорт' AFTER `passport_issue_date`,
CHANGE `birth_date` `birth_date` date NOT NULL COMMENT 'дата рождения' AFTER `passport_issuer`,
CHANGE `birth_place` `birth_place` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'место рождения' AFTER `birth_date`,
CHANGE `registration_address` `registration_address` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'адрес регистрации' AFTER `birth_place`,
CHANGE `balance` `balance` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'актуальный баланс агента' AFTER `registration_address`,
CHANGE `stat_acts_sum` `stat_acts_sum` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'общее кол-во израсходованных по счету средств за все время' AFTER `balance`,
CHANGE `stat_payments_sum` `stat_payments_sum` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'сумма платежей за все время' AFTER `stat_acts_sum`,
CHANGE `stat_sim_count` `stat_sim_count` int(11) NOT NULL DEFAULT '0' COMMENT 'кол=во сим, переданных агенту за все время' AFTER `stat_payments_sum`,
COMMENT='Хранит данные агентов. База (админ) имеет запись с id 1';
        ");

        $this->execute("
ALTER TABLE `agent_referral_rate`
CHANGE `agent_id` `agent_id` int(11) NOT NULL COMMENT 'агент' AFTER `id`,
CHANGE `operator_id` `operator_id` int(11) NOT NULL COMMENT 'оператор' AFTER `agent_id`,
COMMENT='Хранит бонусные ставки агентов по операторам';
        ");

        $this->execute("
ALTER TABLE `balance_report`
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата загрузки отчета' AFTER `id`,
CHANGE `operator_id` `operator_id` int(11) NOT NULL COMMENT 'оператор, к номерам которого относится отчет' AFTER `dt`,
CHANGE `comment` `comment` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'комментарий' AFTER `operator_id`,
COMMENT='Хранит отчеты по балансам';
        ");

        $this->execute("
ALTER TABLE `balance_report_number`
CHANGE `balance_report_id` `balance_report_id` int(11) NOT NULL COMMENT 'отчет' AFTER `id`,
CHANGE `number_id` `number_id` bigint(20) NOT NULL COMMENT 'номер' AFTER `balance_report_id`,
COMMENT='Хранит балансы номеров для бонусных отчетов';
        ");

        $this->execute("
ALTER TABLE `bonus_report`
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата загрузки отчета' AFTER `id`,
CHANGE `operator_id` `operator_id` int(11) NOT NULL COMMENT 'оператор, к номерам которого относится отчет' AFTER `dt`,
CHANGE `comment` `comment` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'комментарий' AFTER `operator_id`,
COMMENT='Хранит бонусные отчеты';
        ");

        $this->execute("
ALTER TABLE `bonus_report_agent`
CHANGE `bonus_report_id` `bonus_report_id` int(11) NOT NULL COMMENT 'бонусный отчет' AFTER `id`,
CHANGE `agent_id` `agent_id` int(11) NOT NULL COMMENT 'агент' AFTER `bonus_report_id`,
CHANGE `sim_count` `sim_count` int(11) NOT NULL COMMENT 'кол-во симок в отчете, которые проходили через агента' AFTER `agent_id`,
CHANGE `sum` `sum` decimal(14,2) NOT NULL COMMENT 'сумма, которая причитается агенту по данному бонусному отчету' AFTER `sim_count`,
CHANGE `sum_referrals` `sum_referrals` decimal(14,2) NOT NULL COMMENT 'сумма, которую агент должен выплатить своим реферралам. Выручка агента равна sum-sum_referrals' AFTER `sum`,
CHANGE `payment_id` `payment_id` int(11) NULL COMMENT 'платеж, по которому агенту были начислены средства на баланс по бонусному отчету' AFTER `sum_referrals`,
COMMENT='Данные по бонусному отчету по агентам';
        ");

        $this->execute("
ALTER TABLE `bonus_report_number`
CHANGE `bonus_report_id` `bonus_report_id` int(11) NOT NULL COMMENT 'бонусный отчет' AFTER `id`,
CHANGE `number_id` `number_id` bigint(20) NOT NULL COMMENT 'номер' AFTER `bonus_report_id`,
CHANGE `parent_agent_id` `parent_agent_id` int(11) NOT NULL COMMENT 'агент, через которого проходила симка' AFTER `number_id`,
CHANGE `agent_id` `agent_id` int(11) NULL COMMENT 'агент потомок parent_agent_id, через которого проходила симка' AFTER `parent_agent_id`,
CHANGE `turnover` `turnover` decimal(14,2) NULL COMMENT 'оборот по номеру' AFTER `agent_id`,
CHANGE `rate` `rate` decimal(5,2) NULL COMMENT 'ставка вознаграждения агента-родителя в процентах' AFTER `turnover`,
CHANGE `sum` `sum` decimal(14,2) NULL COMMENT 'сумма вознаграждения агента-родителя' AFTER `rate`,
CHANGE `status` `status` enum('OK','TURNOVER_ZERO','NUMBER_MISSING') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'статус: ОК- все хорошо, TURNOVER_ZERO - нулевой оборот по номеру, NUMBER_MISSING- номер уже присутствовал в предыдущих отчетах, но в текущем отсутствует' AFTER `sum`,
COMMENT='Данные по бонусному отчету по номерам в разрезе всех агентов (для кадного номера хранится столько записей, через скольких агентов к нему попала симка)';
        ");

        $this->execute("
ALTER TABLE `company`
CHANGE `title` `title` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'название компании' AFTER `id`,
COMMENT='Названия компаний, которым принадлежат симки';
        ");

        $this->execute("
ALTER TABLE `file`
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата и время загрузки файла' AFTER `id`,
CHANGE `url` `url` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'абсолютный урл относительно корня сервера' AFTER `dt`,
COMMENT='Хранит данные о файлах (сканы паспортов и договоров)';
        ");

        $this->execute("
ALTER TABLE `number`
CHANGE `sim_id` `sim_id` bigint(20) NULL COMMENT 'Хранит ссылку на запись симки данного номера, которая принадлежит базе' AFTER `id`,
CHANGE `number` `number` varchar(50) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'номер' AFTER `sim_id`,
CHANGE `personal_account` `personal_account` varchar(50) COLLATE 'utf8_general_ci' NULL COMMENT 'личный счет' AFTER `number`,
CHANGE `status` `status` enum('UNKNOWN','FREE','ACTIVE','BLOCKED') COLLATE 'utf8_general_ci' NULL COMMENT 'статус: UNKNOWN- неизвестно, FREE - свободен для подключения, ACTIVE - активен (подключен), BLOCKED - заблокирован (пока не используется)' AFTER `personal_account`,
CHANGE `balance_status` `balance_status` enum('NORMAL','POSITIVE_STATIC','NEGATIVE_STATIC','POSITIVE_DYNAMIC','NEGATIVE_DYNAMIC','NEW','MISSING') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'NORMAL' COMMENT 'статус баланса' AFTER `status`,
CHANGE `balance_status_changed_dt` `balance_status_changed_dt` timestamp NULL COMMENT 'дата и время последнего изменения статуса баланса' AFTER `balance_status`,
CHANGE `codeword` `codeword` varchar(20) COLLATE 'utf8_general_ci' NULL COMMENT 'кодовое слово' AFTER `balance_status_changed_dt`,
CHANGE `service_password` `service_password` varchar(20) COLLATE 'utf8_general_ci' NULL COMMENT 'сервисный пароль' AFTER `codeword`,
CHANGE `support_operator_id` `support_operator_id` int(11) NULL COMMENT 'оператор, которому номер был передан в работу/последний обработавший номер оператор/После того, как загружены сканы, здесь хранится оператор, загрузивший их' AFTER `service_password`,
CHANGE `support_operator_got_dt` `support_operator_got_dt` timestamp NULL COMMENT 'дата и время, когда номер был передан оператору в работу' AFTER `support_operator_id`,
CHANGE `support_dt` `support_dt` timestamp NULL COMMENT 'дата и время последней обработки номера оператором' AFTER `support_operator_got_dt`,
CHANGE `support_status` `support_status` enum('UNAVAILABLE','CALLBACK','REJECT','PREACTIVE','ACTIVE','SERVICE_INFO','HELP') COLLATE 'utf8_general_ci' NULL COMMENT 'статус обработки номера' AFTER `support_dt`,
CHANGE `support_callback_dt` `support_callback_dt` timestamp NULL COMMENT 'дата и время, когда абонент попросил перезвонить' AFTER `support_status`,
CHANGE `support_callback_name` `support_callback_name` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'имя, которое нужно использовать при повторном прозвоне' AFTER `support_callback_dt`,
CHANGE `support_getting_passport_variant` `support_getting_passport_variant` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'способ получения паспорта' AFTER `support_callback_name`,
CHANGE `support_number_region_usage` `support_number_region_usage` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'регион использования номера' AFTER `support_getting_passport_variant`,
CHANGE `support_sent_sms_status` `support_sent_sms_status` enum('OFFICE','LK','EMAIL') COLLATE 'utf8_general_ci' NULL COMMENT 'тип последней отосланной абоненту SMS-ки' AFTER `support_number_region_usage`,
CHANGE `user_id` `user_id` int(11) NULL COMMENT 'соответствующая номеру user, используется для логина в ЛК' AFTER `support_sent_sms_status`,
CHANGE `support_passport_need_validation` `support_passport_need_validation` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'равен 1, если абонент загрузил данные по паспорту в личном кабинете, но оператор еще не проверил данные' AFTER `user_id`,
COMMENT='Хранит данные по номеру';
        ");

        $this->execute("
ALTER TABLE `number_history`
CHANGE `number_id` `number_id` bigint(20) NOT NULL COMMENT 'номер' AFTER `id`,
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата и время действия' AFTER `number_id`,
CHANGE `who` `who` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'кто выполнил действие. Можно использовать конструкции типа {<Модель>:<id>}' AFTER `dt`,
CHANGE `comment` `comment` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'описание действия. Можно использовать конструкции типа {<Модель>:<id>}' AFTER `who`,
COMMENT='Хранит историю по номеру';
        ");

        $this->execute("
ALTER TABLE `operator`
CHANGE `title` `title` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'Название оператора сотовой связи' AFTER `id`,
COMMENT='Хранит название операторов сотовой связи';
        ");

        $this->execute("
ALTER TABLE `operator_region`
CHANGE `title` `title` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'регион' AFTER `id`,
CHANGE `operator_id` `operator_id` int(11) NOT NULL COMMENT 'оператор' AFTER `title`,
COMMENT='Хранит названия регионов оператора';
        ");

        $this->execute("
ALTER TABLE `payment`
CHANGE `agent_id` `agent_id` int(11) NOT NULL COMMENT 'агент' AFTER `id`,
CHANGE `type` `type` enum('NORMAL','BONUS') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'тип пополнения: NORMAL - обычное пополнение, BONUS - начисленные по бонусному отчету бонусы' AFTER `agent_id`,
CHANGE `comment` `comment` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'комментарий' AFTER `type`,
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата и время' AFTER `comment`,
CHANGE `sum` `sum` decimal(14,2) NOT NULL COMMENT 'сумма' AFTER `dt`,
COMMENT='Хранит пополнения балансов агентов';
        ");

        $this->execute("
ALTER TABLE `person`
CHANGE `sex` `sex` enum('M','F') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'пол' AFTER `id`,
CHANGE `name` `name` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'имя' AFTER `sex`,
CHANGE `surname` `surname` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'фамилия' AFTER `name`,
CHANGE `middle_name` `middle_name` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'отчество' AFTER `surname`,
CHANGE `phone` `phone` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'телефон' AFTER `middle_name`,
CHANGE `email` `email` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'email' AFTER `phone`,
CHANGE `passport_series` `passport_series` varchar(10) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'серия паспорта' AFTER `email`,
CHANGE `passport_number` `passport_number` varchar(20) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'номер паспорта' AFTER `passport_series`,
CHANGE `passport_issue_date` `passport_issue_date` date NOT NULL COMMENT 'дата выдачи паспорта' AFTER `passport_number`,
CHANGE `passport_issuer` `passport_issuer` varchar(500) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'орган, выдавший паспорт' AFTER `passport_issue_date`,
CHANGE `passport_issuer_subdivision_code` `passport_issuer_subdivision_code` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'код подразделения органа, выдавшего паспорт' AFTER `passport_issuer`,
CHANGE `birth_date` `birth_date` date NOT NULL COMMENT 'дата рождения' AFTER `passport_issuer_subdivision_code`,
CHANGE `birth_place` `birth_place` varchar(500) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'место рождения' AFTER `birth_date`,
CHANGE `registration_address` `registration_address` varchar(500) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'адрес регистрации' AFTER `birth_place`,
COMMENT='хранит персональные данные абонентов';
        ");

        $this->execute("
ALTER TABLE `person_file`
CHANGE `person_id` `person_id` bigint(20) NOT NULL COMMENT 'персональные данные абонента' FIRST,
CHANGE `file_id` `file_id` bigint(20) NOT NULL COMMENT 'прикрепленный файл' AFTER `person_id`,
COMMENT='Хранит информацию о сканах паспорта';
        ");

        $this->execute("
ALTER TABLE `sim`
CHANGE `personal_account` `personal_account` varchar(50) COLLATE 'utf8_general_ci' NULL COMMENT 'личный счет' AFTER `id`,
CHANGE `number` `number` varchar(50) COLLATE 'utf8_general_ci' NULL COMMENT 'номер' AFTER `personal_account`,
CHANGE `number_price` `number_price` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'цена номера для агента agent_id' AFTER `number`,
CHANGE `sim_price` `sim_price` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT 'цена симки для агента agent_id' AFTER `number_price`,
CHANGE `icc` `icc` varchar(50) COLLATE 'utf8_general_ci' NULL COMMENT 'icc' AFTER `sim_price`,
CHANGE `parent_id` `parent_id` bigint(20) NULL COMMENT 'ссылка на запись данной симки, принадлежащей базе (у которой parent_id=1)' AFTER `icc`,
CHANGE `parent_agent_id` `parent_agent_id` int(11) NULL COMMENT 'NULL, если  симка еще не добавлена в базу (загружена накладная), =1 если симка в базе' AFTER `parent_id`,
CHANGE `operator_id` `operator_id` int(11) NULL COMMENT 'оператор' AFTER `act_id`,
CHANGE `tariff_id` `tariff_id` int(11) NULL COMMENT 'тарифный план' AFTER `operator_id`,
CHANGE `operator_region_id` `operator_region_id` int(11) NULL COMMENT 'регион' AFTER `tariff_id`,
CHANGE `company_id` `company_id` int(11) NULL COMMENT 'компания' AFTER `operator_region_id`,
COMMENT='Хранит информацию о симках и их передаче между агентами. Если симка прошла через N агентов (включая базу), то в таблице будет N записей для этой симки';
        ");

        $this->execute("
ALTER TABLE `sms_log`
CHANGE `user_id` `user_id` bigint(20) NOT NULL COMMENT 'агент/оператор, отправивший смс' AFTER `id`,
CHANGE `number` `number` varchar(50) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'номер' AFTER `user_id`,
CHANGE `text` `text` varchar(140) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'текст' AFTER `number`,
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата и время' AFTER `text`,
COMMENT='Хранит лог отправленных смс функцией \'Отправка СМС\'';
        ");

        $this->execute("
ALTER TABLE `subscription_agreement`
CHANGE `defined_id` `defined_id` varchar(50) COLLATE 'utf8_general_ci' NULL COMMENT 'номер договора' AFTER `id`,
CHANGE `dt` `dt` timestamp NULL COMMENT 'дата и время заключения' AFTER `defined_id`,
CHANGE `number_id` `number_id` bigint(20) NULL COMMENT 'номер' AFTER `dt`,
CHANGE `person_id` `person_id` bigint(20) NULL COMMENT 'персональные данные' AFTER `number_id`,
COMMENT='Хранит заключенные договора';
        ");

        $this->execute("
ALTER TABLE `subscription_agreement_file`
CHANGE `subscription_agreement_id` `subscription_agreement_id` bigint(20) NOT NULL COMMENT 'договор' FIRST,
CHANGE `file_id` `file_id` bigint(20) NOT NULL COMMENT 'файл скана договора' AFTER `subscription_agreement_id`,
COMMENT='Хранит информацию о сканах подписанного договора';
        ");

        $this->execute("
ALTER TABLE `support_operator`
CHANGE `user_id` `user_id` int(11) NOT NULL COMMENT 'запись user, используемая для логина в систему' AFTER `id`,
CHANGE `role` `role` enum('support','supportAdmin','supportMain','supportMegafon') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'роль: support - обычный оператор, supportAdmin - администратор, supportMain - главный оператор, supportMegafon - оператор мегафона ' AFTER `user_id`,
CHANGE `name` `name` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'имя' AFTER `role`,
CHANGE `surname` `surname` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'фамилия' AFTER `name`,
CHANGE `middle_name` `middle_name` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'отчество' AFTER `surname`,
CHANGE `phone` `phone` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'телефон' AFTER `middle_name`,
CHANGE `email` `email` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'email' AFTER `phone`,
COMMENT='Хранит операторов техподдержки';
        ");

        $this->execute("
ALTER TABLE `tariff`
CHANGE `operator_id` `operator_id` int(11) NOT NULL COMMENT 'оператор' AFTER `id`,
CHANGE `title` `title` varchar(50) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'название тарифного плана' AFTER `operator_id`,
CHANGE `description` `description` text COLLATE 'utf8_general_ci' NULL COMMENT 'описание тарифного плана' AFTER `title`,
CHANGE `price_agent_sim` `price_agent_sim` decimal(14,2) NOT NULL COMMENT 'цена сим для агента (наверно не используется)' AFTER `description`,
CHANGE `price_license_fee` `price_license_fee` decimal(14,2) NOT NULL COMMENT 'не используется' AFTER `price_agent_sim`,
COMMENT='Хранит тарифные планы';
        ");

        $this->execute("
ALTER TABLE `ticket`
CHANGE `number_id` `number_id` bigint(20) NOT NULL COMMENT 'номер' AFTER `id`,
CHANGE `sim_id` `sim_id` bigint(20) NOT NULL COMMENT 'сим номера' AFTER `number_id`,
CHANGE `agent_id` `agent_id` int(11) NOT NULL COMMENT 'агент, который непосредственно передал симку абноненту' AFTER `sim_id`,
CHANGE `support_operator_id` `support_operator_id` int(11) NULL COMMENT 'оператор/оператор мегафона, обработавший тикет' AFTER `agent_id`,
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата создания тикета' AFTER `support_operator_id`,
CHANGE `status` `status` enum('NEW','IN_WORK_MEGAFON','IN_WORK_OPERATOR','REFUSED_BY_MEGAFON','REFUSED_BY_ADMIN','REFUSED_BY_OPERATOR','FOR_REVIEW','DONE','REFUSED') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'статус тикета' AFTER `dt`,
CHANGE `text` `text` mediumtext COLLATE 'utf8_general_ci' NOT NULL COMMENT 'текст тикета, заполненный абонентом' AFTER `status`,
CHANGE `internal` `internal` mediumtext COLLATE 'utf8_general_ci' NULL COMMENT 'внутреннее задание, заполненное администратором' AFTER `text`,
CHANGE `response` `response` mediumtext COLLATE 'utf8_general_ci' NULL COMMENT 'ответ по тикету' AFTER `internal`,
COMMENT='Хранит тикеты';
        ");

        $this->execute("
ALTER TABLE `ticket_history`
CHANGE `ticket_id` `ticket_id` bigint(20) NOT NULL COMMENT 'тикет' AFTER `id`,
CHANGE `dt` `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата' AFTER `ticket_id`,
CHANGE `support_operator_id` `support_operator_id` int(11) NULL COMMENT 'оператор, совершивший действие' AFTER `dt`,
CHANGE `agent_id` `agent_id` int(11) NULL COMMENT 'агент, совершивший действие' AFTER `support_operator_id`,
CHANGE `comment` `comment` mediumtext COLLATE 'utf8_general_ci' NULL COMMENT 'комментарий' AFTER `agent_id`,
CHANGE `status` `status` enum('NEW','IN_WORK_MEGAFON','IN_WORK_OPERATOR','REFUSED_BY_MEGAFON','REFUSED_BY_ADMIN','REFUSED_BY_OPERATOR','FOR_REVIEW','DONE','REFUSED') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'статус тикета после совершения действия' AFTER `comment`,
COMMENT='Хранит историю по тикету. Действия могут совершать как операторы, так и база (pavimus)';
        ");

        $this->execute("
ALTER TABLE `user`
CHANGE `status` `status` enum('ACTIVE','BLOCKED') COLLATE 'utf8_general_ci' NOT NULL COMMENT 'status' AFTER `id`,
CHANGE `username` `username` varchar(200) COLLATE 'utf8_general_ci' NOT NULL COMMENT 'имя пользователя' AFTER `status`,
CHANGE `password` `password` varchar(200) COLLATE 'utf8_general_ci' NULL COMMENT 'соленый пароль в crypt формате ' AFTER `username`,
CHANGE `failed_logins` `failed_logins` int(11) NULL COMMENT 'кол-во неудачных логинов после последнего удачного либо после истечения времени блокировки' AFTER `password`,
CHANGE `blocked_until` `blocked_until` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'дата и время снятия блокировки, если пользователь заблокирован из-за неудачных попыток логина' AFTER `failed_logins`,
CHANGE `last_password_restore` `last_password_restore` timestamp NULL COMMENT 'дата и время последнего восстановления пароля данного пользователя в личном кабинете (используется на сайте личного кабинета)' AFTER `blocked_until`,
COMMENT='Данные пользователей для входа в систему';
        ");
    }

	public function down()
	{
		echo "m130307_090207_add_tables_comments does not support migration down.\n";
		return false;
	}
}