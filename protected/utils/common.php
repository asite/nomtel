<?php

// function for calling external programs
function myexec($cmdline,$caption='',$dont_redirect_errors=false) {
    if ($caption!='') echo "<b>$caption</b>\n";
    echo "<PRE>\n";
    passthru('umask 0022 && '.$cmdline.($dont_redirect_errors ? '':' 2>&1'),$status);
    echo "</PRE><b>execution finished, status $status</b><br><br><br>\n";
    
}

header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

// change current directory
$script_dir=getcwd();
chdir(preg_replace("%[^\/\\\\]+$%","",__FILE__).'../../');

echo '<html><head><title>SITE TOOLS</title><META HTTP-EQUIV="Cache-Control" content="no-cache"></head><body>';

function shutdown() {
  echo "</body></html>";
}

register_shutdown_function('shutdown');

