<?php

$PageSecurity = 10;
include('includes/sess.inc');
$title = _('Employee Master');

include('includes/headerclms.inc'); 
	if (isset($_POST['submit'])) {
	$sql = "select count(*)  from  clmsemployeemaster  where employeeid='".$_POST['empid']."'";
	$result = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_row($result);
	if(trim($_POST['LastName'])==''){
		  exit("New Employee Name must not be blank");	
	}
	if(trim($_POST['empid'])>=3000){
		  exit("Employee ID Must be between 2000 and 3000");	
	}
	if(trim($_POST['empid'])<2000){
		  exit("Employee ID Must be between 2000 and 3000");	
	}
	
		
    if($myrow[0]==0){
		
	$sql = "insert into clmsemployeemaster(employeeid,lastname,dept,orgunit) values('".$_POST['empid']."','".$_POST['LastName']."','".$_POST['contractor']."','Kancor')";
//	echo $sql;
		$result = DB_query($sql,$db,$ErrMsg);

        Echo "New Employee";
		echo "<br>Employee ID is".$_POST['empid'];
 		echo "<br><h3>Added New Record</h3>";
		echo "<br><h3>Please Edit and update other details</h3>";
		echo '<h3><br><br><center><a href="prlSelectEmployee.php">' . _('Back to Previous Page') . '</a><BR></h3>';
        	  
	}Else{

      Echo "Employee Exists";
		echo "<br>Employee ID is".$_POST['empid'];
		echo '<h3><br><br><center><a href="prlSelectEmployee.php">' . _('Back to Previous Page') . '</a><BR></h3>';

	}	
		


	}	 
?>