<?php

require_once('../common.php');

myexec('git log -n 50 --format="format:%ai %h %an %s"');
