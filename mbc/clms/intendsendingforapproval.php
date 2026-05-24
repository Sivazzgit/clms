<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Shift Wise Labour Intend ');
include('includes/headerclms.inc');
/* 
if($_SESSION['UserID']=='rmsuser'){
	include('mfgheader2.php');
}else{
	include('mfgheader.php');

}
*/
 $mrecid=$_GET['recid']; 
//	echo "PPPP". $mrecid; 
$sql09 = "SELECT approver from clmsapprovalmatrix where creater='".$_SESSION['UserID']."'";
	$ErrMsg = _('The employee master could not be retrieved because'); 
//echo $sql09;	 

	$result09 = DB_query($sql09,$db,$ErrMsg);
	$myrow09 = DB_fetch_array($result09);
//	echo "Approver". $myrow09[0]; 
		$mmdt=date("d/m/Y :h:i:sa"); 
		$mapprovedchangedby=$myrow09[0].' at '.$mmdt;

	
	$sql09 = "update clmsintend set status='Sent for Approval to ".$mapprovedchangedby."' where recordid=".$mrecid;
//echo $sql09	;

//	$ErrMsg = _('The employee master could not be retrieved because'); 
	$result09 = DB_query($sql09,$db,$ErrMsg);

			echo "<h3>Details sent for Approval to ".$myrow09[0]."</h3>";
	echo '<h3><br><br><center><a href="intend.php">' . _('Back to Intend Page') . '</a><BR></h3>';

?>