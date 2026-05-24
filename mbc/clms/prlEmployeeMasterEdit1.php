<?php

/* $Revision: 1.0 $ */
//exit("Break-1");

$PageSecurity = 10;
include('includes/sess.inc');
$title = _('Employee Master');

include('includes/headerclms.inc'); 
	if (isset($_POST['submit'])) {
    $o3=trim($_POST['dob']);
	$o3=date("Y-m-d",strtotime($o3)); 
	$sql = "update clmsemployeemaster set birthdate='".$o3."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
    $o3=trim($_POST['doj']);
	$o3=date("Y-m-d",strtotime($o3)); 
	$sql = "update clmsemployeemaster set hiredate='".$o3."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "update clmsemployeemaster set dept='".$_POST['contractor']."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "update clmsemployeemaster set address1='".$_POST['add']."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "update clmsemployeemaster set phone1='".$_POST['phone1']."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "update clmsemployeemaster set esino='".$_POST['esino']."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "update clmsemployeemaster set pfno='".$_POST['pfno']."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "update clmsemployeemaster set aadhar='".$_POST['aadhar']."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "update clmsemployeemaster set pan='".$_POST['pan']."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$sql = "update clmsemployeemaster set section='".$_POST['section']."' where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
		
      echo "DOB is".$o3;
      echo "Employee ID is".$_POST['empid'];
 		echo "<h3>Details Updated</h3>";
		echo '<h3><br><br><center><a href="prlSelectEmployee.php">' . _('Back to Previous Page') . '</a><BR></h3>';


	}	 
?>