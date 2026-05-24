
  <script type="text/javascript">  
	function passval(app){
		
		var mapp = document.getElementById('app').options[document.getElementById('app').selectedIndex].text;
   alert("You entered: " +mapp);
	
	 }

	 function onapproval(url,id){
		
		var mapp = document.getElementById(id).value;
   		document.location=encodeURI(url+"&approve="+mapp);
	
	 }
	</script>	


<?php
$PageSecurity = 10;  


include('includes/sess.inc');
include('includes/headerclms.inc');

$title = _('Contract Labour Engagement Approval Status');
if(substr($_SESSION['UserID'],0,4)<>'clms' ){
	exit("Not allowed. Unauthorised User");
}
/*
echo "Hai";
echo"<br> Date" .$_GET['stdate'];
echo"<br> Shift" .$_GET['shift'];
echo"<br> Section" .$_GET['section'];
echo"<br> approved" .$_GET['approve'];
echo"Month". $mmonth1;
*/
$mmonth1=$_GET['mmonth'];
//echo"MMDT".$mmdt;

$mmdt=date("d/m/Y :h:i:sa"); 
$mappby=$_SESSION['UserID'].' at '.$mmdt;
$md="update clmsmanpowerapstatus".$mmonth1." set approved=".$_GET['approve']." where trim(dt)='".trim($_GET['stdate'])."' and trim(shift)='".trim($_GET['shift'])."' 
and trim(section) like '".trim($_GET['section'])."%' and trim(vendor) like '".trim($_GET['vendor'])."%'";
		$result = DB_query($md,$db);
$md="update clmsmanpowerapstatus".$mmonth1." set approvedby='".$mappby."' where trim(dt)='".trim($_GET['stdate'])."' and trim(shift)='".trim($_GET['shift'])."' 
and trim(section) like '".trim($_GET['section'])."%'  and trim(vendor) like '".trim($_GET['vendor'])."%'";
//echo $md;
		$result = DB_query($md,$db);
//Echo "Done";
 
		echo '<h3><br><br><center><a href="manpowerentryapprovalstatus.php">' . _('Back to Approval Page') . '</a><BR></h3>';
 


?>