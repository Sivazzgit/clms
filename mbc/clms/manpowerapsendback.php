<?php

/* $Revision: 1.0 $ */

$PageSecurity = 5;

include('includes/sess.inc');
//include('PeriodSetting.php');
$title = _('Manpower Approval Sending Back');
include('includes/headerclms.inc');

$md="select distinct category from  clmsmanpower".$_GET['mmonth']." where trim(dt)='".trim($_GET['stdate'])."' and trim(shift)='".trim($_GET['shift'])."' 
and trim(section)='".trim($_GET['section'])."'";
//echo $md; 
 
		$result = DB_query($md,$db);
//$myrow0 = DB_fetch_row($result);
//include('includes/header.inc');
//include('includes/SQL_CommonFunctions.inc');
//include('includes/prlFunctions.php');
			include('catlmailer.php');

	while ($myrow0 = DB_fetch_row($result))  
    {
		//echo '<br>'.$myrow0[0];
			
 
$quarter=14;
$shodname='xxxxxx';
$shodname=$myrow0[0];
$mselfname='';
	//	$shodemail='c.ramankutty@ramconsultancy.in';
		$shodemail='ramu1956@gmail.com'; 
echo"";		
			$toaddr=array($shodemail);
			$name1=ucwords(strtolower($shodname));
			if($quarter==14){
			$sub=trim(ucwords(strtolower($mselfname))).' : Manpower Approval Sending Back';
			$body1='Manpowe Approval request for   '.$_GET["section"].' on '.$_GET["stdate"].' in '.$_GET["shift"].'shift is sending back. Plaese veryfy and make changes as discussed';
			}else{
			$sub=trim(ucwords(strtolower($mselfname))).' : KRA Evalution Q'.$quarter .'';
			$body1='KRA  evaluation for quarter  '.$quarter.' is pending for <b> Mr.'.trim(ucwords(strtolower($mselfname)));
				
			}
			$toCC='';
			$toBCC='';
			catlmailer($toaddr,$name1,$toCC,$toBCC,$sub,$body1,"");
			
echo'<div>';
echo'<center><table border="1">';
echo'<tr>';
echo'<td>';
echo$Mmsg1.'Mail send to '.$shodname.' for   '.$_GET["section"].' on '.$_GET["stdate"].' in '.$_GET["shift"].'shift' ;
echo'</td>';
echo'</tr>';
echo'</center></table>';
echo'</div>';
	}	
		echo '<h3><br><br><center><a href="manpowerentryapprovalstatus.php">' . _('Back to Approval Page') . '</a><BR></h3>';
$mmdt=date("d/m/Y :h:i:sa"); 
//echo"MMDT".$mmdt;
$mappby=$_SESSION['UserID'].' at '.$mmdt;
$md="update clmsmanpowerapstatus".$_GET['mmonth']." set sendback='".$mappby."' where trim(dt)='".trim($_GET['stdate'])."' and trim(shift)='".trim($_GET['shift'])."' 
and trim(section)='".trim($_GET['section'])."'"; 
//echo $md; 
		$result = DB_query($md,$db); 
 
?>