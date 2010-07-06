<?php
 	/**
	* Database class for Oracle
	* @author Luis Francisco Gonzalez Hernandez
	* @copyright Copyright (C) 1998-2000 Luis Francisco Gonzalez Hernandez
	* @copyright Portions Copyright (C) 2001-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @link http://www.sanisoft.com/phplib/manual/DB_sql.php
	* @package phpgwapi
	* @subpackage database
	* @version $Id: class.db_oracle.inc.php,v 1.6.2.1.2.3 2004/02/10 13:51:18 ceb Exp $
	*/

	/**
	* Database class for Oracle
	* 
	* @package phpgwapi
	* @subpackage database
	*/
	class db
	{
		var $Remote = 1;

		var $ora_no_next_fetch = False;

		/* copied from db_mysql for completeness */
		/* public: identification constant. never change this. */
		var $type     = 'oracle';
		var $revision = '1.2';

		/* public: constructor */
		function db($query = '')
		{
			$this->db_($query);
		}

		function connect($Database = '', $Host = '', $User = '', $Password = '')
		{
			/* Handle defaults */
			if ($Database == '')
			{
				$Database = $this->Database;
			}
			if ($Host == '')
			{
				$Host     = $this->Host;
			}
			if ($User == '')
			{
				$User     = $this->User;
			}
			if ($Password == '')
			{
				$Password = $this->Password;
			}

			if (! $this->Link_ID)
			{
				if($this->Debug)
				{
					printf('<br>Connecting to ' . $Database . "...<br>\n");
				}
				if($this->Remote)
				{
					if($this->Debug)
					{
						printf("<br>connect() $User/******@$Database.world<br>\n");
					}
					if($GLOBALS['phpgw_info']['server']['db_persistent'])
					{
						$this->Link_ID = ora_plogon("$User/$Password@$Database","");
						/************** (comment by SSilk)
						this dosn't work on my system:
						$this->Link_ID = ora_plogon("$User@$Database.world","$Password");
						***************/
					}
					else
					{
						$this->Link_ID = ora_logon("$User/$Password@$Database","");
						/************** (comment by SSilk)
						this dosn't work on my system:
						$this->Link_ID=ora_logon("$User@$Database.world","$Password");
						***************/
					}
				}
				else
				{
					if($this->Debug)
					{
						printf("<br>connect() $User, $Password <br>\n");
					}
					if($GLOBALS['phpgw_info']['server']['db_persistent'])
					{
						$this->Link_ID=ora_plogon("$User","$Password");
						/* (comment by SSilk: don't know how this could work, but I leave this untouched!) */
					}
					else
					{
						$this->Link_ID=ora_logon("$User","$Password");
					}
				}
				if($this->Debug)
				{
					printf("<br>connect() Link_ID: $this->Link_ID<br>\n");
				}
				if (!$this->Link_ID)
				{
					$this->halt('connect() Link-ID == false '
						. "($this->Link_ID), ora_".($GLOBALS['phpgw_info']['server']['db_persistent']?'p':'').'logon failed');
				}
				else
				{
					//echo 'commit on<p>';
					ora_commiton($this->Link_ID);
				}
				if($this->Debug)
				{
					printf("<br>connect() Obtained the Link_ID: $this->Link_ID<br>\n");
				}
			}
		}

		## In order to increase the # of cursors per system/user go edit the
		## init.ora file and increase the max_open_cursors parameter. Yours is on
		## the default value, 100 per user.
		## We tried to change the behaviour of query() in a way, that it tries
		## to safe cursors, but on the other side be carefull with this, that you
		## don't use an old result.
		## 
		## You can also make extensive use of ->disconnect()!
		## The unused QueryIDs will be recycled sometimes. 

		function query($Query_String)
		{
			/* No empty queries, please, since PHP4 chokes on them. */
			if ($Query_String == '')
			{
				/* The empty query string is passed on from the constructor,
				* when calling the class without a query, e.g. in situations
				* like these: '$db = new DB_Sql_Subclass;'
				*/
				return 0;
			}
			$this->connect();
			$this->lastQuery=$Query_String;

			if (!$this->Query_ID)
			{
				$this->Query_ID = ora_open($this->Link_ID);
			}
			if($this->Debug)
			{
				printf("Debug: query = %s<br>\n", $Query_String);
				printf("<br>Debug: Query_ID: %d<br>\n", $this->Query_ID);
			}

			if(!@ora_parse($this->Query_ID,$Query_String))
			{
				$this->Errno=ora_errorcode($this->Query_ID);
				$this->Error=ora_error($this->Query_ID);
				$this->halt("<BR>ora_parse() failed:<BR>$Query_String<BR><small>Snap & paste this to sqlplus!</SMALL>");
			}
			elseif (!@ora_exec($this->Query_ID))
			{
				$this->Errno=ora_errorcode($this->Query_ID);
				$this->Error=ora_error($this->Query_ID);
				$this->halt("<BR>\n$Query_String\n<BR><small>Snap & paste this to sqlplus!</SMALL>");
			}

			$this->Row=0;

			if(!$this->Query_ID)
			{
				$this->halt('Invalid SQL: '.$Query_String);
			}

			return $this->Query_ID;
		}

		function next_record()
		{
			if (!$this->no_next_fetch && 
				0 == ora_fetch($this->Query_ID))
			{
				if ($this->Debug)
				{
					printf("<br>next_record(): ID: %d,Rows: %d<br>\n",
					$this->Query_ID,$this->num_rows());
				}
				$this->Row +=1;

				$errno=ora_errorcode($this->Query_ID);
				if(1403 == $errno)
				{ # 1043 means no more records found
					$this->Errno = 0;
					$this->Error = '';
					$this->disconnect();
					$stat=0;
				}
				else
				{
					$this->Error=ora_error($this->Query_ID);
					$this->Errno=$errno;
					if($this->Debug)
					{
						printf('<br>%d Error: %s',
						$this->Errno,
						$this->Error);
					}
					$stat=0;
				}
			}
			else
			{
				$this->no_next_fetch=false;
				for($ix=0;$ix<ora_numcols($this->Query_ID);$ix++)
				{
					$col=strtolower(ora_columnname($this->Query_ID,$ix));
					$value=ora_getcolumn($this->Query_ID,$ix);
					$this->Record[ "$col" ] = $value;
					# echo"<b>[$col]</b>: $value <br>\n";
				}
				$stat=1;
			}
			return $stat;
		}

		## seek() works only for $pos - 1 and $pos
		## Perhaps I make a own implementation, but my
		## opinion is, that this should be done by PHP3
		function seek($pos)
		{
			if($this->Row - 1 == $pos)
			{
				$this->no_next_fetch=true;
			}
			elseif ($this->Row == $pos )
			{
				## do nothing
			}
			else
			{
				$this->halt("Invalid seek(): Position is cannot be handled by API.<BR>".
				"Difference too big. Wanted: $pos Current pos: $this->Row");
			}
			if($Debug)
			{
				echo "<BR>Debug: seek = $pos<BR>";
			}
			$this->Row=$pos;
		}

		function lock($table, $mode = 'write')
		{
			if ($mode == 'write')
			{
				$result = ora_do($this->Link_ID, "lock table $table in row exclusive mode");
			}
			else
			{
				$result = 1;
			}
			return $result;
		}

		function unlock()
		{
			return ora_do($this->Link_ID, 'commit');
		}

		function metadata($table,$full=false)
		{
			$count = 0;
			$id    = 0;
			$res   = array();

			/*
			* Due to compatibility problems with Table we changed the behavior
			* of metadata();
			* depending on $full, metadata returns the following values:
			*
			* - full is false (default):
			* $result[]:
			*	 [0]['table']	table name
			*	 [0]['name']	 field name
			*	 [0]['type']	 field type
			*	 [0]['len']		field length
			*	 [0]['flags']	field flags ('NOT NULL', 'INDEX')
			*	 [0]['format'] precision and scale of number (eg. '10,2') or empty
			*	 [0]['index']	name of index (if has one)
			*	 [0]['chars']	number of chars (if any char-type)
			*
			* - full is true
			* $result[]:
			*	 ['num_fields'] number of metadata records
			*	 [0]['table']	table name
			*	 [0]['name']	 field name
			*	 [0]['type']	 field type
			*	 [0]['len']		field length
			*	 [0]['flags']	field flags ('NOT NULL', 'INDEX')
			*	 [0]['format'] precision and scale of number (eg. '10,2') or empty
			*	 [0]['index']	name of index (if has one)
			*	 [0]['chars']	number of chars (if any char-type)
			*	 ['meta'][field name]	index of field named 'field name'
			*	 The last one is used, if you have a field name, but no index.
			*	 Test:	if (isset($result['meta']['myfield'])) {} ...
			*/

			$this->connect();

			## This is a RIGHT OUTER JOIN: '(+)', if you want to see, what
			## this query results try the following:
			## $table = new Table; $db = new my_DB_Sql; # you have to make
			## # your own class
			## $table->show_results($db->query(see query vvvvvv))
			##
			$this->query("SELECT T.table_name,T.column_name,T.data_type,".
				"T.data_length,T.data_precision,T.data_scale,T.nullable,".
				"T.char_col_decl_length,I.index_name".
				" FROM ALL_TAB_COLUMNS T,ALL_IND_COLUMNS I".
				" WHERE T.column_name=I.column_name (+)".
				" AND T.table_name=I.table_name (+)".
				" AND T.table_name=UPPER('$table') ORDER BY T.column_id");

			$i=0;
			while ($this->next_record())
			{
				$res[$i]['table'] = $this->Record[table_name];
				$res[$i]['name']  = strtolower($this->Record[column_name]);
				$res[$i]['type']  = $this->Record[data_type];
				$res[$i]['len']   = $this->Record[data_length];
				if ($this->Record[index_name])
				{
					$res[$i]['flags'] = 'INDEX ';
				}
				$res[$i]['flags'] .= ( $this->Record['nullable'] == 'N') ? '' : 'NOT NULL';
				$res[$i]['format']= (int)$this->Record['data_precision'].','.
				(int)$this->Record[data_scale];
				if ('0,0'==$res[$i]['format'])
				{
					$res[$i]['format']='';
				}
				$res[$i]['index'] = $this->Record[index_name];
				$res[$i]['chars'] = $this->Record[char_col_decl_length];
				if ($full)
				{
					$j=$res[$i]['name'];
					$res['meta'][$j] = $i;
					$res['meta'][strtoupper($j)] = $i;
				}
				if ($full)
				{
					$res['meta'][$res[$i]['name']] = $i;
				}
				$i++;
			}
			if ($full)
			{
				$res['num_fields']=$i;
			}
			# $this->disconnect();
			return $res;
		}

		## THIS FUNCTION IS UNSTESTED!
		function affected_rows()
		{
			if ($Debug)
			{
				echo '<BR>Debug: affected_rows='. ora_numrows($this->Query_ID).'<BR>';
			}
			return ora_numrows($this->Query_ID);
		}

		## Known bugs: It will not work for SELECT DISTINCT and any
		## other constructs which are depending on the resulting rows.
		## So you *really need* to check every query you make, if it
		## will work with it.
		##
		## Also, for a qualified replacement you need to parse the
		## selection, cause this will fail: 'SELECT id, from FROM ...').
		## 'FROM' is - as far as I know a keyword in Oracle, so it can
		## only be used in this way. But you have been warned.
		function num_rows()
		{
			$curs=ora_open($this->Link_ID);

			## this is the important part and it is also the HACK!
			if (eregi("^[[:space:]]*SELECT[[:space:]]",$this->lastQuery) )
			{
				$from_pos = strpos(strtoupper($this->lastQuery),'FROM');
				$q = 'SELECT count(*) '. substr($this->lastQuery, $from_pos);

				ORA_parse($curs,$q);
				ORA_exec($curs);
				ORA_fetch($curs);
				if ($Debug)
				{
					echo '<BR>Debug: num_rows='. ORA_getcolumn($curs,0).'<BR>';
				}
				return(ORA_getcolumn($curs,0));
			}
			else
			{
				$this->halt("Last Query was not a SELECT: $this->lastQuery");
			}
		}

		function num_fields()
		{
			if ($Debug)
			{
				echo '<BR>Debug: num_fields='. ora_numcols($this->Query_ID) . '<BR>';
			}
			return ora_numcols($this->Query_ID);
		}

		/* public: sequence number */
		function nextid($seq_name)
		{
			$this->connect();

			/* Independent Query_ID */
			$Query_ID = ora_open($this->Link_ID);

			if(!@ora_parse($Query_ID,"SELECT $seq_name.NEXTVAL FROM DUAL")) 
			{
				// There is no such sequence yet, then create it
				if(!@ora_parse($Query_ID,"CREATE SEQUENCE $seq_name") ||
					!@ora_exec($Query_ID))
				{
					$this->halt('<BR> nextid() function - unable to create sequence');
					return 0;
				}
				@ora_parse($Query_ID,"SELECT $seq_name.NEXTVAL FROM DUAL");
			} 
			if (!@ora_exec($Query_ID))
			{
				$this->halt("<BR>ora_exec() failed:<BR>nextID function");
			}
			if (@ora_fetch($Query_ID) )
			{
				$next_id = ora_getcolumn($Query_ID, 0);
			}
			else
			{
				$next_id = 0;
			}
			if ( Query_ID > 0 )
			{
				ora_close(Query_ID);
			}
			return $next_id;
		}

		function disconnect()
		{
			if($this->Debug)
			{
				echo "Debug: Disconnecting $this->Query_ID...<br>\n";
			}
			if ( $this->Query_ID < 1 )
			{
				echo "<B>Warning</B>: disconnect(): Cannot free ID $this->Query_ID\n";
				# return();
			}
			ora_close($this->Query_ID);
			$this->Query_ID=0;
		}

		/* private: error handling */
		function halt($msg)
		{
			if ($this->Halt_On_Error == 'no')
			{
				return;
			}

			$this->haltmsg($msg);

			if ($this->Halt_On_Error != 'report')
			{
				die('Session halted.');
			}
		}

		function haltmsg($msg)
		{
			printf("<b>Database error:</b> %s<br>\n", $msg);
			printf("<b>Oracle Error</b>: %s (%s)<br>\n",
			$this->Errno,
			$this->Error);
		}

		function table_names()
		{
			$this->connect();
			$this->query('SELECT table_name,tablespace_name FROM user_tables');
			$i=0;
			while ($this->next_record())
			{
				$info[$i]['table_name']      = strtolower($this->Record['table_name']);
				$info[$i]['tablespace_name'] = $this->Record['tablespace_name'];
				$i++;
			} 
			return $info;
		}
	}
