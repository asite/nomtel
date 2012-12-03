<?php


function smarty_function_var_dump($params, &$smarty)
{
	echo '<pre>';
	var_dump($params['var']);
	echo '</pre>';
}

