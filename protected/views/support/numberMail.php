Агент: <?=$agent?><?="\n"?>
Номер Агента: <?=$agent->phone_1?><?="\n"?>
E-Mail Агента: <?=$agent->email?><?="\n"?>

Номер обращения: <?=$report_number?><?="\n"?>
Время обращения: <?=$report_dt?><?="\n"?>

Номер абонента:  <?=$report->abonent_number?><?="\n"?>
Проблемный номер: <?=$report->number?><?="\n"?>

Оператор: <?=$number->sim->operator?><?="\n"?>
Тариф: <?=$number->sim->tariff?><?="\n"?>
Регион: <?=$number->sim->operatorRegion?><?="\n"?>

Сообщение:<?="\n"?>
<?=$report->message?>


