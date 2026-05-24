<?php

/* $Revision: 1.10 $ */

define ('LIKE','LIKE');
$_SESSION['DatabaseName']="myorgap6_bareilly";
$db = mysqli_connect($host , $dbuser, $dbpassword,$_SESSION['DatabaseName']);

if ( !$db ) {
	echo '<BR>' . _('The configuration in the file config.php for the database user name and password do not provide the information required to connect to the database server');
	exit;
}
/*
if (! mysqli_select_db($_SESSION['DatabaseName'],$db)) {
	echo '<BR>' . _('The company name entered does not correspond to a database on the database server specified in the config.php configuration file. Try logging in with a different company name');
	echo '<BR><A HREF="index.php">' . _('Back to login page') . '</A>';
	unset ($_SESSION['DatabaseName']);
	exit; 
}
*/
require_once ($PathPrefix .'includes/MiscFunctions.php');

//DB wrapper functions to change only once for whole application

function DB_query ($SQL,
		&$Conn,
		$ErrorMessage='',
		$DebugMessage= '',
		$Transaction=false,
		$TrapErrors=true){

	global $debug;
	global $PathPrefix;
	
	$result=mysqli_query($Conn,$SQL);
	
	if ($DebugMessage == '') {
		$DebugMessage = _('The SQL that failed was');
	}
	
	if (DB_error_no($Conn) != 0 AND $TrapErrors==true){
		if ($TrapErrors){
			require_once($PathPrefix . 'includes/header.inc');
		}
		prnMsg($ErrorMessage.'<BR>' . DB_error_msg($Conn),'error', _('Database Error'));
		if ($debug==1){
			prnMsg($DebugMessage. "<BR>$SQL<BR>",'error',_('Database SQL Failure'));
		}
		if ($Transaction){
			$SQL = 'rollback';
			$Result = DB_query($SQL,$Conn);
			if (DB_error_no($Conn) !=0){
				prnMsg(_('Error Rolling Back Transaction'), '', _('Database Rollback Error') );
			}
		}
		if ($TrapErrors){
			include($PathPrefix . 'includes/footer.inc');
			exit;
		}
	}
	return $result;

}

function DB_fetch_row (&$ResultIndex) {

	$RowPointer=mysqli_fetch_row($ResultIndex);
	Return $RowPointer;
}

function DB_fetch_assoc (&$ResultIndex) {

	$RowPointer=mysqli_fetch_assoc($ResultIndex);
	Return $RowPointer;
}

function DB_fetch_array (&$ResultIndex) {

	$RowPointer=mysqli_fetch_array($ResultIndex);
	Return $RowPointer;
}

function DB_data_seek (&$ResultIndex,$Record) {
	mysqli_data_seek($ResultIndex,$Record);
}

function DB_free_result (&$ResultIndex){
	mysqli_free_result($ResultIndex);
}

function DB_num_rows (&$ResultIndex){
	return mysqli_num_rows($ResultIndex);
}

function DB_error_no (&$Conn){
	return mysqli_errno($Conn);
}

function DB_error_msg(&$Conn){
	return mysqli_error($Conn);
}

function DB_Last_Insert_ID(&$Conn,$table, $fieldname){
	return mysqli_insert_id($Conn);
}


function DB_escape_string($String){
	//Return mysql_escape_string($String);
	//Return mysqli_real_escape_string($Conn,$String);
	Return $String;
}

function mysql_num_fields($string){
  return mysqli_num_fields($string);
//	Return $String;
}

function mysql_field_name($string,$col){
  return mysqli_fetch_field_direct($string, $col)->name;
//	Return $String;
}


function INTERVAL( $val, $Inter ){
		global $dbtype;
		return "\n".'INTERVAL ' . $val . ' '. $Inter."\n";
}

function DB_Maintenance($Conn){

	prnMsg(_('The system has just run the regular database administration and optimisation routine.'),'info');
	
	$TablesResult = DB_query('SHOW TABLES',$Conn);
	while ($myrow = DB_fetch_row($TablesResult)){
		$Result = DB_query('OPTIMIZE TABLE ' . $myrow[0],$Conn);
	}
	
	$Result = DB_query('UPDATE config 
				SET confvalue="' . Date('Y-m-d') . '" 
				WHERE confname="DB_Maintenance_LastRun"',
				$Conn);
}

?>
