<h2>Личный кабинет номера <?=$number->formattedNumber?></h2>

<?php
if ($needPassport)
    $this->renderPartial('passport',array('person_files'=>$person_files,'person'=>$person));
else
    $this->renderPartial('mainData',array('number'=>$number,'sim'=>$sim));

?>