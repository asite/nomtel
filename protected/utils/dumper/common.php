<?php

require_once('../ext_tools_common.php');

define('SES_FILE',dirname(__FILE__).'/../../../var/dumper/ses.php');
define('CFG_FILE',dirname(__FILE__).'/../../../var/dumper/cfg.php');
define('ERRORLOG_FILE',dirname(__FILE__).'/../../../var/dumper/error.log');
if (!file_exists(SES_FILE)) copy ('ses.php',SES_FILE);
if (!file_exists(CFG_FILE)) copy ('cfg.php',CFG_FILE);
