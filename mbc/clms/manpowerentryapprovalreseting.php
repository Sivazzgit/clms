<?php
$PageSecurity = 10;  
include('includes/sess.inc');
include('includes/headerclms.inc');
/*
$sql = "select dt as'Date',Workcenter as'User',Shift,Employeeid,Employeename,HRS,Section as 'Working Section',category as'Vendor'
 from clmsmanpower".$_GET['mmonth']." where trim(dt)='".trim($_GET['stdate'])."' and trim(shift)='".trim($_GET['shift'])."' 
and trim(section)='".trim($_GET['section'])."'";
*/
$sql = "update  clmsmanpowerapstatus".$_GET['mmonth']." set approvedby='' where recordid=".$_GET['recdid'];

echo 'Reset Done for Record Id='.$_GET['recdid'];		
    $DbgMsg = _('The SQL that was used to insert the employee but failed was');
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
//    $column_count = mysql_num_fields($result03);
	echo '<center><a href="manpowerentryapprovalstatus.php">' . _('Back to Manpower Approval Form') . '</a><BR>';

 
?>