<?php
$PageSecurity = 10;
include('includes/sess.inc');
//$title = _('Staff Pay Register Any Month');
include('includes/headerclms.inc');
//include('PeriodSetting.php');

if(isset($_POST["submit1"]))
	{
		$msepdate = $_POST['sepdt'];
 		$msepreason = $_POST['sepreason'];
 		$mno = $_POST['mno'];
     //   echo "I am Going to update";
	 //   echo "rrrrrr".$msepdate; 
	 //   echo "ttttttt".$msepreason; 
	 //   echo "rrrrrr".$mno; 
		
		$sql = "update clmsemployeemaster set sepdate='".$msepdate."' where employeeid='".$mno."'";
    	$result = DB_query($sql,$db,$ErrMsg);
//echo $sql;
		$sql = "update clmsemployeemaster set sepreason='".$msepreason."' where employeeid='".$mno."'";
    	$result = DB_query($sql,$db,$ErrMsg);

		$sql = "update clmsemployeemaster set active=1 where employeeid='".$mno."'";
    	$result = DB_query($sql,$db,$ErrMsg);
		
	    echo "Separation Updated for ".$mno; 

	   
	}
	
	


?>