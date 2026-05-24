<?php
/* $Revision: 1.0 $ */
$PageSecurity = 15;
include('includes/sess.inc');

$title = _('Shift Wise Labour Intend Approval');

include('includes/headerclms.inc');

 





 $mrecid=$_GET['recid']; 

		$mmdt=date("d/m/Y :h:i:sa"); 

		$mapprovedby=$_SESSION['UserID'].' at '.$mmdt;



	

	$sql09 = "update clmsintend set approvedby='Approved by ".$mapprovedby."' where recordid=".$mrecid;

//echo $sql09	;



	$result09 = DB_query($sql09,$db,$ErrMsg); 



			echo "<h3>Details  Approved </h3>";

	echo '<h3><br><br><center><a href="intend.php">' . _('Back to Intend Page') . '</a><BR></h3>';



?>