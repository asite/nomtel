<?php

$this->breadcrumbs = array(
    $agreement->adminLabel(SubscriptionAgreement::label(1))
);

?>

<h1><?=$agreement->adminLabel(SubscriptionAgreement::label(1))?></h1>

<?php

$this->renderPartial('_form', array(
    'sim'=>$sim,
    'agreement'=>$agreement,
    'person'=>$person,
    'person_files'=>$person_files,
    'agreement_files'=>$agreement_files
));

?>