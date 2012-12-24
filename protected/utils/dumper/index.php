<?php
/***************************************************************************\
| Sypex Dumper               version 2.0.9                                  |
| (c) 2003-2011 zapimir      zapimir@zapimir.net       http://sypex.net/    |
| (c) 2005-2011 BINOVATOR    info@sypex.net                                 |
|---------------------------------------------------------------------------|
|     created: 2003.09.02 19:07              modified:                      |
|---------------------------------------------------------------------------|
| Sypex Dumper is released under the terms of the BSD license               |
|   http://sypex.net/bsd_license.txt                                        |
\***************************************************************************/

header("Expires: Wed, 19 Nov 2008 19:19:19 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Content-Type: text/html; charset=utf-8");
if(!ini_get('zlib.output_compression') && function_exists('ob_gzhandler')) ob_start('ob_gzhandler');
//error_reporting(E_ALL);
@set_magic_quotes_runtime(0);
error_reporting(0);
set_error_handler('sxd_error_handler');
register_shutdown_function('sxd_shutdown');
$SXD = new Sypex_Dumper();
chdir(dirname(__FILE__));
$SXD->init(!empty($argc) && $argc > 1 ? $argv : false);

class Sypex_Dumper
{

	function Sypex_Dumper( )
	{
		define( "C_DEFAULT", 1 );
		define( "C_RESULT", 2 );
		define( "C_ERROR", 3 );
		define( "C_WARNING", 4 );
		define( "SXD_DEBUG", false );
		define( "TIMER", array_sum( explode( " ", microtime( ) ) ) );
		define( "V_SXD", 20009 );
		define( "V_PHP", sxd_ver2int( phpversion( ) ) );
		$this->name = "Sypex Dumper Pro 2.0.9";
		$this->url = isset( $_SERVER['SERVER_PORT'] ) ? ( $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://" ).getenv( "SERVER_NAME" ).dirname( $_SERVER['PHP_SELF'] )."/" : "";
	}

	function loadLang( $lng_name = "auto" )
	{
		if ( $lng_name == "auto" )
		{
			include( "lang/list.php" );
			$this->langs =& $langs;
			$lng = "en";
			if ( preg_match_all( "/[a-z]{2}(-[a-z]{2})?/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $m ) )
			{
				foreach ( $m[0] as $l )
				{
					if ( isset( $langs[$l] ) )
					{
					    $lng_name = $l;
					    break;
					}
				}
			}
		}
		if ( file_exists( "lang/lng_{$lng_name}.php" ) )
		{
			include( "lang/lng_{$lng_name}.php" );
		}
		else
		{
			include( "lang/lng_en.php" );
		}
		$this->LNG = &$LNG;
		$this->LNG['name'] = $lng_name;
		return true;
	}

	function init( $args = false )
	{
		if ( get_magic_quotes_gpc( ) )
		{
			$_POST = sxd_antimagic( $_POST );
		}
		include( "cfg.php" );
		$this->loadLang( $CFG['lang'] );
		if ( !ini_get( "safe_mode" ) && function_exists( "set_time_limit" ) && strpos( ini_get( "disable_functions" ), "set_time_limit" ) === false )
		{
			@set_time_limit( $CFG['time_web'] );
		}
		else if ( ini_get( "max_execution_time" ) < $CFG['time_web'] )
		{
			$CFG['time_web'] = ini_get( "max_execution_time" );
		}
		$this->CFG = &$CFG;
		$this->try = false;
		$this->virtualize = false;
		$this->cron_mode = false;
		if ( empty( $this->CFG['my_user'] ) )
		{
			$this->CFG['my_host'] = "localhost";
			$this->CFG['my_port'] = 3306;
			$this->CFG['my_user'] = "root";
			$this->CFG['my_pass'] = "";
			$this->CFG['my_comp'] = 0;
			$this->CFG['my_db'] = "";
		}
		if ( $args )
		{
			foreach ( $args as $key => $arg )
			{
				if ( preg_match( "/^-([hupoj])=(.*?)\$/", $arg, $m ) )
				{
					switch ( $m[1] )
					{
					case "h" :
						$this->CFG['my_host'] = $m[2];
						break;
					case "o" :
						$this->CFG['my_port'] = $m[2];
						break;
					case "u" :
						$this->CFG['my_user'] = $m[2];
						break;
					case "p" :
						$this->CFG['my_pass'] = $m[2];
						break;
					case "j" :
						$this->CFG['sjob'] = $m[2];
						break;
					}
				}
			}
			$this->cron_mode = true;
			set_time_limit( $CFG['time_cron'] );
			$auth = $this->connect( );
			if ( $auth && !empty( $this->CFG['sjob'] ) )
			{
				$this->ajax( $this->loadJob( $this->CFG['sjob'] ) );
				echo file_get_contents( $this->JOB['file_log'] );
				if ( file_exists( $this->JOB['file_log'] ) )
				{
					unlink( $this->JOB['file_log'] );
				}
				if ( file_exists( $this->JOB['file_rtl'] ) )
				{
					unlink( $this->JOB['file_rtl'] );
				}
			}
			else
			{
				echo "Auth error";
			}
			exit;
		}
		else if ( !empty( $this->CFG['auth'] ) )
		{
			$auth = false;
			$sfile = "ses.php";
			if ( !empty( $_COOKIE['sxd'] ) && preg_match( "/^[\da-f]{32}$/", $_COOKIE['sxd'] ) )
			{
				include( $sfile );
				if ( isset( $SES[$_COOKIE['sxd']] ) )
				{
					$auth = true;
					$this->CFG = $SES[$_COOKIE['sxd']]['cfg'];
					$this->SES = &$SES;
					$this->loadLang( $this->CFG['lang'] );
				}
			}
			if ( !$auth )
			{
				$user = !empty( $_POST['user'] ) ? $_POST['user'] : "";
				$pass = !empty( $_POST['pass'] ) ? $_POST['pass'] : "";
				$host = !empty( $_POST['host'] ) ? $_POST['host'] : "localhost";
				$port = !empty( $_POST['port'] ) && is_numeric( $_POST['port'] ) ? $_POST['port'] : 3306;
				$temp = preg_split( "/\s+/", $this->CFG['auth'] );
				if ( !empty( $_REQUEST['lang'] ) && preg_match( "/^[a-z]{2}(-[a-z]{2})?$/", $_REQUEST['lang'] ) )
				{
					$this->loadLang( $_REQUEST['lang'] );
				}
				foreach ( $temp as $a )
				{
					switch ( $a )
					{
						case "cfg" :
							if ( empty( $user ) )
							{
								continue;
							}
							$auth = !empty( $CFG['user'] ) && isset( $CFG['pass'] ) && $CFG['user'] == $user && $CFG['pass'] == $pass;
							break;
						case "mysql" :
							if ( empty( $user ) )
							{
								continue;
							}
							if ( $host != "localhost" && !empty($this->CFG['my_host']) && $this->CFG['my_host'] != $host )
							{
								continue;
							}
							$auth = $this->connect( $host, $port, $user, $pass );
							break;
						default :
							$file = "auth_".$a.".php";
							if ( !file_exists( $file ) )
							{
								continue;
							}
							include	$file;
					}
					if ( $auth )
					{
						break;
					}
				}
				if ( $auth )
				{
					$key = md5( rand( 1, 100000 ).$user.microtime( ) );
					$CFG['lang'] = $this->LNG['name'];
					$_COOKIE['sxd'] = $key;
					$this->saveCFG( );
					setcookie( "sxd", $key, !empty( $_POST['save'] ) ? time( ) + 31536000 : 0 );
					header( "Location: {$this->url}" );
					exit;
				}
				foreach ( array( "user", "pass", "host", "port" ) as $key )
				{
					$_POST[$key] = !empty( $_POST[$key] ) ? htmlspecialchars( $_POST[$key], ENT_NOQUOTES ) : "";
				}
				$_POST['save'] = !empty( $_POST['save'] ) ? " CHECKED" : "";
			}
			if ( !$auth )
			{
				if ( !empty( $_POST['ajax'] ) )
				{
					echo "sxd.hideLoading();alert('Session not found');";
					exit;
				}
				$this->lng_list = '<option value="auto">- auto -</opinion>';
				if ( !isset( $this->langs ) )
				{
					include( "lang/list.php" );
					$this->langs = &$langs;
				}
				foreach ( $this->langs as $k => $v )
				{
					$this->lng_list .= "<option value=\"{$k}\"".( $k == ( !empty( $_REQUEST['lang'] ) ? $this->LNG['name'] : $this->CFG['lang'] ) ? " SELECTED" : "" ).">{$v}</opinion>";
				}
				echo sxd_tpl_auth( );
				exit;
			}
		}
		if ( empty( $_POST['ajax']['act'] ) || $_POST['ajax']['act'] != "save_connect" )
		{
			$this->connect( );
		}
		if ( isset( $_POST['ajax'] ) )
		{
			$this->ajax( $_POST['ajax'] );
		}
		else
		{
			$this->main( );
		}
	}

	function saveToFile( $name, $content )
	{
		$fp = fopen( $name, "w" );
		fwrite( $fp, $content );
		fclose( $fp );
	}

	function connect( $host = null, $port = null, $user = null, $pass = null )
	{
		$this->error = "";
		$this->try = true;
		if ( !empty( $user ) && isset( $pass ) )
		{
			$this->CFG['my_host'] = $host;
			$this->CFG['my_port'] = $port;
			$this->CFG['my_user'] = $user;
			$this->CFG['my_pass'] = $pass;
		}
		if ( mysql_connect( $this->CFG['my_host'].( $this->CFG['my_host']{0} != ":" ? ":{$this->CFG['my_port']}" : "" ), $this->CFG['my_user'], $this->CFG['my_pass'] ) )
		{
			mysql_query( "SET NAMES utf8" ) or sxd_my_error( );
			$r = mysql_query( "SELECT UNIX_TIMESTAMP()" );
			$res = mysql_fetch_row( );
			$h = str_replace( "www.", "", isset( $_SERVER['HTTP_HOST'] ) ? getenv( "SERVER_NAME" ) : getenv( "HOSTNAME" ) );
				define( "V_MYSQL", sxd_ver2int( mysql_get_server_info( ) ) );
		}
		else
		{
			define( "V_MYSQL", 0 );
			$this->error = "sxd.actions.tab_connects();alert('{".mysql_escape_string( mysql_error( ) )."');";
		}
		$this->try = false;
		return V_MYSQL ? true : false;
	}

	function main( )
	{
		$this->VAR['toolbar'] = sxd_php2json( array(
			array(
				"backup",
				$this->LNG['tbar_backup'],
				1,
				3
			),
			array(
				"restore",
				$this->LNG['tbar_restore'],
				2,
				3
			),
			array( "|" ),
			array(
				"files",
				$this->LNG['tbar_files'],
				3,
				1
			),
			array(
				"services",
				$this->LNG['tbar_services'],
				5,
				1
			),
			array( "|" ),
			array(
				"createdb",
				$this->LNG['tbar_createdb'],
				7,
				0
			),
			array(
				"connects",
				$this->LNG['tbar_connects'],
				6,
				0
			),
			array( "|" ),
			array(
				"options",
				$this->LNG['tbar_options'],
				4,
				1
			),
			array( "|" ),
			array(
				"exit",
				$this->LNG['tbar_exit'],
				8,
				1
			)
		) );
		$this->db = "temp";
		$zip = array(
			$this->LNG['zip_none']
		);
		if ( function_exists( "gzopen" ) )
		{
			for ( $i = 1; $i < 10; $i++ )
			{
				$zip[] = "GZip: {$i}";
			}
			$zip[1] .= " ({$this->LNG['zip_min']})";
			$zip[7] .= " ({$this->LNG['default']})";
		}
		if ( function_exists( "bzopen" ) )
		{
			$zip[10] = "BZip";
		}
		end( $zip );
		$zip[key( $zip )] .= " ({$this->LNG['zip_max']})";
		$this->VAR['combos'] = $this->addCombo( "backup_db", $this->db, 11, "db", array( ) ).$this->addCombo( "backup_charset", 0, 9, "charset", $this->getCharsetList( ) ).$this->addCombo( "backup_zip", 7, 10, "zip", $zip ).$this->addCombo( "restore_db", $this->db, 11, "db" ).$this->addCombo( "restore_charset", 0, 9, "charset" ).$this->addCombo( "restore_file", 0, 12, "files", $this->getFileList( ) ).$this->addCombo( "restore_type", 0, 13, "types", array(
			"CREATE + INSERT (".$this->LNG['default'].")",
			"TRUNCATE + INSERT",
			"REPLACE",
			"INSERT IGNORE"
		) ).$this->addCombo( "services_db", $this->db, 11, "db" ).$this->addCombo( "services_check", 0, 5, "check", array(
			"- ".$this->LNG['default']." -",
			"QUICK",
			"FAST",
			"CHANGED",
			"MEDIUM",
			"EXTENDED"
		) ).$this->addCombo( "services_repair", 0, 5, "repair", array(
			"- ".$this->LNG['default']." -",
			"QUICK",
			"EXTENDED"
		) ).$this->addCombo( "services_charset", 0, 9, "collation", $this->getCollationList( ) ).$this->addCombo( "services_charset_col", 0, 15, "collation:services_charset" ).$this->addCombo( "db_charset", 0, 9, "collation" ).$this->addCombo( "db_charset_col", 0, 15, "collation:db_charset" );
		if ( !V_MYSQL )
		{
			$this->VAR['combos'] .= $this->error;
		}
		$this->VAR['combos'] .= $this->getSavedJobs( ).( "sxd.confirms = {$this->CFG['confirm']};sxd.actions.dblist();" );
		$this->LNG['del_date'] = sprintf( $this->LNG['del_date'], '<input type="text" id="del_time" class=txt style="width:24px;" maxlength="3">' );
		$this->LNG['del_count'] = sprintf( $this->LNG['del_count'], '<input id="del_count" type="text" class=txt style="width:18px;" maxlength="2">' );
		$this->LNG['prefix'] = sprintf( $this->LNG['prefix'], '<input type="text" id="prefix_from" class=txt style="width:30px;">', '<input type="text" id="prefix_to" class=txt style="width:30px;">' );
		echo sxd_tpl_page( );
	}

	function addCombo( $name, $sel, $ico, $opt_name, $opts = "" )
	{
		$opts = !empty( $opts ) ? "{{$opt_name}:".sxd_php2json( $opts )."}" : "'{$opt_name}'";
		return "sxd.addCombo('{$name}', '{$sel}', {$ico}, {$opts});\n";
	}

	function ajax( $req )
	{
		$res = "";
		$act = $req['act'];
		if ( $req['act'] == "run_savedjob" )
		{
			$req = $this->loadJob( $req );
		}
		switch ( $req['act'] )
		{
		case "load_db" :
			$res = $this->getObjects( str_replace( "_db", "", $req['name'] ), $req['value'] );
			break;
		case "load_files" :
			$res = $this->getFileObjects( "restore", $req['value'] );
			break;
		case "filelist" :
			$res = "sxd.clearOpt('files');sxd.addOpt(".sxd_php2json( array(
				"files" => $this->getFileList( )
			) ).");";
			break;
		case "dblist" :
			$res = "sxd.clearOpt('db');sxd.addOpt(".sxd_php2json( array(
				"db" => $this->getDBList( )
			) ).");sxd.combos.restore_db.select(0,'-');sxd.combos.services_db.select(0,'-');sxd.combos.backup_db.select(0,'-');";
			break;
		case "load_connect" :
			$CFG = $this->cfg2js( $this->CFG );
			$res = "z('con_host').value = '".$CFG['my_host']."', z('con_port').value = '{$CFG['my_port']}', z('con_user').value = '{$CFG['my_user']}',
		z('con_pass').value = '', z('con_comp').checked = {$CFG['my_comp']}, z('con_db').value = '{$CFG['my_db']}', z('con_pass').changed = false;";
			break;
		case "save_connect" :
			$res = $this->saveConnect( $req );
			break;
		case "save_job" :
			unset( $req['act'] );
			$this->saveJob( "sj_".$req['job'], $req );
			$res = $this->getSavedJobs( );
			break;
		case "add_db" :
			$res = $this->addDb( $req );
			break;
		case "load_options" :
			$CFG = $this->cfg2js( $this->CFG );
			$res = "z('time_web').value = '".$CFG['time_web']."', z('time_cron').value = '{$CFG['time_cron']}', z('backup_path').value = '{$CFG['backup_path']}',
		z('backup_url').value = '{$CFG['backup_url']}', z('globstat').checked = {$CFG['globstat']}, z('outfile_path').value = '{$CFG['outfile_path']}', z('outfile_size').value = '{$CFG['outfile_size']}', z('charsets').value = '{$CFG['charsets']}', z('only_create').value = '{$CFG['only_create']}', z('auth').value = '{$CFG['auth']}', z('conf_import').checked = {$CFG['confirm']} & 1, z('conf_file').checked = {$CFG['confirm']} & 2, z('conf_db').checked = {$CFG['confirm']} & 4, z('conf_truncate').checked = {$CFG['confirm']} & 8, z('conf_drop').checked = {$CFG['confirm']} & 16;sxd.confirms = {$this->CFG['confirm']};";
			break;
		case "save_options" :
			$res = $this->saveOptions( $req );
			break;
		case "delete_file" :
			if ( preg_match( "/^[^\/]+?\.sql(\.(gz|bz2))?$/", $req['name'] ) )
			{
				$file = $this->CFG['backup_path'].$req['name'];
				if ( file_exists( $file ) )
				{
					unlink( $file );
				}
			}
			$res = $this->getFileListExtended( );
			break;
		case "delete_db" :
			$res = $this->deleteDB( $req['name'] );
			break;
		case "load_files_ext" :
			$res .= $this->getFileListExtended( );
			break;
		case "services" :
			$this->runServices( $req );
			break;
		case "backup" :
			$this->addBackupJob( $req );
			break;
		case "restore" :
			$this->addRestoreJob( $req );
			break;
		case "resume" :
			$this->resumeJob( $req );
			break;
		case "exit" :
			setcookie( "sxd", "", 0 );
			$res = "top.location.href = '".mysql_escape_string( $this->CFG['exitURL'] )."'";
			break;
		}
		echo $res;
	}

	function loadJob( $job )
	{
		$file = $this->CFG['backup_path']."sj_".( is_array( $job ) ? $job['job'] : $job ).".job.php";
		if ( !file_exists( $file ) )
		{
			return;
		}
		include( $file );
		$JOB['act'] = $JOB['type'];
		$JOB['type'] = "run";
		return $JOB;
	}

	function deleteDB( $name )
	{
		$r = mysql_query( "DROP DATABASE `".mysql_escape_string( $name )."`" ) or sxd_my_error( );
		if ( $r )
		{
			echo "sxd.clearOpt('db');sxd.addOpt(".sxd_php2json( array(
				"db" => $this->getDBList( )
			) ).");sxd.combos.services_db.select(0,'-');";
		}
		else
		{
			echo "alert('".mysql_escape_string( mysql_error( ) )."')";
		}
	}

	function cfg2js( $cfg )
	{
		foreach ( $cfg as $k => $v )
		{
			$cfg[$k] = mysql_escape_string( $v );
		}
		return $cfg;
	}

	function addDb( $req )
	{
		$r = mysql_query( "CREATE DATABASE `".mysql_escape_string( $req['name'] )."`".( 40100 < V_MYSQL ? "CHARACTER SET {$req['charset']} COLLATE {$req['collate']}" : "" ) );
		if ( $r )
		{
			echo "sxd.addOpt(".sxd_php2json( array(
				"db" => array(
					$req['name'] => "{$req['name']} (0)"
				)
			) ).");";
		}
		else
		{
			sxd_my_error( );
		}
	}

	function saveConnect( $req )
	{
		$this->CFG['my_host'] = $req['host'];
		$this->CFG['my_port'] = ( int )$req['port'];
		$this->CFG['my_user'] = $req['user'];
		if ( isset( $req['pass'] ) )
		{
			$this->CFG['my_pass'] = $req['pass'];
		}
		$this->CFG['my_comp'] = $req['comp'] ? 1 : 0;
		$this->CFG['my_db'] = $req['db'];
		$this->saveCFG( );
		$this->connect( );
		if ( V_MYSQL )
		{
			$tmp = array(
				"db" => $this->getDBList( ),
				"charset" => $this->getCharsetList( ),
				"collation" => $this->getCollationList( )
			);
			echo "sxd.clearOpt('db');sxd.clearOpt('charset');sxd.clearOpt('collation');sxd.addOpt(".sxd_php2json( $tmp ).");sxd.combos.backup_db.select(0,'-');sxd.combos.restore_db.select(0,'-');sxd.combos.services_db.select(0,'-');sxd.combos.backup_charset.select(0,'-');sxd.combos.services_db.select(0,'-');sxd.combos.db_charset.select(0,'-');";
		}
		else
		{
			echo $this->error;
		}
	}

	function saveOptions( $req )
	{
		$this->CFG['time_web'] = $req['time_web'];
		$this->CFG['time_cron'] = $req['time_cron'];
		$this->CFG['backup_path'] = $req['backup_path'];
		$this->CFG['backup_url'] = $req['backup_url'];
		$this->CFG['globstat'] = $req['globstat'] ? 1 : 0;
		$this->CFG['outfile_path'] = $req['outfile_path'];
		$this->CFG['outfile_size'] = $req['outfile_size'];
		$this->CFG['charsets'] = $req['charsets'];
		$this->CFG['only_create'] = $req['only_create'];
		$this->CFG['auth'] = $req['auth'];
		$this->CFG['confirm'] = $req['confirm'];
		$this->saveCFG( );
	}

	function saveCFG( )
	{
		if ( isset( $_COOKIE['sxd'] ) )
		{
			$this->SES[$_COOKIE['sxd']] = array(
				"cfg" => $this->CFG,
				"time" => time( ),
				"lng" => $this->LNG['name']
			);
			$this->saveToFile( "ses.php", "<?php\n\$SES = ".var_export( $this->SES, true ).";\n"."?>" );
		}
		if ( !$this->virtualize )
		{
			$this->saveToFile( "cfg.php", "<?php\n\$CFG = ".var_export( $this->CFG, true ).";\n"."?>" );
		}
	}

	function runServices( $job )
	{
		$serv = array( "optimize" => "OPTIMIZE", "analyze" => "ANALYZE", "check" => "CHECK", "repair" => "REPAIR" );
		$add = array(
			"check" => array( "", "QUICK", "FAST", "CHANGED", "MEDIUM", "EXTENDED" ),
			"repair" => array( "", "QUICK", "EXTENDED" )
		);
		if ( isset( $serv[$job['type']] ) )
		{
			mysql_select_db( $job['db'] );
			$filter = $object = array( );
			$this->createFilters( $job['obj'], $filter, $object );
			$r = mysql_query( "SHOW TABLE STATUS" ) or sxd_my_error( );
			if ( !$r )
			{
				return;
			}
			$tables = array( );
			while ( $item = mysql_fetch_assoc( $r ) )
			{
				if ( 40101 < V_MYSQL && is_null( $item['Engine'] ) && preg_match( "/^VIEW/i", $item['Comment'] ) )
				{
					continue;
				}
				if ( sxd_check( $item['Name'], $object['TA'], $filter['TA'] ) )
				{
					$tables[] = "`{$item['Name']}`";
				}
			}
			$sql = $serv[$job['type']]." TABLE ".implode( ",", $tables );
			if ( $job['type'] == "check" || $job['type'] == "repair" )
			{
				$sql .= isset( $add[$job['type']][$job[$job['type']]] ) ? " ".$add[$job['type']][$job[$job['type']]] : "";
			}
			$r = mysql_query( $sql ) or sxd_my_error( );
			if ( !$r )
			{
				return;
			}
			$res = array( );
			while ( $item = mysql_fetch_row( $r ) )
			{
				$res[] = $item;
			}
			echo "sxd.result.add(".sxd_php2json( $res ).");";
		}
		else if ( in_array( $job['type'], array( "convert", "correct", "enable_keys", "disable_keys", "truncate", "drop_tab" ) ) )
		{
			mysql_select_db( $job['db'] );
			$filter = $object = array( );
			$this->createFilters( $job['obj'], $filter, $object );
			$r = mysql_query( "SHOW TABLE STATUS" ) or sxd_my_error( );
			if ( !$r )
			{
				return;
			}
			$tables = array( );
			while ( $item = mysql_fetch_assoc( $r ) )
			{
				if ( 40101 < V_MYSQL && is_null( $item['Engine'] ) && preg_match( "/^VIEW/i", $item['Comment'] ) )
				{
					continue;
				}
				if ( sxd_check( $item['Name'], $object['TA'], $filter['TA'] ) )
				{
					$tables[] = "`{$item['Name']}`";
				}
			}
			foreach ( $tables as $t )
			{
				$type = $job['type'];
				$error = false;
				switch ( $job['type'] )
				{
				case "convert" :
					if ( mysql_query( "ALTER TABLE {$t} CONVERT TO CHARACTER SET {$job['charset']} COLLATE {$job['collate']}" ) )
					{
						$result = "OK. Convert to `{$job['collate']}`";
					}
					$error = true;
					break;
				case "correct" :
					$c = mysql_query( "SHOW FULL COLUMNS FROM {$t} WHERE `Collation` IS NOT NULL" );
					$ok = true;
					while ( $col = mysql_fetch_assoc( $c ) )
					{
						$add = ( $col['Null'] == "YES" ? "" : "NOT NULL" ).( preg_match( "/(blob|text)/", $col['Type'] ) ? "" : " DEFAULT ".( is_null( $col['Default'] ) ? "NULL" : "'".mysql_escape_string( $col['Default'] )."'" ) ).( empty( $col['Comment'] ) ? "" : " COMMENT '".mysql_escape_string( $col['Comment'] )."'" );
						if ( !mysql_query( "ALTER TABLE {$t} CHANGE {$col['Field']} {$col['Field']} {$col['Type']} CHARACTER SET binary {$add}" ) )
						{
							break;
						}
						else if ( !mysql_query( "ALTER TABLE {$t} CHANGE `{$col['Field']}` `{$col['Field']}` {$col['Type']} CHARACTER SET {$job['charset']} COLLATE {$job['collate']} {$add}" ) )
						{
							break;
						}
						$ok = false;
						break;
					}
					$ok = false;
					break;
					if ( $ok )
					{
						mysql_query( "ALTER TABLE {$t} DEFAULT CHARACTER SET {$job['charset']} COLLATE {$job['collate']}" );
						$result = "OK. Correct to `{$job['collate']}`";
					}
					$error = true;
					break;
				case "enable_keys" :
					$type = "enable keys";
					if ( mysql_query( "ALTER TABLE {$t} ENABLE KEYS" ) )
					{
						$result = "OK";
					}
					$error = true;
					break;
				case "disable_keys" :
					$type = "disable keys";
					if ( mysql_query( "ALTER TABLE {$t} DISABLE KEYS" ) )
					{
						$result = "OK";
					}
					$error = true;
					break;
				case "truncate" :
					if ( mysql_query( "TRUNCATE {$t}" ) )
					{
						$result = "OK";
					}
					$error = true;
					break;
				case "drop_tab" :
					if ( mysql_query( "DROP TABLE {$t}" ) )
					{
						$result = "OK";
					}
					else
					{
						$error = true;
					}
				}
				if ( $error )
				{
					echo "sxd.result.add(['{$t}', '{$type}', 'error', '".mysql_escape_string( mysql_error( ) )."']);";
				}
				else
				{
					echo "sxd.result.add(['{$t}', '{$type}', 'status', '{$result}']);";
				}
				if ( in_array( $type, array( "truncate", "drop_tab" ) ) )
				{
					echo "sxd.combos.services_db.action();";
				}
			}
		}
	}

	function createFilters( &$obj, &$filter, &$object )
	{
		$types = array( "TA", "TC", "VI", "PR", "FU", "TR", "EV" );
		foreach ( $types as $type )
		{
			$filter[$type] = array( );
			$object[$type] = array( );
			if ( !empty( $obj[$type] ) )
			{
				foreach ( $obj[$type] as $v )
				{
					if ( strpos( $v, "*" ) !== false )
					{
						$filter[$type][] = str_replace( "*", ".*?", $v );
					}
					else
					{
						$object[$type][$v] = true;
					}
				}
				$filter[$type] = 0 < count( $filter[$type] ) ? "/^(".implode( "|", $filter[$type] ).")\$/i" : "";
			}
		}
	}

	function closeConnect( )
	{
		@ignore_user_abort( 1 );
		header( "SXD: {$this->name}" );
		header( "Connection: close" );
		header( "Content-Length: 0" );
		@ob_end_flush( );
		@flush( );
	}

	function resumeJob( $job )
	{
		$this->closeConnect( );
		include( $this->CFG['backup_path'].$job['job'].".job.php" );
		$this->JOB = &$JOB;
		if ( file_exists( $this->JOB['file_stp'] ) )
		{
			unlink( $this->JOB['file_stp'] );
		}
		$this->fh_rtl = fopen( $this->JOB['file_rtl'], "r+b" );
		$this->fh_log = fopen( $this->JOB['file_log'], "ab" );
		$t = fgets( $this->fh_rtl );
		if ( !empty( $t ) )
		{
			$this->rtl = explode( "\t", $t );
		}
		else
		{
			$this->addLog( $this->LNG['not_found_rtl'] );
			exit;
		}
		fseek( $this->fh_rtl, 0 );
		$this->rtl[1] = time( );
		$this->rtl[9] = 0;
		fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
		if ( $this->JOB['act'] == "backup" )
		{
			$this->runBackupJob( true );
		}
		else if ( $this->JOB['act'] == "restore" )
		{
			$this->runRestoreJob( true );
		}
	}

	function addRestoreJob( $job )
	{
		$this->closeConnect( );
		$this->JOB = $job;
		$filter = $object = array( );
		$this->createFilters( $this->JOB['obj'], $filter, $object );
		$h = str_replace( "www.", "", getenv( "SERVER_NAME" ) );
		$objects = $this->getFileObjects( "restore", $this->JOB['file'], false );
		$todo = array( );
		$rows = 0;
		$this->tab_rows = array( );
		$todo = array( );
		foreach ( $objects as $t => $list )
		{
			if ( $t == "TA" && ( !empty( $object['TC'] ) || !empty( $filter['TC'] ) ) )
			{
			}
			else if ( empty( $object[$t] ) && empty( $filter[$t] ) )
			{
				continue;
			}
			if ( empty( $list ) )
			{
				continue;
			}
			foreach ( $list as $item )
			{
				switch ( $t )
				{
				case "TA" :
					$type = "";
					if ( sxd_check( $item[0], $object['TA'], $filter['TA'] ) )
					{
						$type = empty( $item[1] ) ? "TC" : "TA";
					}
					else if ( sxd_check( $item[0], $object['TC'], $filter['TC'] ) )
					{
						$type = "TC";
					}
					else
					{
						$todo[] = array(
							"TA",
							$item[0],
							"SKIP"
						);
						continue;
					}
					$todo[] = array(
						$type,
						$item[0],
						$item[1],
						$item[2]
					);
					$rows += $type == "TA" ? $item[1] : 0;
					break;
				default :
					if ( sxd_check( $item, $object[$t], $filter[$t] ) )
					{
						$todo[] = array(
							$t,
							$item,
							""
						);
						$skip = false;
					}
					else
					{
						$todo[] = array(
							$t,
							$item,
							"SKIP"
						);
					}
				}
			}
		}
		$this->JOB['file_tmp'] = $this->JOB['file_name'] = $this->CFG['backup_path'].$this->JOB['file'];
		$this->JOB['file_rtl'] = $this->CFG['backup_path'].$this->JOB['job'].".rtl";
		$this->JOB['file_log'] = $this->CFG['backup_path'].$this->JOB['job'].".log";
		$this->JOB['file_stp'] = $this->CFG['backup_path'].$this->JOB['job'].".stp";
		if ( !empty( $this->JOB['prefix_from'] ) )
		{
			preg_quote( $this->JOB['prefix_from'] );
		}
		if ( file_exists( $this->JOB['file_stp'] ) )
		{
			unlink( $this->JOB['file_stp'] );
		}
		$this->fh_tmp = $this->openFile( $this->JOB['file_tmp'], "r" );
		if ( is_null( $this->JOB['obj'] ) )
		{
			$s = fread( $this->fh_tmp, 2048 );
			if ( strpos( $s, "\r\n" ) )
			{
				$this->JOB['eol'] = "\r\n";
			}
			else if ( strpos( $s, "\n" ) )
			{
				$this->JOB['eol'] = "\n";
			}
			else
			{
				$this->JOB['eol'] = "\r";
			}
			$bom = strncmp( $s, "\xEF\xBB\xBF", 3 ) == 0 ? 3 : strncmp( $s, "\xFE\xFF", 2 ) == 0 || strncmp( $s, "\xFF\xFE", 2 ) == 0 ? 2 : 0;
			fseek( $this->fh_tmp, $bom );
		}
		$this->JOB['todo'] = $todo;
		$this->saveJob( $this->JOB['job'], $this->JOB );
		$this->fh_rtl = fopen( $this->JOB['file_rtl'], "wb" );
		$this->fh_log = fopen( $this->JOB['file_log'], "wb" );
		$this->rtl = array(
			time( ),
			time( ),
			$rows,
			0,
			"",
			"",
			"",
			0,
			0,
			0,
			0,
			TIMER,
			"\n"
		);
		$this->addLog( sprintf( $this->LNG['restore_begin'], $this->JOB['db'] ).( $this->JOB['savesql'] ? "{$this->LNG['infile']} `*.sxd.sql`" : "" ) );
		$this->addLog( "{$this->LNG['combo_file']} {$this->JOB['file']}" );
		$this->runRestoreJob( );
	}

	function runRestoreJob( $continue = false )
	{
		$ei = false;
		if ( $continue )
		{
			$this->fh_tmp = $this->openFile( $this->JOB['file_tmp'], "r" );
			fseek( $this->fh_tmp, $this->rtl[3] );
			if ( !empty( $this->rtl[6] ) )
			{
				$this->setNames( $this->JOB['correct'] == 1 && !empty( $this->JOB['charset'] ) ? $this->JOB['charset'] : $this->rtl[6] );
			}
			if ( $this->rtl[7] < $this->rtl[10] )
			{
				$ei = true;
			}
		}
		$h = str_replace( "www.", "", getenv( "SERVER_NAME" ) );
		mysql_select_db( $this->JOB['db'] );
		if ( is_null( $this->JOB['obj'] ) )
		{
			$this->runRestoreJobForeign( $continue );
		}
		mysql_query( "SET NAMES 'binary'" );
		$types = array( "VI" => "View", "PR" => "Procedure", "FU" => "Function", "TR" => "Trigger", "EV" => "Event" );
		$fcache = "";
		$writes = 0;
		$old_charset = "";
		$tab = "";
		$seek = 0;
		$this->rtl[3] = ftell( $this->fh_tmp );
		fseek( $this->fh_rtl, 0 );
		$this->rtl[1] = time( );
		fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
		$c = 0;
		switch ( $this->JOB['strategy'] )
		{
		case 1 :
			$tc = "TRUNCATE";
			$td = "INSERT";
			break;
		case 2 :
			$tc = "";
			$td = "REPLACE";
			break;
		case 3 :
			$tc = "";
			$td = "INSERT IGNORE";
			break;
		default :
			$tc = "DROP TABLE IF EXISTS";
			$td = "INSERT";
		}
		$tab_exists = array( );
		if ( 0 < $this->JOB['strategy'] )
		{
		        $r = mysql_query( "SHOW TABLES" ) or sxd_my_error( );
                        while ( $item = mysql_fetch_row( $r ) )
                        {
			    $tab_exists[$item[0]] = true;
                        }
		}
		$this->query = $query = $this->JOB['savesql'] ? "save_query" : "mysql_query";
		if ( $this->JOB['savesql'] && file_exists( $this->JOB['file_name'].".sxd.sql" ) )
		{
			unlink( $this->JOB['file_name'].".sxd.sql" );
		}
		$insert = $continue && $this->rtl[7] < $this->rtl[10] ? "{$td} INTO `{$this->rtl[5]}` VALUES " : "";
		if ( 40014 < V_MYSQL )
		{
			mysql_query( "SET UNIQUE_CHECKS=0" );
			mysql_query( "SET FOREIGN_KEY_CHECKS=0" );
			if ( 40101 < V_MYSQL )
			{
				mysql_query( "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'" );
			}
			if ( 40111 < V_MYSQL )
			{
				mysql_query( "SET SQL_NOTES=0" );
			}
		}
		$log_sql = false;
		$fields = "";
		$time_old = time( );
		$exit_time = $time_old + $this->CFG['time_web'] - 1;
		$prefix = empty( $this->JOB['prefix_from'] ) || empty( $this->JOB['prefix_to'] ) ? false : true;
		$skipto = $this->skipper( $this->JOB['todo'][0][0] );
		while ( $q = sxd_read_sql( $this->fh_tmp, $seek, $ei, $skipto ) )
		{
			if ( $time_old < time( ) )
			{
				if ( file_exists( $this->JOB['file_stp'] ) )
				{
					$type = file_get_contents( $this->JOB['file_stp'] );
					$this->rtl[9] = !empty( $type ) ? $type : 2;
					fseek( $this->fh_rtl, 0 );
					$this->rtl[1] = time( );
					fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
					unset( $this->rtl );
					exit;
				}
				$time_old = time( );
				if ( $exit_time <= $time_old )
				{
					$this->rtl[9] = 3;
					fseek( $this->fh_rtl, 0 );
					$this->rtl[1] = time( );
					fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
					unset( $this->rtl );
					exit;
				}
				clearstatcache( );
			}
			switch ( $q[0] )
			{
			case "(" :
				if ( $continue )
				{
					$this->addLog( sprintf( "{$this->LNG['restore_TC']} {$this->LNG['continue_from']}", $this->rtl[5], $this->rtl[3] ) );
					$continue = false;
				}
				$q = $insert.$q;
				$ex = 1;
				$c = 1;
				break;
			case "I" :
				if ( preg_match( "/^INSERT( INTO `(.+?)`) VALUES/", $q, $m ) )
				{
					$insert = $td.$m[1].$fields." VALUES \n";
					$tab = $m[2];
					$this->rtl[7] = 0;
					$this->rtl[8] = 0;
					foreach ( $this->JOB['todo'] as $t )
					{
						$this->rtl[8] = $t[2];
					}
					if ( $prefix )
					{
						$insert = preg_replace( "/`{$this->JOB['prefix_from']}(.+?)`/", "`{$this->JOB['prefix_to']}\\1`", $insert );
						$tab = preg_replace( "/^{$this->JOB['prefix_from']}(.+?)/", "{$this->JOB['prefix_to']}\\1", $tab );
						$q = substr_replace( $q, $insert, 0, strlen( $m[0] ) + 2 );
					}
					if ( $this->JOB['strategy'] )
					{
						$q = substr_replace( $q, $insert, 0, strlen( $m[0] ) + 2 );
					}
					mysql_query( "ALTER TABLE `{$tab}` DISABLE KEYS" ) or sxd_my_error( );
					$ex = 1;
				}
				break;
			case "C" :
				$ex = 1;
				if ( preg_match( "/^CREATE TABLE `/", $q ) )
				{
					if ( $this->JOB['strategy'] != 0 && isset( $tab_exists[$this->rtl[5]] ) )
					{
						$ex = 0;
					}
					else
					{
						$ex = 1;
						if ( $prefix )
						{
							$q = preg_replace( "/^CREATE TABLE `{$this->JOB['prefix_from']}(.+?)` \\(/", "CREATE TABLE `{$this->JOB['prefix_to']}\\1` (", $q );
						}
						if ( !empty( $this->JOB['correct'] ) && !empty( $this->JOB['charset'] ) )
						{
							$q = preg_replace( "/(DEFAULT)?\\s*(CHARSET|CHARACTER SET|COLLATE)[=\\s]+\\w+/i", "", $q ).( V_MYSQL < 40100 ? "" : " DEFAULT CHARSET=".$this->JOB['charset'] );
						}
						if ( !empty( $this->JOB['autoinc'] ) )
						{
							$q = preg_replace( "/AUTO_INCREMENT=\\d+/", "AUTO_INCREMENT=1", $q );
						}
					}
					$fields = 0 < $this->JOB['strategy'] && preg_match_all( "/^\\s+(`.+?`) /m", $q, $f, PREG_PATTERN_ORDER ) ? "(".implode( ",", $f[1] ).")" : "";
				}
				break;
			case "#" :
				if ( preg_match( "/\#\t(TC|TD|VI|PR|FU|TR|EV)`(.+?)`(([^_]+?)_.+?)?$/", $q, $m ) )
				{
					$skipto = $this->skipper( $m[1], $m[2] );
					if ( $skipto )
					{
						$ex = 0;
						continue;
					}
					$this->setNames( $this->JOB['correct'] == 1 && !empty( $this->JOB['charset'] ) ? $this->JOB['charset'] : empty( $m[3] ) ? "" : $m[3] );
					$m[2] = preg_replace( "/^{$this->JOB['prefix_from']}(.+?)/", "{$this->JOB['prefix_to']}\\1", $m[2] );
					if ( $m[1] == "TC" )
					{
						$this->addLog( sprintf( $this->LNG['restore_TC'], $m[2] ) );
						$insert = "";
						$tab = "";
						$this->rtl[4] = "TD";
						$this->rtl[5] = $m[2];
						$ei = 0;
						if ( $tc && ( $this->JOB['strategy'] == 0 || isset( $tab_exists[$m[2]] ) ) )
						{
							mysql_query( "{$tc} `{$m[2]}`" ) or sxd_my_error( );
						}
					}
					else if ( $m[1] == "TD" )
					{
						$ei = 1;
					}
					else
					{
						$this->rtl[4] = $m[1];
						$this->rtl[5] = $m[2];
						$this->rtl[7] = 0;
						$this->rtl[8] = 0;
						mysql_query( "DROP {$types[$m[1]]} IF EXISTS `{$m[2]}`" ) or sxd_my_error( );
						$this->addLog( sprintf( $this->LNG["restore_{$m[1]}"], $m[2] ) );
						$ei = 0;
					}
				}
				$ex = 0;
				break;
			default :
				$insert = "";
				$ex = 1;
			}
			if ( $ex )
			{
				$this->rtl[3] = ftell( $this->fh_tmp ) - $seek;
				fseek( $this->fh_rtl, 0 );
				$this->rtl[1] = time( );
				fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
				if ( mysql_query( $q ) )
				{
					if ( $insert )
					{
						$c = 1;
					}
				}
				else
				{
					error_log( "----------\n{$q}\n", 3, "error.log" );
					sxd_my_error( );
				}
				if ( $c )
				{
					$i = $this->JOB['savesql'] ? $this->nl_count : mysql_affected_rows( );
					$this->rtl[3] = ftell( $this->fh_tmp ) - $seek;
					$this->rtl[7] += $i;
					$this->rtl[10] += $i;
					fseek( $this->fh_rtl, 0 );
					$this->rtl[1] = time( );
					fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
					$c = 1;
				}
			}
		}
		if ( !$this->JOB['savesql'] )
		{
			$this->addLog( $this->LNG['restore_keys'] );
			$this->rtl[4] = "EK";
			$this->rtl[5] = "";
			$this->rtl[6] = "";
			$this->rtl[7] = 0;
			$this->rtl[8] = 0;
			foreach ( $this->JOB['todo'] as $tab )
			{
				if ( $tab[0] == "TA" && $tab[2] != "SKIP" )
				{
					if ( $prefix )
					{
						$tab[1] = preg_replace( "/^{$this->JOB['prefix_from']}(.+?)/", "{$this->JOB['prefix_to']}\\1", $tab[1] );
					}
					$this->rtl[1] = time( );
					$this->rtl[5] = $tab[1];
					fseek( $this->fh_rtl, 0 );
					fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
				}
			}
		}
		else
		{
			$this->rtl[7] = 0;
			$this->rtl[8] = 0;
		}
		$this->rtl[4] = "EOJ";
		$this->rtl[5] = round( array_sum( explode( " ", microtime( ) ) ) - $this->rtl[11], 4 );
		fseek( $this->fh_rtl, 0 );
		fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
		$this->addLog( sprintf( $this->LNG['restore_end'], $this->JOB['db'] ) );
		fclose( $this->fh_log );
		fclose( $this->fh_rtl );
	}

	function runRestoreJobForeign( $continue = false )
	{
		$ei = false;
		$fcache = "";
		$writes = 0;
		$old_charset = "";
		$tab = "";
		$seek = 0;
		$this->rtl[3] = ftell( $this->fh_tmp );
		fseek( $this->fh_rtl, 0 );
		$this->rtl[1] = time( );
		fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
		$c = 0;
		$log_sql = false;
		$fields = "";
		$insert = "";
		$last_tab = "";
		$time_old = time( );
		$exit_time = $time_old + $this->CFG['time_web'] - 1;
		$delimiter = ";";
		$this->query = "mysql_query";
		while ( $q = sxd_read_foreign_sql( $this->fh_tmp, $seek, $ei, $delimiter, $this->JOB['eol'] ) )
		{
			$q = ltrim( $q );
			if ( empty( $q ) )
			{
				break;
			}
			if ( $time_old < time( ) )
			{
				if ( file_exists( $this->JOB['file_stp'] ) )
				{
					$type = file_get_contents( $this->JOB['file_stp'] );
					$this->rtl[9] = !empty( $type ) ? $type : 2;
					fseek( $this->fh_rtl, 0 );
					$this->rtl[1] = time( );
					fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
					unset( $this->rtl );
					exit;
				}
				$time_old = time( );
				if ( $exit_time <= $time_old )
				{
					$this->rtl[9] = 3;
					fseek( $this->fh_rtl, 0 );
					$this->rtl[1] = time( );
					fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
					unset( $this->rtl );
					exit;
				}
				clearstatcache( );
			}
			do
			{
				$repeat = false;
				switch ( $q[0] )
				{
				case "(" :
					if ( $continue )
					{
						$this->addLog( sprintf( "{$this->LNG['restore_TC']} {$this->LNG['continue_from']}", $this->rtl[5], $this->rtl[3] ) );
						$continue = false;
					}
					$q = $insert.$q;
					$ex = 1;
					$c = 1;
					break;
				case "I" :
					if ( preg_match( "/^(INSERT( INTO `?(.+?)`?).+?\sVALUES)/s", $q, $m ) )
					{
						$insert = trim( $m[1] )." ";
						$tab = $m[3];
						$this->rtl[7] = 0;
						$this->rtl[8] = 0;
						$ex = 1;
					}
					break;
				case "C" :
					$ex = 1;
					$ei = 1;
					if ( preg_match( "/^CREATE TABLE.+?`(.+?)`/", $q, $m ) )
					{
						$ex = 1;
						$tab = $m[1];
						$this->addLog( sprintf( $this->LNG['restore_TC'], $tab ) );
						if ( !empty( $this->JOB['correct'] ) && !empty( $this->JOB['charset'] ) )
						{
							$q = preg_replace( "/(DEFAULT)?\s*(CHARSET|CHARACTER SET|COLLATE)[=\s]+\w+/i", "", $q ).( V_MYSQL < 40100 ? "" : " DEFAULT CHARSET=".$this->JOB['charset'] );
						}
						else if ( empty( $this->JOB['charset'] ) && preg_match( "/(CHARACTER SET|CHARSET)[=\s]+(\w+)/i", $q, $charset ) )
						{
							$this->setNames( $charset[2] );
						}
					}
					break;
				case "-" && $q[1] == "-" :
				case "#" :
					$repeat = true;
					$q = ltrim( substr( $q, strpos( $q, $this->JOB['eol'] ) ) );
					$ex = 0;
					break;
				case "/" :
				case "S" :
					if ( preg_match( "/SET NAMES (\w+)/", $q, $m ) )
					{
						$this->JOB['charset'] = $m[1];
						$this->setNames( $this->JOB['charset'] );
						$ex = 0;
					}
					$ex = 1;
					break;
				case "D" :
					if ( preg_match( "/^DELIMITER (.+?)\s/s", $q, $m ) )
					{
						$q = ltrim( substr( $q, strpos( $q, $this->JOB['eol'] ) ) ).$delimiter.$this->JOB['eol'];
						$delimiter = $m[1];
						$this->addLog( sprintf( "Установлен разделитель '%s'", $delimiter ) );
						$q .= ltrim( sxd_read_foreign_sql( $this->fh_tmp, $seek, $ei, $delimiter, $this->JOB['eol'] ) );
						$delimiter = $m[1];
						$ex = 1;
					}
					else
					{
						$insert = "";
						$ex = 1;
						$ei = 0;
					}
					break;
				default :
					$insert = "";
					$ex = 1;
					$ei = 0;
				}
			} while ( $repeat );
			if ( $ex )
			{
				$this->rtl[3] = ftell( $this->fh_tmp ) - $seek;
				fseek( $this->fh_rtl, 0 );
				$this->rtl[1] = time( );
				fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
				if ( mysql_query( $q ) )
				{
					if ( $insert )
					{
						$c = 1;
					}
				}
				else
				{
					error_log( "-----------------\n{$q}\n", 3, "error.log" );
					sxd_my_error( );
				}
				if ( $c )
				{
					$i = mysql_affected_rows( );
					$this->rtl[3] = ftell( $this->fh_tmp ) - $seek;
					$this->rtl[7] += $i;
					$this->rtl[10] += $i;
					fseek( $this->fh_rtl, 0 );
					$this->rtl[1] = time( );
					fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
					$c = 1;
				}
			}
		}
		$this->rtl[4] = "EOJ";
		$this->rtl[5] = round( array_sum( explode( " ", microtime( ) ) ) - $this->rtl[11], 4 );
		$this->rtl[7] = 0;
		$this->rtl[8] = 0;
		fseek( $this->fh_rtl, 0 );
		fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
		$this->addLog( sprintf( $this->LNG['restore_end'], $this->JOB['db'] ) );
		fclose( $this->fh_log );
		fclose( $this->fh_rtl );
	}

	function addBackupJob( $job )
	{
		$this->closeConnect( );
		$this->JOB = $job;
		mysql_select_db( $this->JOB['db'] );
		$filter = $object = array( );
		$this->createFilters( $this->JOB['obj'], $filter, $object );
		$queries = array(
			array( "TABLE STATUS", "Name", "TA" )
		);
		if ( 50014 < V_MYSQL )
		{
			$queries[] = array(
				"PROCEDURE STATUS WHERE db='{$this->JOB['db']}'",
				"Name",
				"PR"
			);
			$queries[] = array(
				"FUNCTION STATUS WHERE db='{$this->JOB['db']}'",
				"Name",
				"FU"
			);
			$queries[] = array( "TRIGGERS", "Trigger", "TR" );
			if ( 50100 < V_MYSQL )
			{
				$queries[] = array( "EVENTS", "Name", "EV" );
			}
		}
		$todo = $header = array( );
		$tabs = $rows = 0;
		$only_create = explode( " ", $this->CFG['only_create'] );
		foreach ( $queries as $query )
		{
			$t = $query[2];
			if ( $t == "TA" && ( !empty( $object['TC'] ) || !empty( $filter['TC'] ) ) )
			{
			}
			else if ( empty( $object[$t] ) && empty( $filter[$t] ) )
			{
				continue;
			}
			$r = mysql_query( "SHOW ".$query[0] ) or sxd_my_error( );
			if ( !$r )
			{
				continue;
			}
			$todo[$t] = array( );
			$header[$t] = array( );
			while ( $item = mysql_fetch_assoc( $r ) )
			{
				$n = $item[$query[1]];
				switch ( $t )
				{
				case "TA" :
				case "TC" :
					if ( 40101 < V_MYSQL && is_null( $item['Engine'] ) && preg_match( "/^VIEW/i", $item['Comment'] ) )
					{
						if ( sxd_check( $n, $object['VI'], $filter['VI'] ) )
						{
							$todo['VI'] = array( );
							$header['VI'] = array( );
						}
						continue;
					}
					if ( sxd_check( $n, $object['TA'], $filter['TA'] ) )
					{
						$engine = 40101 < V_MYSQL ? $item['Engine'] : $item['Type'];
						$t = in_array( $engine, $only_create ) ? "TC" : "TA";
					}
					else if ( sxd_check( $n, $object['TC'], $filter['TC'] ) )
					{
						$t = "TC";
						$item['Rows'] = $item['Data_length'] = "";
					}
					else
					{
						continue;
					}
					$todo['TA'][] = array(
						$t,
						$n,
						!empty( $item['Collation'] ) ? $item['Collation'] : "",
						$item['Auto_increment'],
						$item['Rows'],
						$item['Data_length']
					);
					$header['TA'][] = "{$n}`{$item['Rows']}`{$item['Data_length']}";
					++$tabs;
					$rows += $item['Rows'];
					break;
				default :
					if ( sxd_check( $n, $object[$t], $filter[$t] ) )
					{
						$todo[$t][] = array(
							$t,
							$n,
							!empty( $item['collation_connection'] ) ? $item['collation_connection'] : ""
						);
						$header[$t][] = $n;
					}
				}
			}
		}
		if ( 50014 < V_MYSQL && ( !empty( $object['VI'] ) || !empty( $filter['VI'] ) ) )
		{
			$r = mysql_query( "SELECT table_name, view_definition /*!50121 , collation_connection */ FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = '{$this->JOB['db']}'" ) or sxd_my_error( );
			$views = $dumped = $views_collation = array( );
			$re = "/`{$this->JOB['db']}`.`(.+?)`/";
			while ( $item = mysql_fetch_assoc( $r ) )
			{
				preg_match_all( $re, preg_replace( "/^select.+? from/i", "", $item['view_definition'] ), $m );
				$used = $m[1];
				$views_collation[$item['table_name']] = !empty( $item['collation_connection'] ) ? $item['collation_connection'] : "";
				$views[$item['table_name']] = $used;
			}
			while ( 0 < count( $views ) )
			{
				foreach ( $views as $n => $view )
				{
					$can_dumped = true;
					foreach ( $view as $k )
					{
						if ( isset( $views[$k] ) && !isset( $dumped[$k] ) )
						{
							$can_dumped = false;
						}
					}
					if ( $can_dumped )
					{
						if ( sxd_check( $n, $object['VI'], $filter['VI'] ) )
						{
							$todo['VI'][] = array(
								"VI",
								$n,
								$views_collation[$n]
							);
							$header['VI'][] = $n;
						}
						$dumped[$n] = 1;
						unset( $views[$n] );
					}
				}
			}
			unset( $dumped );
			unset( $views );
			unset( $views_collation );
		}
		$this->JOB['file_tmp'] = $this->CFG['backup_path'].$this->JOB['job'].".tmp";
		$this->JOB['file_rtl'] = $this->CFG['backup_path'].$this->JOB['job'].".rtl";
		$this->JOB['file_log'] = $this->CFG['backup_path'].$this->JOB['job'].".log";
		$this->JOB['file_stp'] = $this->CFG['backup_path'].$this->JOB['job'].".stp";
		if ( !empty( $this->JOB['outfile'] ) )
		{
			$this->JOB['file_buf'] = ( preg_match( "/^([a-z]:|\\/)/", $this->CFG['outfile_path'] ) ? "" : strtr( dirname( __FILE__ ), "\\", "/" )."/" ).$this->CFG['outfile_path'].$this->JOB['job'].".buf";
		}
		if ( file_exists( $this->JOB['file_stp'] ) )
		{
			unlink( $this->JOB['file_stp'] );
		}
		$this->fh_tmp = $this->openFile( $this->JOB['file_tmp'], "w" );
		$this->JOB['file'] = sprintf( "%s_%s.%s", isset( $this->JOB['title'] ) ? $this->JOB['job'] : $this->JOB['db'], date( "Y-m-d_H-i-s" ), $this->JOB['file_ext'] );
		$this->JOB['file_name'] = $this->CFG['backup_path'].$this->JOB['file'];
		$this->JOB['todo'] = $todo;
		$this->saveJob( $this->JOB['job'], $this->JOB );
		$fcache = implode( "|", array(
			"#SXD20",
			V_SXD,
			V_MYSQL,
			V_PHP,
			date( "Y.m.d H:i:s" ),
			$this->JOB['db'],
			$this->JOB['charset'],
			$tabs,
			$rows,
			mysql_escape_string( $this->JOB['comment'] )
		) )."\n";
		foreach ( $header as $t => $o )
		{
			if ( !empty( $o ) )
			{
				$fcache .= "#{$t} ".implode( "|", $o )."\n";
			}
		}
		$this->fh_rtl = fopen( $this->JOB['file_rtl'], "wb" );
		$this->fh_log = fopen( $this->JOB['file_log'], "wb" );
		$this->rtl = array(
			time( ),
			time( ),
			$rows,
			0,
			"",
			"",
			"",
			0,
			0,
			0,
			0,
			TIMER,
			"\n"
		);
		$fcache .= "#EOH\n\n";
		$this->write( $fcache );
		$this->addLog( sprintf( $this->LNG['backup_begin'], $this->JOB['db'] ) );
		$this->runBackupJob( );
	}

	function runBackupJob( $continue = false )
	{
		if ( $continue )
		{
			$this->fh_tmp = $this->openFile( $this->JOB['file_tmp'], "a" );
			mysql_select_db( $this->JOB['db'] );
		}
		mysql_query( "SET SQL_QUOTE_SHOW_CREATE = 1" );
		$types = array( "VI" => "View", "PR" => "Procedure", "FU" => "Function", "TR" => "Trigger", "EV" => "Event" );
		$fcache = "";
		$writes = 0;
		if ( 40101 < V_MYSQL )
		{
			mysql_query( "SET SESSION character_set_results = '".( $this->JOB['charset'] ? $this->JOB['charset'] : "binary" )."'" ) or sxd_my_error( );
		}
		$time_old = time( );
		$exit_time = $time_old + $this->CFG['time_web'] - 1;
		$no_cache = V_MYSQL < 40101 ? "SQL_NO_CACHE " : "";
		foreach ( $this->JOB['todo'] as $t => $o )
		{
			if ( empty( $this->rtl[4] ) )
			{
				$this->rtl[4] = $t;
			}
			else if ( $this->rtl[4] != $t )
			{
				continue;
			}
			foreach ( $o as $n )
			{
				if ( empty( $this->rtl[5] ) )
				{
					$this->rtl[5] = $n[1];
					$this->rtl[7] = 0;
					$this->rtl[8] = !empty( $n[4] ) ? $n[4] : 0;
				}
				else if ( $this->rtl[5] != $n[1] )
				{
					continue;
				}
				switch ( $n[0] )
				{
				case "TC" :
				case "TD" :
				case "TA" :
					$from = "";
					if ( $n[0] == "TC" || $this->rtl[7] == 0 )
					{
						$r = mysql_query( "SHOW CREATE TABLE `{$n[1]}`" ) or sxd_my_error( );
						$item = mysql_fetch_assoc( $r );
						$fcache .= "#\tTC`{$n[1]}`{$n[2]}\t;\n{$item['Create Table']}\t;\n";
						$this->addLog( sprintf( $this->LNG['backup_TC'], $n[1] ) );
						$this->rtl[7] = 0;
						if ( $n[0] == "TC" || !$n[4] )
						{
							break;
						}
						$fcache .= "#\tTD`{$n[1]}`{$n[2]}\t;\nINSERT INTO `{$n[1]}` VALUES \n";
					}
					else
					{
						$from = " LIMIT {$this->rtl[7]}, {$this->rtl[8]}";
						$this->addLog( sprintf( "{$this->LNG['backup_TC']} {$this->LNG['continue_from']}", $n[1], $this->rtl[7] ) );
					}
					if ( $this->JOB['outfile'] == 1 )
					{
						$buffer = $this->CFG['outfile_size'] * 1024 * 1024;
						$limit = 1 + floor( $buffer / ( $n[5] / $n[4] ) );
						fwrite( $this->fh_tmp, "{$fcache}" );
						$fcache = "";
						$i = 0;
						while ( $i < $n[4] )
						{
							if ( file_exists( $this->JOB['file_buf'] ) )
							{
								unlink( $this->JOB['file_buf'] );
							}
							if ( $i )
							{
								fwrite( $this->fh_tmp, ",\n" );
							}
							fwrite( $this->fh_tmp, "(" );
							mysql_query( "SELECT * INTO OUTFILE '{$this->JOB['file_buf']}' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY \"'\" LINES TERMINATED BY '\\\x00\\\x00\\\x00\\\x00' FROM `{$n[1]}`".( $n[5] < $buffer ? "" : " LIMIT {$i}, {$limit}" ) ) or sxd_my_error( );
							$fi = fopen( $this->JOB['file_buf'], "r+" );
							$z = 0;
							ftruncate( $fi, filesize( $this->JOB['file_buf'] ) - 4 );
							while ( !feof( $fi ) && ( $fcache = fread( $fi, 61440 ) ) )
							{
								if ( substr( $fcache, - 1 ) == "\x00" )
								{
									$fcache .= fread( $fi, 3 );
								}
								$z = substr_count( $fcache, "\x00\x00\x00\x00" );
								$this->rtl[7] += $z;
								$this->rtl[10] += $z;
								$fcache = str_replace( array( "\n", "\r", "\x00\x00\x00\x00" ), array( "\\n", "\\r", "),\n(" ), $fcache );
								$this->write( $fcache );
							}
							fclose( $fi );
							fwrite( $this->fh_tmp, ")" );
							++$this->rtl[7];
							++$this->rtl[10];
							$i += $limit;
						}
						@unlink( $this->JOB['file_buf'] );
						fwrite( $this->fh_tmp, "\t;\n" );
					}
					else
					{
						$notNum = array( );
						$r = mysql_query( "SHOW COLUMNS FROM `{$n[1]}`" ) or sxd_my_error( );
						$fields = 0;
						while ( $col = mysql_fetch_array( $r ) )
						{
							$notNum[$fields] = preg_match( "/^(tinyint|smallint|mediumint|bigint|int|float|double|real|decimal|numeric|year)/", $col['Type'] ) ? 0 : 1;
							++$fields;
						}
						$time_old = time( );
						$z = 0;
						$r = mysql_unbuffered_query( "SELECT {$no_cache}* FROM `{$n[1]}`{$from}" );
						while ( $row = mysql_fetch_row( $r ) )
						{
							if ( 61440 <= strlen( $fcache ) )
							{
								$z = 0;
								if ( $time_old < time( ) )
								{
									if ( file_exists( $this->JOB['file_stp'] ) )
									{
										$type = file_get_contents( $this->JOB['file_stp'] );
										$this->rtl[9] = !empty( $type ) ? $type : 2;
										$this->write( $fcache );
										if ( $type == 1 )
										{
										}
										unset( $this->rtl );
										exit;
									}
									$time_old = time( );
									if ( $exit_time <= $time_old )
									{
										$this->rtl[9] = 3;
										$this->write( $fcache );
										unset( $this->rtl );
										exit;
									}
									clearstatcache( );
								}
								$this->write( $fcache );
							}
							$k = 0;
							while ( $k < $fields )
							{
								if ( !isset( $row[$k] ) )
								{
									$row[$k] = "\N";
								}
								else if ( $notNum[$k] )
								{
									$row[$k] = "'".mysql_escape_string( $row[$k] )."'";
								}
								++$k;
							}
							$fcache .= "(".implode( ",", $row )."),\n";
							++$this->rtl[7];
							++$this->rtl[10];
						}
						unset( $row );
						mysql_free_result( $r );
						$fcache = substr_replace( $fcache, "\t;\n", - 2, 2 );
					}
					break;
				default :
					if ( V_MYSQL < 50121 && $n[0] == "TR" )
					{
						$r = mysql_query( "SELECT * FROM `INFORMATION_SCHEMA`.`TRIGGERS` WHERE `TRIGGER_SCHEMA` = '{$this->JOB['db']}' AND `TRIGGER_NAME` = '{$n[1]}'" ) or sxd_my_error( );
						$item = mysql_fetch_assoc( $r );
						$fcache .= "#\tTR`{$n[1]}`{$n[2]}\t;\nCREATE TRIGGER `{$item['TRIGGER_NAME']}` {$item['ACTION_TIMING']} {$item['EVENT_MANIPULATION']} ON `{$item['EVENT_OBJECT_TABLE']}` FOR EACH ROW {$item['ACTION_STATEMENT']}\t;\n";
					}
					else
					{
						$this->addLog( sprintf( $this->LNG["backup_".$n[0]], $n[1] ) );
						$r = mysql_query( "SHOW CREATE {$types[$n[0]]} `{$n[1]}`" ) or sxd_my_error( );
						$item = mysql_fetch_assoc( $r );
						$fcache .= "#\t{$n[0]}`{$n[1]}`{$n[2]}\t;\n".preg_replace( "/DEFINER=`.+?`@`.+?` /", "", $n[0] == "TR" ? $item['SQL Original Statement'] : $item["Create ".$types[$n[0]]] )."\t;\n";
					}
				}
				$this->rtl[5] = "";
			}
			$this->rtl[4] = "";
		}
		$this->rtl[5] = round( array_sum( explode( " ", microtime( ) ) ) - $this->rtl[11], 4 );
		$this->rtl[6] = "";
		$this->rtl[7] = 0;
		$this->rtl[8] = 0;
		$this->write( $fcache );
		fclose( $this->fh_tmp );
		rename( $this->JOB['file_tmp'], $this->JOB['file_name'] );
		$this->addLog( sprintf( $this->LNG['backup_end'], $this->JOB['db'] ) );
		if ( $this->JOB['del_time'] || $this->JOB['del_count'] )
		{
			$this->addLog( $this->LNG['autodelete'] );
			$deldate = "";
			if ( !empty( $this->JOB['del_time'] ) )
			{
				$deldate = date( "Y-m-d_H-i-s", time( ) - intval( $this->JOB['del_time'] ) * 86400 );
			}
			$deleted = false;
			if ( $dh = opendir( $this->CFG['backup_path'] ) )
			{
				$files = array( );
				$name = isset( $this->JOB['title'] ) ? $this->JOB['job'] : $this->JOB['db'];
				while ( false !== ( $file = readdir( $dh ) ) )
				{
					if ( preg_match( "/^{$name}_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.sql/", $file, $m ) )
					{
						if ( $deldate && $m[1] < $deldate )
						{
							if ( unlink( $this->CFG['backup_path'].$file ) )
							{
								$this->addLog( sprintf( $this->LNG['del_by_date'], $file ) );
							}
							else
							{
								$this->addLog( sprintf( $this->LNG['del_fail'], $file ) );
							}
							$deleted = true;
						}
						else
						{
							$files[$m[1]] = $file;
						}
					}
				}
				closedir( $dh );
				if ( !empty( $this->JOB['del_count'] ) )
				{
					ksort( $files );
					$file_to_delete = count( $files ) - $this->JOB['del_count'];
					foreach ( $files as $file )
					{
						if ( 0 < $file_to_delete-- )
						{
							if ( unlink( $this->CFG['backup_path'].$file ) )
							{
								$this->addLog( sprintf( $this->LNG['del_by_count'], $file ) );
							}
							else
							{
								$this->addLog( sprintf( $this->LNG['del_fail'], $file ) );
							}
							$deleted = true;
						}
					}
				}
			}
			if ( !$deleted )
			{
				$this->addLog( $this->LNG['del_nothing'] );
			}
		}
		fclose( $this->fh_log );
		$this->rtl[4] = "EOJ";
		fseek( $this->fh_rtl, 0 );
		fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
		fclose( $this->fh_rtl );
		unset( $this->rtl );
	}

	function setNames( $collation )
	{
		if ( empty( $collation ) )
		{
			return;
		}
		if ( $this->rtl[6] != $collation )
		{
			//mysql_query = $this->query; //g_m
			mysql_query('SET NAMES \'' . preg_replace('/^(\w+?)_/', '\\1\' COLLATE \'\\1_', $collation) . '\'') or sxd_my_error();
			$this->addLog( sprintf( $this->LNG['set_names'], $collation ) );
			$this->rtl[6] = $collation;
		}
	}

	function write( &$str )
	{
		fseek( $this->fh_rtl, 0 );
		$this->rtl[1] = time( );
		$this->rtl[3] += fwrite( $this->fh_tmp, $str );
		fwrite( $this->fh_rtl, implode( "\t", $this->rtl ) );
		$str = "";
	}

	function addLog( $str, $type = 1 )
	{
		fwrite( $this->fh_log, date( "Y.m.d H:i:s" )."\t{$type}\t{$str}\n" );
	}

	function getDBList( )
	{
		$dbs = $items = array( );
		if ( !V_MYSQL )
		{
			return $dbs;
		}
		$qq = V_MYSQL < 50000 ? "" : "'";
		if ( $this->CFG['my_db'] )
		{
			$tmp = explode( ",", $this->CFG['my_db'] );
			foreach ( $tmp as $d )
			{
				$d = trim( $d );
				$items[] = $qq.mysql_escape_string( $d ).$qq;
				$dbs[$d] = "{$d} (0)";
			}
		}
		else
		{
			$result = mysql_query( "SHOW DATABASES" ) or sxd_my_error( );
			while ( $item = mysql_fetch_row( $result ) )
			{
				if ( $item[0] == "information_schema" || $item[0] == "mysql" )
				{
					continue;
				}
				$items[] = $qq.mysql_escape_string( $item[0] ).$qq;
				$dbs[$item[0]] = "{$item[0]} (0)";
			}
		}
		if ( V_MYSQL < 50000 )
		{
			foreach ( $items as $item )
			{
				$tables = mysql_query( "SHOW TABLES FROM `{$item}`" ) or sxd_my_error( );
				if ( $tables )
				{
					$tabs = mysql_num_rows( $tables );
					$dbs[$item] = "{$item} ({$tabs})";
				}
			}
		}
		else
		{
			$where = 0 < count( $items ) ? "WHERE `table_schema` IN (".implode( ",", $items ).")" : "";
			$result = mysql_query( "SELECT `table_schema`, COUNT(*) FROM `information_schema`.`tables` {$where} GROUP BY `table_schema`" ) or sxd_my_error( );
			while ( $item = mysql_fetch_row( $result ) )
			{
				if ( $item[0] == "information_schema" || $item[0] == "mysql" )
				{
					continue;
				}
				$dbs[$item[0]] = "{$item[0]} ({$item[1]})";
			}
		}
		return $dbs;
	}

	function getCharsetList( )
	{
		$tmp = array( 0 => "- auto -" );
		if ( !V_MYSQL )
		{
			return $tmp;
		}
		if ( 40101 < V_MYSQL )
		{
			$def_charsets = "";
			if ( !empty( $this->CFG['charsets'] ) )
			{
				$def_charsets = preg_match_all( "/([\w*?]+)\s*/", $this->CFG['charsets'], $m, PREG_PATTERN_ORDER ) ? "/^(".str_replace( array( "?", "*" ), array( ".", "\w+?" ), implode( "|", $m[1] ) ).")$/i" : "";
			}
			$r = mysql_query( "SHOW CHARACTER SET" ) or sxd_my_error( );
			while ( $r && ( $item = mysql_fetch_assoc( $r ) ) )
			{
				if ( empty( $def_charsets ) || preg_match( $def_charsets, $item['Charset'] ) )
				{
					$tmp[$item['Charset']] = "{$item['Charset']}";
				}
			}
		}
		return $tmp;
	}

	function getCollationList( )
	{
		$tmp = array( );
		if ( !V_MYSQL )
		{
			return $tmp;
		}
		if ( 40101 < V_MYSQL )
		{
			$def_charsets = "";
			if ( !empty( $this->CFG['charsets'] ) )
			{
				$def_charsets = preg_match_all( "/([\w*?]+)\s*/", $this->CFG['charsets'], $m, PREG_PATTERN_ORDER ) ? "/^(".str_replace( array( "?", "*" ), array( ".", "\\w+?" ), implode( "|", $m[1] ) ).")$/i" : "";
			}
			$r = mysql_query( "SHOW COLLATION" ) or sxd_my_error( );
			while ( $r && ( $item = mysql_fetch_assoc( $r ) ) )
			{
				if ( empty( $def_charsets ) || preg_match( $def_charsets, $item['Charset'] ) )
				{
					$tmp[$item['Charset']][$item['Collation']] = $item['Default'] == "Yes" ? 1 : 0;
				}
			}
		}
		return $tmp;
	}

	function getObjects( $tree, $db_name )
	{
		mysql_select_db( $db_name );
		$r = mysql_query( "SHOW TABLE STATUS" );
		$tab_prefix_last = $tab_prefix = "*";
		$objects = array(
			"TA" => array( ),
			"VI" => array( ),
			"PR" => array( ),
			"FU" => array( ),
			"TR" => array( ),
			"EV" => array( )
		);
		if ( $r )
		{
			while ( $item = mysql_fetch_assoc( $r ) )
			{
				if ( 40101 < V_MYSQL && is_null( $item['Engine'] ) && preg_match( "/^VIEW/i", $item['Comment'] ) )
				{
					$objects['VI'][] = $item['Name'];
				}
				else
				{
					$objects['TA'][] = array(
						$item['Name'],
						$item['Rows'],
						$item['Data_length']
					);
				}
			}
			if ( 50014 < V_MYSQL && $tree != "services" )
			{
				$shows = array(
					"PROCEDURE STATUS WHERE db='{$db_name}'",
					"FUNCTION STATUS WHERE db='{$db_name}'",
					"TRIGGERS"
				);
				if ( 50100 < V_MYSQL )
				{
					$shows[] = "EVENTS WHERE db='{$db_name}'";
				}
				for ( $i = 0, $l = count( $shows ); $i < $l ; $i++ )
				{
					$r = mysql_query( "SHOW ".$shows[$i] );
					if ( $r && 0 < mysql_num_rows( $r ) )
					{
						$col_name = $shows[$i] == "TRIGGERS" ? "Trigger" : "Name";
						$type = substr( $shows[$i], 0, 2 );
						while ( $item = mysql_fetch_assoc( $r ) )
						{
							$objects[$type][] = $item[$col_name];
						}
					}
				}
			}
			else
			{
				$objects['VI'] = array( );
			}
		}
		return $this->formatTree( $tree, $objects );
	}

	function getFileObjects( $tree, $name, $formatTree = true )
	{
		$objects = array(
			"TA" => array( ),
			"VI" => array( ),
			"PR" => array( ),
			"FU" => array( ),
			"TR" => array( ),
			"EV" => array( )
		);
		if ( !preg_match( "/\.sql(\.(gz|bz2))?$/i", $name, $m ) )
		{
			return "";
		}
		$name = $this->CFG['backup_path'].$name;
		if ( !is_readable( $name ) )
		{
			return "sxd.tree.{$tree}.error(sxd.lng('err_fopen'))";
		}
		$fp = $this->openFile( $name, "r" );
		$temp = fread( $fp, 60000 );
		if ( preg_match( "/^(#SXD20\|.+?)\n#EOH\n/s", $temp, $m ) )
		{
			$head = explode( "\n", $m[1] );
			$h = explode( "|", $head[0] );
			$i = 1;
			$c = count( $head );
			while ( $i < $c )
			{
				$objects[substr( $head[$i], 1, 2 )] = explode( "|", substr( $head[$i], 4 ) );
				++$i;
			}
			$i = 0;
			$l = count( $objects['TA'] );
			while ( $i < $l )
			{
				$objects['TA'][$i] = explode( "`", $objects['TA'][$i] );
				++$i;
			}
		}
		else
		{
			$h[9] = "";
		}
		return $formatTree ? $this->formatTree( $tree, $objects )."sxd.comment.restore.value = '{$h[9]}';z('restore_savejob').disabled = z('restore_runjob').disabled = false;" : $objects;
	}

	function formatTree( $tree, &$objects )
	{
		$obj = "";
		$pid = $row = 1;
		$info = array(
			"TA" => array(
				$this->LNG['obj_tables'],
				1
			),
			"VI" => array(
				$this->LNG['obj_views'],
				3
			),
			"PR" => array(
				$this->LNG['obj_procs'],
				5
			),
			"FU" => array(
				$this->LNG['obj_funcs'],
				7
			),
			"TR" => array(
				$this->LNG['obj_trigs'],
				9
			),
			"EV" => array(
				$this->LNG['obj_events'],
				11
			)
		);
		$tab_prefix_last = $tab_prefix = "*";
		for ( $i = 0, $l = count( $objects['TA'] ); $i < $l ; $i++ )
		{
			$t = $objects['TA'][$i];
			$tab_prefix = preg_match( "/^([a-z0-9]+_)/", $t[0], $m ) ? $m[1] : "*";
			if ( $tab_prefix != $tab_prefix_last )
			{
				if ( $tab_prefix != "*" )
				{
					$objects['TA']['*'][] = $tab_prefix;
				}
				$tab_prefix_last = $tab_prefix;
			}
			$objects['TA'][$tab_prefix][] = $t;
			unset( $this->TA[$i] );
		}
		foreach ( $objects as $type => $o )
		{
			if ( !count( $o ) )
			{
				continue;
			}
			if ( $type == "TA" )
			{
				$open_childs = 1 < count( $o['*'] ) ? 0 : 1;
				$obj .= "[{$row},0,'".mysql_escape_string( $info[$type][0] )."',1,1,1],";
				++$row;
				foreach ( $o['*'] as $value )
				{
					if ( is_string( $value ) )
					{
						if ( 1 < count( $o[$value] ) )
						{
							$obj .= "[{$row},1,'{$value}*',1,1,{$open_childs}],";
							$pid = $row++;
							for ( $i = 0, $l = count( $o[$value] ); $i < $l ; $i++ )
							{
								$checked = $o[$value][$i][1] == "" && $o[$value][$i][2] == "" ? 2 : 1;
								$obj .= "[{$row},{$pid},'".mysql_escape_string( $o[$value][$i][0] )."',2,{$checked},{$o[$value][$i][2]}],";
								++$row;
							}
						}
						else
						{
							$value = $o[$value][0];
						}
					}
					if ( is_array( $value ) )
					{
						$checked = $value[1] == "" && $value[2] == "" ? 2 : 1;
						$obj .= "[{$row},1,'{$value[0]}',2,{$checked},{$value[2]}],";
						++$row;
					}
				}
			}
			else
			{
				$obj .= "[{$row},0,'".mysql_escape_string( $info[$type][0] )."',{$info[$type][1]},1,1],";
				$pid = $row++;
				++$info[$type][1];
				for ( $i = 0, $l = count( $o ); $i < $l ; $i++ )
				{
					$o[$i] = mysql_escape_string( $o[$i] );
					$obj .= "[{$row},{$pid},'{$o[$i]}',{$info[$type][1]},1,0],";
					++$row;
				}
			}
		}
		$add = "";
		if ( $tree == "restore" )
		{
			$add = "z('autoinc').disabled = z('prefix').disabled = z('restore_type').disabled = z('prefix_from').disabled = z('prefix_to').disabled = z('savesql').disabled = ".( $obj ? "false" : "true" ).";";
		}
		return ( $obj ? "sxd.tree.".$tree.".drawTree([".substr_replace( $obj, "]", - 1 ).");" : "sxd.tree.{$tree}.error(sxd.lng('err_sxd2'));" ).$add;
	}

	function getFileList( )
	{
		$files = array( );
		if ( is_dir( $this->CFG['backup_path'] ) && false !== ( $handle = opendir( $this->CFG['backup_path'] ) ) )
		{
			while ( false !== ( $file = readdir( $handle ) ) )
			{
				if ( preg_match( "/^.+?\.sql(\.(gz|bz2))?$/", $file ) )
				{
					$files[$file] = $file;
				}
			}
			closedir( $handle );
		}
		ksort( $files );
		return $files;
	}

	function getSavedJobs( )
	{
		$sj = array(
			"sj_backup" => array( ),
			"sj_restore" => array( )
		);
		if ( is_dir( $this->CFG['backup_path'] ) && false !== ( $handle = opendir( $this->CFG['backup_path'] ) ) )
		{
			while ( false !== ( $file = readdir( $handle ) ) )
			{
				if ( preg_match( "/^sj_(.+?)\.job.php$/", $file ) )
				{
					include( $this->CFG['backup_path'].$file );
					$sj["sj_".$JOB['type']][$JOB['job']] = "<b>{$JOB['job']}</b><br><i>{$JOB['title']}&nbsp;</i>";
				}
			}
			closedir( $handle );
		}
		if ( 0 < count( $sj['sj_backup'] ) )
		{
			ksort( $sj['sj_backup'] );
		}
		else
		{
			$sj['sj_backup'] = array(
				0 => "<b>No Saved Jobs</b><br>".$this->LNG['no_saved']
			);
		}
		if ( 0 < count( $sj['sj_restore'] ) )
		{
			ksort( $sj['sj_restore'] );
		}
		else
		{
			$sj['sj_restore'] = array(
				0 => "<b>No Saved Jobs</b><br>".$this->LNG['no_saved']
			);
		}
		return "sxd.clearOpt('sj_backup');sxd.clearOpt('sj_restore');sxd.addOpt(".sxd_php2json( $sj ).");";
	}

	function getFileListExtended( )
	{
		$files = array( );
		if ( is_dir( $this->CFG['backup_path'] ) && false !== ( $handle = opendir( $this->CFG['backup_path'] ) ) )
		{
			while ( false !== ( $file = readdir( $handle ) ) )
			{
				if ( preg_match( "/^.+?\.sql(\.(gz|bz2))?$/", $file, $m ) )
				{
					$fp = $this->openFile( $this->CFG['backup_path'].$file, "r" );
					$ext = !empty( $m[2] ) ? $m[2] : "sql";
					$temp = fgets( $fp );
					if ( preg_match( "/^(#SXD20\|.+?)\n/s", $temp, $m ) )
					{
						$h = explode( "|", $m[1] );
						$files[] = array(
							$h[5],
							substr( $h[4], 0, - 3 ),
							$ext,
							$h[7],
							number_format( $h[8], 0, "", " " ),
							filesize( $this->CFG['backup_path'].$file ),
							$h[9],
							$file
						);
					}
					else if ( preg_match( "/^(#SKD101\|.+?)\n/s", $temp, $m ) )
					{
						$h = explode( "|", $m[1] );
						$files[] = array(
							$h[1],
							substr( $h[3], 0, - 3 ),
							$ext,
							$h[2],
							number_format( $h[4], 0, "", " " ),
							filesize( $this->CFG['backup_path'].$file ),
							"SXD 1.0.x",
							$file
						);
					}
					else
					{
						$files[] = array(
							$file,
							"-",
							$ext,
							"-",
							"-",
							filesize( $this->CFG['backup_path'].$file ),
							"",
							$file
						);
					}
				}
			}
			closedir( $handle );
		}
		function s( $a, $b )
		{
			return strcmp( $b[1], $a[1] );
		}
		usort( $files, "s" );
		return "sxd.files.clear();sxd.files.add(".sxd_php2json( $files ).");";
	}

	function saveJob( $job, $config )
	{
		$this->saveToFile( $this->CFG['backup_path'].$job.".job.php", "<?php\n\$JOB = ".var_export( $config, true ).";\n"."?>" );
	}

	function openFile( $name, $mode )
	{
		if ( $mode == "r" )
		{
			if ( preg_match( "/\.(sql|sql\.bz2|sql\.gz)$/i", $name, $m ) )
			{
				$this->JOB['file_ext'] = strtolower( $m[1] );
			}
		}
		else
		{
			switch ( $this->JOB['zip'] )
			{
			case 0 :
				$this->JOB['file_ext'] = "sql";
				break;
			case 10 :
				$this->JOB['file_ext'] = "sql.bz2";
				break;
			default :
				$this->JOB['file_ext'] = "sql.gz";
				break;
			}
		}
		switch ( $this->JOB['file_ext'] )
		{
		case "sql" :
			return fopen( $name, "{$mode}b" );
		case "sql.bz2" :
			return bzopen( $name, $mode );
		case "sql.gz" :
			return gzopen( $name, $mode.( $mode == "w" ? $this->JOB['zip'] : "" ) );
		default :
			return false;
		}
	}

	function skipper( $curtype, $curobj = "null" )
	{
		static $curpos = -1;
		$founded = $curobj == "null" ? true : false;
		$skip = false;
		if ( 0 <= $curpos && $curobj == $this->JOB['todo'][$curpos][1] )
		{
			if ( $curtype == $this->JOB['todo'][$curpos][0] || ( $curtype == "TC" && $this->JOB['todo'][$curpos][0] == "TA" || $curtype == "TD" && $this->JOB['todo'][$curpos][0] == "TA" ) )
			{
				return false;
			}
			if ( $curtype == "TD" && $this->JOB['todo'][$curpos][0] == "TC" )
			{
				$founded = true;
				$skip = true;
			}
		}
		++$curpos;
		$l = count( $this->JOB['todo'] );
		while ( $curpos < $l )
		{
			$obj = $this->JOB['todo'][$curpos];
			if ( !$founded && $obj[1] == $curobj && ( $obj[0] == $curtype || $obj[0] == "TA" && ( $curtype == "TC" || $curtype == "TD" ) ) )
			{
				$founded = true;
			}
			if ( $skip && $obj[2] != "SKIP" )
			{
				return "\t".strtr( $obj[0], "A", "C" )."`{$obj[1]}";
			}
			if ( $founded )
			{
				if ( $obj[2] != "SKIP" )
				{
					return false;
				}
				$skip = true;
			}
			++$curpos;
		}
		return "EOJ";
	}
}

function save_query( $str )
{
	global $SXD;
	$SXD->nl_count = substr_count( $str, "\n" );
	return error_log( "{$str};\n", 3, $SXD->JOB['file_name'].".sxd.sql" );
}

function sxd_read_sql( $f, &$seek, $ei, $skipto = false )
{
	static $l = "";
	static $r = 0;
	if ( $skipto == "EOJ" )
	{
		return false;
	}
	$fs = ftell( $f );
	while ( $r || ( $s = fread( $f, 61440 ) ) )
	{
		if ( !$r )
		{
			$l .= $s;
		}
		if ( $skipto )
		{
			$pos = strpos( $l, $skipto );
			if ( $pos === false )
			{
				$l = substr( $l, 0 - strlen( $skipto ) );
				$seek = strlen( $l );
				$r = 0;
			}
			else
			{
				$l = substr( $l, $pos - 1 );
			}
		}
		$pos = strpos( $l, "\t;\n" );
		if ( $pos !== false )
		{
			$q = substr( $l, 0, $pos );
			$l = substr( $l, $pos + 3 );
			$r = 1;
			$seek = strlen( $l );
			return $q;
		}
		if ( $ei )
		{
			$pos = strrpos( $l, "\n" );
			if ( $pos !== false && $l[$pos - 1] === "," )
			{
				$q = substr( $l, 0, $pos - 1 );
				$l = substr( $l, $pos + 1 );
				$seek = strlen( $l );
				$r = 0;
				return $q;
			}
		}
		$r = 0;
	}
	if ( !empty( $l ) )
	{
		return $l;
	}
	return false;
}

function sxd_read_foreign_sql( $f, &$seek, $ei, $delimiter = ";", $eol = "\n" )
{
	static $l = "";
	static $r = 0;
	$fs = ftell( $f );
	$delim_len = strlen( $delimiter.$eol );
	while ( $r || ( $s = fread( $f, 61440 ) ) )
	{
		if ( !$r )
		{
			$l .= $s;
		}
		$pos = strpos( $l, $delimiter.$eol );
		if ( $pos !== false )
		{
			$q = substr( $l, 0, $pos );
			$l = substr( $l, $pos + $delim_len );
			$r = 1;
			$seek = strlen( $l );
			return $q;
		}
		if ( $ei )
		{
			$pos = strrpos( $l, $eol );
			if ( 0 < $pos && $l[$pos - 1] === "," )
			{
				$q = substr( $l, 0, $pos - 1 );
				$l = substr( $l, $pos + strlen( $eol ) );
				$seek = strlen( $l );
				$r = 0;
				return $q;
			}
		}
		$r = 0;
	}
	if ( !empty( $l ) )
	{
		return $l;
	}
	return false;
}

function sxd_check( $n, $obj, $filt )
{
	return isset( $obj[$n] ) || $filt && preg_match( $filt, $n );
}

function sxd_php2json( $obj )
{
	if ( count( $obj ) == 0 )
	{
		return "[]";
	}
	$is_obj = isset( $obj[0], $obj[count( $obj ) - 1] ) ? false : true;
	$str = $is_obj ? "{" : "[";
	foreach ( $obj as $key => $value )
	{
		$str .= $is_obj ? "'".addcslashes( $key, "\n\r\t'\\/" )."'".":" : "";
		if ( is_array( $value ) )
		{
			$str .= sxd_php2json( $value );
		}
		else if ( is_null( $value ) )
		{
			$str .= "null";
		}
		else if ( is_bool( $value ) )
		{
			$str .= $value ? "true" : "false";
		}
		else if ( is_numeric( $value ) )
		{
			$str .= $value;
		}
		else
		{
			$str .= "'".addcslashes( $value, "\n\r\t'\\/" )."'";
		}
		$str .= ",";
	}
	return substr_replace( $str, $is_obj ? "}" : "]", - 1 );
}

function sxd_ver2int( $ver )
{
	return preg_match( "/^(\d+)\.(\d+)\.(\d+)/", $ver, $m ) ? sprintf( "%d%02d%02d", $m[1], $m[2], $m[3] ) : 0;
}

function sxd_error_handler( $errno, $errmsg, $filename, $linenum, $vars )
{
	global $SXD;
	if ( $SXD->try )
	{
		return;
	}
	if ( strpos( $errmsg, "timezone settings" ) )
	{
		return;
	}
	$errortype = array( 1 => "Error", 2 => "Warning", 4 => "Parsing Error", 8 => "Notice", 16 => "Core Error", 32 => "Core Warning", 64 => "Compile Error", 128 => "Compile Warning", 256 => "MySQL Error", 512 => "Warning", 1024 => "Notice" );
	$str = mysql_escape_string( "{$errortype[$errno]}: {$errmsg} ({$filename}:{$linenum})" );
	if ( SXD_DEBUG )
	{
		error_log( "[index.php]\n{$str}\n", 3, "error.log" );
	}
	if ( $errno == 8 || $errno == 1024 )
	{
		if ( !$SXD->fh_log && !$SXD->fh_rtl )
		{
			echo isset( $_POST['ajax'] ) ? "alert('".$str."');" : $str;
		}
		else
		{
			fwrite( $SXD->fh_log, date( "Y.m.d H:i:s" )."\t3\t{$str}\n" );
		}
	}
	else if ( $errno < 1024 )
	{
		$SXD->error = true;
		if ( !$SXD->fh_log && !$SXD->fh_rtl )
		{
			echo isset( $_POST['ajax'] ) ? "alert('".$str."');" : $str;
		}
		else
		{
			$SXD->rtl[1] = time( );
			$SXD->rtl[9] = 5;
			fseek( $SXD->fh_rtl, 0 );
			fwrite( $SXD->fh_rtl, implode( "\t", $SXD->rtl ) );
			fwrite( $SXD->fh_log, date( "Y.m.d H:i:s" )."\t4\t{$str}\n" );
			unset( $SXD->rtl );
		}
		exit;
	}
}

function sxd_my_error( )
{
	trigger_error( mysql_error( ), E_USER_ERROR );
}

function sxd_shutdown( )
{
	global $SXD;
	if ( isset( $SXD->fh_rtl ) && is_resource( $SXD->fh_rtl ) && !empty( $SXD->rtl ) && empty( $SXD->error ) )
	{
		$SXD->rtl[1] = time( );
		if ( !empty( $SXD->JOB['file_stp'] ) && file_exists( dirname( __FILE__ )."/".$SXD->JOB['file_stp'] ) )
		{
			$type = file_get_contents( dirname( __FILE__ )."/".$SXD->JOB['file_stp'] );
			$SXD->rtl[9] = !empty( $type ) ? $type : 2;
		}
		else
		{
			$SXD->rtl[9] = 5;
		}
		fseek( $SXD->fh_rtl, 0 );
		fwrite( $SXD->fh_rtl, implode( "\t", $SXD->rtl ) );
	}
}

function sxd_antimagic( $arr )
{
	return is_array( $arr ) ? array_map( "sxd_antimagic", $arr ) : stripslashes( $arr );
}

function sxd_tpl_page( )
{
	global $SXD;
	return <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{$SXD->name}</title>
<link rel="stylesheet" type="text/css" href="{$SXD->url}load.php?sxd.css">
<script type="text/javascript" src="{$SXD->url}load.php?sxd.js"></script>
<script type="text/javascript" src="{$SXD->url}load.php?{$SXD->LNG['name']}.lng.js"></script>
<link rel="shortcut icon" href="{$SXD->url}load.php?favicon.ico">
</head>

<body>
<div id="main_div">
	<div id="header">{$SXD->name}</div>
	<div id="sxdToolbar"></div>
	<div id="name"><div id="loading"></div><b></b></div>
	<div id="content" class="content">
		<table cellspacing="0" id="tab_backup">
			<tr>
				<td width="242" valign="top">
					<div class="caption">{$SXD->LNG['combo_db']}</div><div id="backup_db"></div>
					<div class="caption">{$SXD->LNG['combo_charset']}</div><div id="backup_charset"></div>
					<div class="caption">{$SXD->LNG['combo_zip']}</div><div id="backup_zip"></div>
					<div class="caption">{$SXD->LNG['combo_comments']}</div>
					<div class="border"><textarea cols="10" rows="3" id="backup_comment"></textarea></div>
					<div class="caption" style="margin-top:12px;">
						<fieldset><legend>{$SXD->LNG['del_legend']}</legend>
						<div class="caption">&nbsp;– {$SXD->LNG['del_date']}</div>
						<div class="caption">&nbsp;– {$SXD->LNG['del_count']}</div>
						</fieldset>
					</div>
					<div class="caption" style="margin-top:8px;">
						<fieldset><legend>{$SXD->LNG['ext_legend']}</legend>
						<div class="caption"><label><input type="checkbox" id="outfile"> {$SXD->LNG['outfile']}</label></div>
						</fieldset>
					</div>
				</td>
				<td width="308" valign="top">
					<div class="caption">{$SXD->LNG['tree']}</div><div id=backup_tree class="zTree"></div>
				</td>
			</tr>
			<tr><td></td><td align="right"><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.showDialog('savejob');z('sj_name').value = sxd.combos.backup_db.value;"> <input type="button" value="{$SXD->LNG['btn_exec']}" onclick="sxd.runBackup();"></td></tr>
		</table>
		
		<table cellspacing="0" id="tab_restore" style="display:none;">
			<tr>
				<td width="242" valign="top">
					<div class="caption">{$SXD->LNG['combo_db']}</div><div id="restore_db"></div>
					<div class="caption">{$SXD->LNG['combo_charset']}</div><div id="restore_charset"></div>
					<div class="caption">{$SXD->LNG['combo_file']}</div><div id="restore_file"></div>
					<div class="caption">{$SXD->LNG['combo_comments']}</div>
					<div class="border"><textarea cols="10" rows="3" id="restore_comment" readonly></textarea></div>
					<div class="caption">{$SXD->LNG['combo_strategy']}</div><div id="restore_type"></div>
					<div class="caption" style="margin-top:1px;">
						<fieldset><legend>{$SXD->LNG['ext_legend']}</legend>
						<div class="caption"><label><input type="checkbox" id="correct"> {$SXD->LNG['correct']}</label></div>
						<div class="caption"><label><input type="checkbox" id="autoinc"> {$SXD->LNG['autoinc']}</label></div>
						<div class="caption"><input type="checkbox" id="prefix" onclick="sxd.getPrefix();"> {$SXD->LNG['prefix']}</div>
						</fieldset>
					</div>
				</td>
				<td width="308" valign="top">
					<div class="caption">{$SXD->LNG['tree']}</div><div id=restore_tree class="zTree"></div>
				</td>
			</tr>
			<tr><td><input type="button" value="{$SXD->LNG['savesql']}"  onclick="sxd.runRestore(1);" style="float:left;" id="savesql"></td><td align="right"><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.showDialog('savejob');z('sj_name').value = sxd.combos.restore_db.value;" id=restore_savejob> <input type="button" value="{$SXD->LNG['btn_exec']}" onclick="sxd.runRestore();" id=restore_runjob></td></tr>
		</table>
		
		<table cellspacing="0" id="tab_log" style="display:none;">
			<tr>
				<td valign="top" colspan=2>
					<div id=sxdGrid1></div> 
				</td>
			</tr>
			<tr><td colspan=2>
			<table class=progress>
				<tr><td>{$SXD->LNG['status_current']}</td><td><div id="sxdProc1"></div></td><td width=60>{$SXD->LNG['time_elapsed']}</td><td width=40 align=right id="sxdTime1">00:00</td></tr>
				<tr><td>{$SXD->LNG['status_total']}</td><td><div id="sxdProc2"></div></td><td>{$SXD->LNG['time_left']}</td><td align=right id="sxdTime2">00:00</td></tr>
			</table>
			</td></tr>
			<tr><td width="152"><input type="button" value="{$SXD->LNG['btn_clear']}" onclick="sxd.log.clear();"></td><td width="380" align="right">
			<input type="button" value="{$SXD->LNG['btn_download']}" id="btn_down" onclick="sxd.runFiles('download', this.file);" style="display:none;">
			<input type="button" value="{$SXD->LNG['btn_again']}" id="btn_again" onclick="sxd.runAgain();" disabled>
			<input type="button" value="{$SXD->LNG['btn_stop']}" id="btn_stop" onclick="sxd.stopJob();" disabled>
			<input type="button" value="{$SXD->LNG['btn_pause']}" id="btn_pause" onclick="sxd.pauseJob();" disabled>
			<input type="button" value="{$SXD->LNG['btn_resume']}" id="btn_resume" onclick="sxd.resumeJob();" style="display:none;">
			</td></tr>
		</table>
		<table cellspacing="0" id="tab_result" style="display:none;">
			<tr>
				<td valign="top">
					<div id=sxdGrid3></div> 
				</td>
			</tr>
			<tr><td>
			<input type="button" value="{$SXD->LNG['btn_clear']}" onclick="sxd.result.clear();">
			</td></tr>
		</table>
		
		<table cellspacing="0" id="tab_files" style="display:none;">
			<tr>
				<td valign="top" colspan=2>
					<div id=sxdGrid2></div> 
				</td>
			</tr>
			<tr><td width="242"><form id="save_file" method="GET" style="visibility:hidden;display:inline;" target=save></form><input type="button" value="{$SXD->LNG['btn_delete']}" onclick="sxd.runFiles('delete')"></td><td width="290" align="right">
			<input type="button" value="{$SXD->LNG['btn_download']}" onclick="sxd.runFiles('download')">
			<input type="button" value="{$SXD->LNG['btn_open']}" onclick="sxd.runFiles('open')">
			</td></tr>
		</table>
		
		
		<table cellspacing="0" id="tab_services" style="display:none;">
			<tr>
				<td width="242" valign="top">
					<div class="caption">{$SXD->LNG['combo_db']}</div><div id="services_db"></div>
					<br>
					<div class="caption">{$SXD->LNG['opt_check']}</div><div id="services_check"></div>
					<div class="caption">{$SXD->LNG['opt_repair']}</div><div id="services_repair"></div>
				</td>
				<td width="308" valign="top">
					<div class="caption">{$SXD->LNG['tree']}</div><div id=services_tree class="zTree"></div>
				</td>
			</tr>
			<tr><td align="right" colspan=2><input type="button" value="{$SXD->LNG['btn_extra']}"  onclick="sxd.showMenu({el:this}, sxd.options.services,{btn:2,width:160});" style="float:left;"> <input type="button" value="{$SXD->LNG['btn_check']}" onclick="sxd.runServices('check')"> <input type="button" value="{$SXD->LNG['btn_repair']}" onclick="sxd.runServices('repair')"> <input type="button" value="{$SXD->LNG['btn_analyze']}" onclick="sxd.runServices('analyze')">  <input type="button" value="{$SXD->LNG['btn_optimize']}" onclick="sxd.runServices('optimize')"></td></tr>
		</table>
		<table cellspacing="0" id="tab_options" style="display:none;">
			<tr>
				<td valign="top" colspan=2>
					<div style="height: 341px;">
					<fieldset>
					<legend>{$SXD->LNG['cfg_legend']}</legend>
						<table cellspacing="0">
							<tr>
								<td width=190>{$SXD->LNG['cfg_time_web']}</td>
								<td width=45><input type="text" id="time_web" style="width:40px;"></td>
								<td align="right">{$SXD->LNG['cfg_time_cron']}</td>
								<td width=40 align="right"><input type="text" id="time_cron" style="width:40px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_backup_path']}</td>
								<td colspan=3><input type="text" id="backup_path" style="width:351px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_backup_url']}</td>
								<td colspan=3><input type="text" id="backup_url" style="width:351px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_globstat']}</td>
								<td><input type="checkbox" id="globstat" value="1"></td>
								<td align="right">Размер буффера OUTFILE (МБ)</td>
								<td width=40 align="right"><input type="text" id="outfile_size" style="width:40px;"></td>
							</tr>
							<tr>
								<td>Путь для OUTFILE</td>
								<td colspan=3><input type="text" id="outfile_path" value="" style="width:351px;"></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
					<legend>{$SXD->LNG['cfg_confirm']}</legend>
						<table cellspacing="0">
							<tr>
								<td width="33%"><label><input type="checkbox" id="conf_import" value="1"> {$SXD->LNG['cfg_conf_import']}</label></td>
								<td width="34%"><label><input type="checkbox" id="conf_file" value="1"> {$SXD->LNG['cfg_conf_file']}</label></td>
								<td width="33%"><label><input type="checkbox" id="conf_db" value="1"> {$SXD->LNG['cfg_conf_db']}</label></td>
							</tr><tr>
								<td><label><input type="checkbox" id="conf_truncate" value="1"> {$SXD->LNG['cfg_conf_truncate']}</label></td>
								<td><label><input type="checkbox" id="conf_drop" value="1"> {$SXD->LNG['cfg_conf_drop']}</label></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
					<legend>{$SXD->LNG['cfg_extended']}</legend>
						<table cellspacing="0">
							<tr>
								<td width=190>{$SXD->LNG['cfg_charsets']}</td>
								<td><input type="text" id="charsets" value="" style="width:351px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_only_create']}</td>
								<td><input type="text" id="only_create" value="" style="width:351px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_auth']}</td>
								<td><input type="text" id="auth" value="" style="width:351px;"></td>
							</tr>
						</table>
					</fieldset>
					</div>
				</td>
			</tr>
			<tr><td align="right" colspan=2><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.saveOptions();"></td></tr>
		</table>
	</div>
</div>

<div id="overlay"></div>
<div class="dialog" id ="dia_connect">
	<div class="header">{$SXD->LNG['con_header']}</div>
	<div class="content">
		<table cellspacing="5">
			<tr>
				<td valign="top">
				<fieldset>
				<legend>{$SXD->LNG['connect']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="80">{$SXD->LNG['my_host']}</td>
							<td width="126"><input type="text" id="con_host" style="width:120px;"></td>
							<td width="40" align="right">{$SXD->LNG['my_port']}</td>
							<td width="36"><input type="text" id="con_port" maxlength="5" style="width:30px;"></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['my_user']}</td>
							<td colspan="3"><input type="text" id="con_user" name="user" style="width:202px;"></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['my_pass']}</td>
							<td colspan="3"><input type="password" id="con_pass" name="pass" title="{$SXD->LNG['my_pass_hidden']}" style="width:202px;" onchange="this.changed = true;"></td>
						</tr>
						<tr>
							<td></td>
							<td colspan="3"><label><input type="checkbox" id="con_comp" value="1"> {$SXD->LNG['my_comp']}</label></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['my_db']}</td>
							<td colspan="3"><input type="text" id="con_db" style="width:202px;"></td>
						</tr>
					</table></fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="right"><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.saveConnect();"><input type="button" value="{$SXD->LNG['btn_cancel']}" onclick="sxd.hideDialog('connect');"></td></tr>
		</table>
	</div>
</div>
<div class="dialog" id ="dia_savejob">
	<div class="header">{$SXD->LNG['sj_header']}</div>
	<div class="content">
		<table cellspacing="5">
			<tr>
				<td valign="top">
				<fieldset>
				<legend>{$SXD->LNG['sj_job']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="80">{$SXD->LNG['sj_name']}</td>
							<td><input type="text" id="sj_name" style="width:202px;" maxlength="12" value=""></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['sj_title']}</td>
							<td><input type="text" id="sj_title" maxlength="64" style="width:202px;"></td>
						</tr>
					</table></fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="right"><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.saveJob();"><input type="button" value="{$SXD->LNG['btn_cancel']}" onclick="sxd.hideDialog('savejob');"></td></tr>
		</table>
	</div>
</div>
<div class=dialog id="dia_createdb">
	<div class="header">{$SXD->LNG['cdb_header']}</div>
	<div class="content">
		<table cellspacing="5">
			<tr>
				<td valign="top">
				<fieldset>
				<legend>{$SXD->LNG['cdb_detail']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="80">{$SXD->LNG['cdb_name']}</td>
							<td width="202"><input type="text" id="db_name" value="my_db_1" style="width:202px;"></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['combo_charset']}</td>
							<td><div id="db_charset"></div></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['combo_collate']}</td>
							<td><div id="db_charset_col"></div></td>
						</tr>
					</table>
				</fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="right"><input type="button" value="{$SXD->LNG['btn_create']}" onclick="sxd.addDb();"><input type="button" value="{$SXD->LNG['btn_cancel']}" onclick="sxd.hideDialog('createdb');"></td></tr>
		</table>
	</div>
</div>

<div class=dialog id="dia_charsets">
	<div class="header" id="cc_header"></div>
	<div class="content">
		<table cellspacing="5">
			<tr>
				<td valign="top">
				<fieldset>
				<legend>{$SXD->LNG['cdb_detail']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="80">{$SXD->LNG['combo_charset']}</td>
							<td width="202"><div id="services_charset"></div></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['combo_collate']}</td>
							<td><div id="services_charset_col"></div></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
				<legend>{$SXD->LNG['hint']}</legend>
					<table cellspacing="3">
						<tr>
							<td colspan=2 id=charset_hint></td>
						</tr>
					</table>
				</fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="right"><input type="button" value="{$SXD->LNG['btn_exec']}" onclick="sxd.changeCS();"><input type="button" value="{$SXD->LNG['btn_cancel']}" onclick="sxd.hideDialog('charsets');"></td></tr>
		</table>
	</div>
</div>

<div id=sxdMenu style="display:none;z-index:9999;"></div>
<script type="text/javascript">
sxd.init();
sxd.backupUrl = '{$SXD->CFG['backup_url']}';
sxd.tbar.init('sxdToolbar', {$SXD->VAR['toolbar']}); 
{$SXD->VAR['combos']}
sxd.actions.tab_backup();
</script>
</body>
</html>
HTML;
}

function sxd_tpl_auth( $error = "" )
{
	global $SXD;
	return <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">  
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{$SXD->name}</title>
<link rel="shortcut icon" href="{$SXD->url}load.php?favicon.ico">
<link rel="stylesheet" type="text/css" href="{$SXD->url}load.php?sxd.css">
</head>
<body>
<div class="dialog" id="dia_auth">
	<div class="header">{$SXD->name}</div>
	<div class="content" id="div_1" style="line-height:50px;text-align:center;">{$SXD->LNG['js_required']}</div>
	<div class="content" id="div_2" style="display:none;">
		<form method="post">
		<table cellspacing="5">
			<tr>
				<td valign="top" colspan="3">
				<fieldset>
				<legend>{$SXD->LNG['auth']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="90">{$SXD->LNG['auth_user']}</td>
							<td width="192"><input type="text" name="user" value="{$_POST['user']}" class="i202"></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['my_pass']}</td>
							<td><input type="password" name="pass" value="{$_POST['pass']}" class="i202"></td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" name="save" value="1"{$_POST['save']}> {$SXD->LNG['auth_remember']}</label></td>
						</tr>
						<tr>
							<td>Language:</td>
							<td><select type="text" name="lang" style="width:198px;" onChange="this.form.submit();">{$SXD->lng_list}</select></td>
						</tr>
					</table>
					<table cellspacing="3" id="hst" style="display:none;">
						<tr>
							<td width="90">{$SXD->LNG['my_host']}</td>
							<td width="116"><input type="text" name="host" style="width:110px;" value="{$_POST['host']}"></td>
							<td width="40" align="right">{$SXD->LNG['my_port']}</td>
							<td width="36"><input type="text" name="port" maxlength="5" style="width:30px;" value="{$_POST['port']}"></td>
						</tr>
					</table>
				</fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="left"><input type="button" value="{$SXD->LNG['btn_details']}" onclick="var s = document.getElementById('hst').style; s.display = (s.display == 'block') ? 'none' : 'block';"></td><td align="right"><input type="submit" value="{$SXD->LNG['btn_enter']}"></td></tr>
		</table>
		</form>
	</div>
	<script type="text/javascript">document.getElementById('div_1').style.display = 'none';document.getElementById('div_2').style.display = 'block';</script>
</div>
</body>
</html>
HTML;
}

?>