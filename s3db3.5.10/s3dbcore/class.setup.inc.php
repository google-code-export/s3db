<?php
	/**
	* Setup
	* @author Joseph Engo<jengo@phpgroupware.org>
	* @author Dan Kuykendall<seek3r@phpgroupware.org>
	* @author Mark Peters<skeeter@phpgroupware.org>
	* @author Miles Lott<milosch@phpgroupware.org>
	* @copyright Portions Copyright (C) 2001-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id: class.setup.inc.php,v 1.15.2.15 2004/02/10 13:51:18 ceb Exp $
	*/

	/**
	* Setup
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class setup
	{
		var $db;
		var $oProc;

		var $detection = '';
		var $process = '';
		var $lang = '';
		var $html = '';
		var $appreg = '';
		
		/* table name vars */
		var $tbl_apps;
		var $tbl_config;
		var $tbl_hooks;

		function setup($html=False, $translation=False)
		{
			$this->detection = CreateObject('phpgwapi.setup_detection');
			$this->process   = CreateObject('phpgwapi.setup_process');
			$this->appreg    = CreateObject('phpgwapi.app_registry');

			/* The setup application needs these */
			$this->html	= $html ? CreateObject('phpgwapi.setup_html') : '';
			$this->translation = $translation ? CreateObject('phpgwapi.setup_translation') : '';
			
			//$this->tbl_apps    = $this->get_apps_table_name();
			//$this->tbl_config  = $this->get_config_table_name();
                	$this->tbl_hooks   = $this->get_hooks_table_name();
		}

		/*!
		@function loaddb
		@abstract include api db class for the ConfigDomain and connect to the db
		*/
		function loaddb()
		{
			$GLOBALS['ConfigDomain'] = get_var('ConfigDomain',array('COOKIE','POST'),$_POST['FormDomain']);

			$GLOBALS['phpgw_info']['server']['db_type'] = $GLOBALS['phpgw_domain'][$GLOBALS['ConfigDomain']]['db_type'];

			$this->db	  = CreateObject('phpgwapi.db');
			$this->db->Host     = $GLOBALS['phpgw_domain'][$GLOBALS['ConfigDomain']]['db_host'];
			$this->db->Type     = $GLOBALS['phpgw_domain'][$GLOBALS['ConfigDomain']]['db_type'];
			$this->db->Database = $GLOBALS['phpgw_domain'][$GLOBALS['ConfigDomain']]['db_name'];
			$this->db->User     = $GLOBALS['phpgw_domain'][$GLOBALS['ConfigDomain']]['db_user'];
			$this->db->Password = $GLOBALS['phpgw_domain'][$GLOBALS['ConfigDomain']]['db_pass'];
		}

		/*!
		@function auth
		@abstract authenticate the setup user
		@param	$auth_type	???
		*/
		function auth($auth_type='Config')
		{
			$remoteip     = $_SERVER['REMOTE_ADDR'];

			$FormLogout   = get_var('FormLogout',  array('GET','POST'));
			$ConfigLogin  = get_var('ConfigLogin', array('POST'));
			$HeaderLogin  = get_var('HeaderLogin', array('POST'));
			$FormDomain   = get_var('FormDomain',  array('POST'));
			$FormPW       = get_var('FormPW',      array('POST'));

			$ConfigDomain = get_var('ConfigDomain',array('POST','COOKIE'));
			$ConfigPW     = get_var('ConfigPW',    array('POST','COOKIE'));
			$HeaderPW     = get_var('HeaderPW',    array('POST','COOKIE'));
			$ConfigLang   = get_var('ConfigLang',  array('POST','COOKIE'));

			/*
			if(!empty($remoteip) && !$this->checkip($remoteip))
			{
				return False;
			}
			*/

			/* 6 cases:
				1. Logging into header admin
				2. Logging into config admin
				3. Logging out of config admin
				4. Logging out of header admin
				5. Return visit to config OR header
				6. None of the above
			*/

			$expire = time() + 1200; /* Expire login if idle for 20 minutes. */

			if(!empty($HeaderLogin) && $auth_type == 'Header')
			{
				/* header admin login */
				if($FormPW == stripslashes($GLOBALS['phpgw_info']['server']['header_admin_password']))
				{
					setcookie('HeaderPW',"$FormPW","$expire");
					setcookie('ConfigLang',"$ConfigLang","$expire");
					return True;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = lang('Invalid password');
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = '';
					return False;
				}
			}
			elseif(!empty($ConfigLogin) && $auth_type == 'Config')
			{
				/* config login */
				if($FormPW == stripslashes(@$GLOBALS['phpgw_domain'][$FormDomain]['config_passwd']))
				{
					setcookie('ConfigPW',"$FormPW","$expire");
					setcookie('ConfigDomain',"$FormDomain","$expire");
					setcookie('ConfigLang',"$ConfigLang","$expire");
					return True;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = lang('Invalid password');
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';
					return False;
				}
			}
			elseif(!empty($FormLogout))
			{
				/* logout */
				if($FormLogout == 'config')
				{
					/* config logout */
					setcookie('ConfigPW','');
					$GLOBALS['phpgw_info']['setup']['LastDomain'] = $_COOKIE['ConfigDomain'];
					setcookie('ConfigDomain','');
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = lang('You have successfully logged out');
					setcookie('ConfigLang','');
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';

					return False;
				}
				elseif($FormLogout == 'header')
				{
					/* header admin logout */
					setcookie('HeaderPW','');
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = lang('You have successfully logged out');
					setcookie('ConfigLang','');
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = '';

					return False;
				}
			}
			elseif(!empty($ConfigPW) && $auth_type == 'Config')
			{
				/* Returning after login to config */
				if($ConfigPW == stripslashes($GLOBALS['phpgw_domain'][$ConfigDomain]['config_passwd']))
				{
					setcookie('ConfigPW',"$ConfigPW","$expire");
					setcookie('ConfigDomain',"$ConfigDomain","$expire");
					setcookie('ConfigLang',"$ConfigLang","$expire");
					return True;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = lang('Invalid password');
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';
					return False;
				}
			}
			elseif(!empty($HeaderPW) && $auth_type == 'Header')
			{
				/* Returning after login to header admin */
				if($HeaderPW == stripslashes($GLOBALS['phpgw_info']['server']['header_admin_password']))
				{
					setcookie('HeaderPW',"$HeaderPW","$expire");
					setcookie('ConfigLang',"$ConfigLang","$expire");
					return True;
				}
				else
				{
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = lang('Invalid password');
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = '';
					return False;
				}
			}
			else
			{
				$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';
				$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = '';
				return False;
			}
		}

		function checkip($remoteip='')
		{
			$allowed_ips = split(',',$GLOBALS['phpgw_info']['server']['setup_acl']);
			if(is_array($allowed_ips))
			{
				$foundip = False;
				while(list(,$value) = @each($allowed_ips))
				{
					$test = split("\.",$value);
					if(count($test) < 3)
					{
						$value .= ".0.0";
						$tmp = split("\.",$remoteip);
						$tmp[2] = 0;
						$tmp[3] = 0;
						$testremoteip = join('.',$tmp);
					}
					elseif(count($test) < 4)
					{
						$value .= ".0";
						$tmp = split("\.",$remoteip);
						$tmp[3] = 0;
						$testremoteip = join('.',$tmp);
					}
					elseif(count($test) == 4 &&
						intval($test[3]) == 0)
					{
						$tmp = split("\.",$remoteip);
						$tmp[3] = 0;
						$testremoteip = join('.',$tmp);
					}
					else
					{
						$testremoteip = $remoteip;
					}

					//echo '<br>testing: ' . $testremoteip . ' compared to ' . $value;

					if($testremoteip == $value)
					{
						//echo ' - PASSED!';
						$foundip = True;
					}
				}
				if(!$foundip)
				{
					$GLOBALS['phpgw_info']['setup']['HeaderLoginMSG'] = '';
					$GLOBALS['phpgw_info']['setup']['ConfigLoginMSG'] = lang('Invalid IP address');
					return False;
				}
			}
			return True;
		}

		/*!
		@function get_major
		@abstract Return X.X.X major version from X.X.X.X versionstring
		@param	$
		*/
		function get_major($versionstring)
		{
			if(!$versionstring)
			{
				return False;
			}
			
			$version = str_replace('pre','.',$versionstring);
			$varray  = explode('.',$version);
			$major   = implode('.',array($varray[0],$varray[1],$varray[2]));

			return $major;
		}

		/*!
		@function clear_session_cache
		@abstract Clear system/user level cache so as to have it rebuilt with the next access
		@param	None
		*/
		function clear_session_cache()
		{
			$tables = Array();
			$tablenames = $this->db->table_names();
			foreach($tablenames as $key => $val)
			{
				$tables[] = $val['table_name'];
			}
			if(in_array('phpgw_app_sessions',$tables))
			{
				$this->db->lock(array('phpgw_app_sessions'));
				@$this->db->query("DELETE FROM phpgw_app_sessions WHERE sessionid = '0' and loginid = '0' and app = 'phpgwapi' and location = 'config'",__LINE__,__FILE__);
				@$this->db->query("DELETE FROM phpgw_app_sessions WHERE app = 'phpgwapi' and location = 'phpgw_info_cache'",__LINE__,__FILE__);
				$this->db->unlock();
			}
		}

		/*!
		@function register_app
		@abstract Add an application to the phpgw_applications table
		@param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		@param	$enable		optional, set to True/False to override setup.inc.php setting
		*/
		function register_app($appname,$enable=99)
		{
			$setup_info = $GLOBALS['setup_info'];

			if(!$appname)
			{
				return False;
			}

			if($enable==99)
			{
				$enable = $setup_info[$appname]['enable'];
			}
			$enable = intval($enable);

			/*
			Use old applications table if the currentver is less than 0.9.10pre8,
			but not if the currentver = '', which probably means new install.
			*/
			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.10pre8') && ($setup_info['phpgwapi']['currentver'] != ''))
			{
				$appstbl = 'applications';
			}
			else
			{
				$appstbl = 'phpgw_applications';
				if($this->amorethanb($setup_info['phpgwapi']['currentver'],'0.9.13.014'))
				{
					$use_appid = True;
				}
			}

			if($GLOBALS['DEBUG'])
			{
				echo '<br>register_app(): ' . $appname . ', version: ' . $setup_info[$appname]['version'] . ', table: ' . $appstbl . '<br>';
				// _debug_array($setup_info[$appname]);
			}

			if($setup_info[$appname]['version'])
			{
				if($setup_info[$appname]['tables'])
				{
					$tables = implode(',',$setup_info[$appname]['tables']);
				}
				if ($setup_info[$appname]['tables_use_prefix'] == True)
				{
					echo $setup_info[$appname]['name'] . ' uses tables_use_prefix, storing ' 
					. $setup_info[$appname]['tables_prefix']
						. ' as prefix for ' . $setup_info[$appname]['name'] . " tables\n";
																			
					$sql = "INSERT INTO phpgw_config (config_app,config_name,config_value) "
						."VALUES ('".$setup_info[$appname]['name']."','"
						.$appname."_tables_prefix','".$setup_info[$appname]['tables_prefix']."')";
					$this->db->query($sql,__LINE__,__FILE__);
				}
				if($use_appid)
				{
					$this->db->query("SELECT MAX(app_id) FROM $appstbl",__LINE__,__FILE__);
					$this->db->next_record();
					if($this->db->f(0))
					{
						$app_id = ($this->db->f(0) + 1) . ',';
						$app_idstr = 'app_id,';
					}
					else
					{
						srand(100000);
						$app_id = rand(1,100000) . ',';
						$app_idstr = 'app_id,';
					}
				}
				$this->db->query("INSERT INTO $appstbl "
					. "($app_idstr app_name,app_enabled,app_order,app_tables,app_version) "
					. "VALUES ("
					. $app_id
					. "'" . $setup_info[$appname]['name'] . "',"
					. $enable . ","
					. intval($setup_info[$appname]['app_order']) . ","
					. "'" . $tables . "',"
					. "'" . $setup_info[$appname]['version'] . "')"
					,__LINE__,__FILE__
				);
				$this->clear_session_cache();
			}
		}

		/*!
		@function app_registered
		@abstract Check if an application has info in the db
		@param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		@param	$enabled	optional, set to False to not enable this app
		*/
		function app_registered($appname)
		{
			$setup_info = $GLOBALS['setup_info'];

			if(!$appname)
			{
				return False;
			}

			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.10pre8') && ($setup_info['phpgwapi']['currentver'] != ''))
			{
				$appstbl = 'applications';
			}
			else
			{
				$appstbl = 'phpgw_applications';
			}

			if(@$GLOBALS['DEBUG'])
			{
				echo '<br>app_registered(): checking ' . $appname . ', table: ' . $appstbl;
				// _debug_array($setup_info[$appname]);
			}

			$this->db->query("SELECT COUNT(app_name) FROM $appstbl WHERE app_name='".$appname."'",__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f(0))
			{
				if(@$GLOBALS['DEBUG'])
				{
					echo '... app previously registered.';
				}
				return True;
			}
			if(@$GLOBALS['DEBUG'])
			{
				echo '... app not registered';
			}
			return False;
		}

		/*!
		@function update_app
		@abstract Update application info in the db
		@param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		@param	$enabled	optional, set to False to not enable this app
		*/
		function update_app($appname)
		{
			$setup_info = $GLOBALS['setup_info'];

			if(!$appname)
			{
				return False;
			}

			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.10pre8') && ($setup_info['phpgwapi']['currentver'] != ''))
			{
				$appstbl = 'applications';
			}
			else
			{
				$appstbl = 'phpgw_applications';
			}

			if($GLOBALS['DEBUG'])
			{
				echo '<br>update_app(): ' . $appname . ', version: ' . $setup_info[$appname]['currentver'] . ', table: ' . $appstbl . '<br>';
				// _debug_array($setup_info[$appname]);
			}

			$this->db->query("SELECT COUNT(app_name) FROM $appstbl WHERE app_name='".$appname."'",__LINE__,__FILE__);
			$this->db->next_record();
			if(!$this->db->f(0))
			{
				return False;
			}

			if($setup_info[$appname]['version'])
			{
				//echo '<br>' . $setup_info[$appname]['version'];
				if($setup_info[$appname]['tables'])
				{
					$tables = implode(',',$setup_info[$appname]['tables']);
				}

				$sql = "UPDATE $appstbl "
					. "SET app_name='" . $setup_info[$appname]['name'] . "',"
					. " app_enabled=" . intval($setup_info[$appname]['enable']) . ","
					. " app_order=" . intval($setup_info[$appname]['app_order']) . ","
					. " app_tables='" . $tables . "',"
					. " app_version='" . $setup_info[$appname]['version'] . "'"
					. " WHERE app_name='" . $appname . "'";
				//echo $sql; exit;

				$this->db->query($sql,__LINE__,__FILE__);
			}
		}

		/*!
		@function update_app_version
		@abstract Update application version in applications table, post upgrade
		@param	$setup_info		Array of application information (multiple apps or single)
		@param	$appname		Application 'name' with a matching $setup_info[$appname] array slice
		@param	$tableschanged	???
		*/
		function update_app_version($setup_info, $appname, $tableschanged = True)
		{
			if(!$appname)
			{
				return False;
			}

			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.10pre8') && ($setup_info['phpgwapi']['currentver'] != ''))
			{
				$appstbl = 'applications';
			}
			else
			{
				$appstbl = 'phpgw_applications';
			}

			if($tableschanged == True)
			{
				$GLOBALS['phpgw_info']['setup']['tableschanged'] = True;
			}
			if($setup_info[$appname]['currentver'])
			{
				$this->db->query("UPDATE $appstbl SET app_version='" . $setup_info[$appname]['currentver'] . "' WHERE app_name='".$appname."'",__LINE__,__FILE__);
			}
			return $setup_info;
		}

		/*!
		@function deregister_app
		@abstract de-Register an application
		@param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		*/
		function deregister_app($appname)
		{
			if(!$appname)
			{
				return False;
			}
			$setup_info = $GLOBALS['setup_info'];

			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.10pre8') && ($setup_info['phpgwapi']['currentver'] != ''))
			{
				$appstbl = 'applications';
			}
			else
			{
				$appstbl = 'phpgw_applications';
			}

			//echo 'DELETING application: ' . $appname;
			$this->db->query("DELETE FROM $appstbl WHERE app_name='". $appname ."'",__LINE__,__FILE__);
			$this->clear_session_cache();
		}

		/*!
		@function register_hooks
		@abstract Register an application's hooks
		@param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		*/
		function register_hooks($appname)
		{
			$setup_info = $GLOBALS['setup_info'];

			if(!$appname)
			{
				return False;
			}

			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.8pre5') && ($setup_info['phpgwapi']['currentver'] != ''))
			{
				/* No phpgw_hooks table yet. */
				return False;
			}

			if (!is_object($this->hooks))
			{
				$this->hooks = CreateObject('phpgwapi.hooks',$this->db);
			}
			$this->hooks->register_hooks($appname,$setup_info[$appname]['hooks']);
		}

		/*!
		@function update_hooks
		@abstract Update an application's hooks
		@param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		*/
		function update_hooks($appname)
		{
			$this->register_hooks($appname);
		}

		/*!
		@function deregister_hooks
		@abstract de-Register an application's hooks
		@param	$appname	Application 'name' with a matching $setup_info[$appname] array slice
		*/
		function deregister_hooks($appname)
		{
			if($this->alessthanb($setup_info['phpgwapi']['currentver'],'0.9.8pre5'))
			{
				/* No phpgw_hooks table yet. */
				return False;
			}

			if(!$appname)
			{
				return False;
			}
			
			//echo "DELETING hooks for: " . $setup_info[$appname]['name'];
			if (!is_object($this->hooks))
			{
				$this->hooks = CreateObject('phpgwapi.hooks',$this->db);
			}
			$this->hooks->register_hooks($appname);
		}

		/*!
		 @function hook
		 @abstract call the hooks for a single application
		 @param $location hook location - required
		 @param $appname application name - optional
		*/
		function hook($location, $appname='')
		{
			if (!is_object($this->hooks))
			{
				$this->hooks = CreateObject('phpgwapi.hooks',$this->db);
			}
			return $this->hooks->single($location,$appname,True,True);
		}

		/*
		@function alessthanb
		@abstract phpgw version checking, is param 1 < param 2 in phpgw versionspeak?
		@param	$a	phpgw version number to check if less than $b
		@param	$b	phpgw version number to check $a against
		#return	True if $a < $b
		*/
		function alessthanb($a,$b,$DEBUG=False)
		{
			$num = array('1st','2nd','3rd','4th');

			if($DEBUG)
			{
				echo'<br>Input values: '
					. 'A="'.$a.'", B="'.$b.'"';
			}
			$newa = ereg_replace('pre','.',$a);
			$newb = ereg_replace('pre','.',$b);
			$testa = explode('.',$newa);
			if(@$testa[1] == '')
			{
				$testa[1] = 0;
			}
			if(@$testa[3] == '')
			{
				$testa[3] = 0;
			}
			$testb = explode('.',$newb);
			if(@$testb[1] == '')
			{
				$testb[1] = 0;
			}
			if(@$testb[3] == '')
			{
				$testb[3] = 0;
			}
			$less = 0;

			for($i=0;$i<count($testa);$i++)
			{
				if($DEBUG) { echo'<br>Checking if '. intval($testa[$i]) . ' is less than ' . intval($testb[$i]) . ' ...'; }
				if(intval($testa[$i]) < intval($testb[$i]))
				{
					if ($DEBUG) { echo ' yes.'; }
					$less++;
					if($i<3)
					{
						/* Ensure that this is definitely smaller */
						if($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely less than B."; }
						$less = 5;
						break;
					}
				}
				elseif(intval($testa[$i]) > intval($testb[$i]))
				{
					if($DEBUG) { echo ' no.'; }
					$less--;
					if($i<2)
					{
						/* Ensure that this is definitely greater */
						if($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely greater than B."; }
						$less = -5;
						break;
					}
				}
				else
				{
					if($DEBUG) { echo ' no, they are equal.'; }
					$less = 0;
				}
			}
			if($DEBUG) { echo '<br>Check value is: "'.$less.'"'; }
			if($less>0)
			{
				if($DEBUG) { echo '<br>A is less than B'; }
				return True;
			}
			elseif($less<0)
			{
				if($DEBUG) { echo '<br>A is greater than B'; }
				return False;
			}
			else
			{
				if($DEBUG) { echo '<br>A is equal to B'; }
				return False;
			}
		}

		/*!
		@function amorethanb
		@abstract phpgw version checking, is param 1 > param 2 in phpgw versionspeak?
		@param	$a	phpgw version number to check if more than $b
		@param	$b	phpgw version number to check $a against
		#return	True if $a < $b
		*/
		function amorethanb($a,$b,$DEBUG=False)
		{
			$num = array('1st','2nd','3rd','4th');

			if($DEBUG)
			{
				echo'<br>Input values: '
					. 'A="'.$a.'", B="'.$b.'"';
			}
			$newa = ereg_replace('pre','.',$a);
			$newb = ereg_replace('pre','.',$b);
			$testa = explode('.',$newa);
			if($testa[3] == '')
			{
				$testa[3] = 0;
			}
			$testb = explode('.',$newb);
			if($testb[3] == '')
			{
				$testb[3] = 0;
			}
			$less = 0;

			for($i=0;$i<count($testa);$i++)
			{
				if($DEBUG) { echo'<br>Checking if '. intval($testa[$i]) . ' is more than ' . intval($testb[$i]) . ' ...'; }
				if(intval($testa[$i]) > intval($testb[$i]))
				{
					if($DEBUG) { echo ' yes.'; }
					$less++;
					if($i<3)
					{
						/* Ensure that this is definitely greater */
						if($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely greater than B."; }
						$less = 5;
						break;
					}
				}
				elseif(intval($testa[$i]) < intval($testb[$i]))
				{
					if($DEBUG) { echo ' no.'; }
					$less--;
					if($i<2)
					{
						/* Ensure that this is definitely smaller */
						if($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely less than B."; }
						$less = -5;
						break;
					}
				}
				else
				{
					if($DEBUG) { echo ' no, they are equal.'; }
					$less = 0;
				}
			}
			if($DEBUG) { echo '<br>Check value is: "'.$less.'"'; }
			if($less>0)
			{
				if($DEBUG) { echo '<br>A is greater than B'; }
				return True;
			}
			elseif($less<0)
			{
				if($DEBUG) { echo '<br>A is less than B'; }
				return False;
			}
			else
			{
				if($DEBUG) { echo '<br>A is equal to B'; }
				return False;
			}
		}

		function get_hooks_table_name()
		{
			if($this->alessthanb($GLOBALS['setup_info']['phpgwapi']['currentver'],'0.9.8pre5') && ($GLOBALS['setup_info']['phpgwapi']['currentver'] != ''))
			{
				/* No phpgw_hooks table yet. */
				return False;
			}
			return 'phpgw_hooks';
		}
}
?>
