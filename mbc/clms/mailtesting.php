<?php

/* $Revision: 1.0 $ */

$PageSecurity = 5;

include('includes/sess.inc');
//include('PeriodSetting.php');
$title = _('KRA Sending For Assessment');

//include('includes/header.inc');
//include('includes/SQL_CommonFunctions.inc');
//include('includes/prlFunctions.php');
$quarter=14;
$shodname='xxxxxx';
$mselfname='yyyyyyy';
	//	$shodemail='c.ramankutty@ramconsultancy.in';
		$shodemail='ramu1956@gmail.com'; 
echo"RRRR";		
			include('catlmailer.php');
			$toaddr=array($shodemail);
			$name1=ucwords(strtolower($shodname));
			if($quarter==14){
			$sub=trim(ucwords(strtolower($mselfname))).' : KRA Evalution Q'.$quarter .' and Annual Aprisal';
			$body1='KRA  evaluation for quarter  '.$quarter.' and Annual Aprisal is pending for <b> Mr.'.trim(ucwords(strtolower($mselfname)));
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
echo$Mmsg1.'RRRRRRRRR';
echo'</td>';
echo'</tr>';
echo'</center></table>';
echo'</div>';

?>