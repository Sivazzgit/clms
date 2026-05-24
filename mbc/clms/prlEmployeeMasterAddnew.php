<?php

/* $Revision: 1.0 $ */
//exit("Break-1");

$PageSecurity = 10;
include('includes/sess.inc');
$title = _('Employee Master');

include('includes/headerclms.inc'); 

if (isset($_GET['EmployeeID'])){
	$EmployeeID = strtoupper($_GET['EmployeeID']);
} elseif (isset($_POST['EmployeeID'])){
	$EmployeeID = strtoupper($_POST['EmployeeID']);
} else {
	unset($EmployeeID);
}

if($_SESSION['UserID']=='clms01'){
  $kkdept='UNIVERSAL ASSOCIATES';
}
if($_SESSION['UserID']=='clms02'){ 
  $kkdept='G4S SECURE SOLUTIONS INDIA PVT LTD';
}
if($_SESSION['UserID']=='clms03'){
  $kkdept='UNNATHI HR SOLUTION';
}
if($_SESSION['UserID']=='clmsadmin'){
  $kkdept='ALL';
}
	$sql="select employeeid,dept,lastname from clmsemployeemaster where trim(dept)='".$kkdept."' order by employeeid desc limit 1";
	
if($kkdept=='ALL'){
  	$sql="select employeeid,dept,lastname from clmsemployeemaster  order by employeeid desc limit 1";
  
}
	
//echo $sql;
	$result = DB_query($sql,$db); 
	$myrow = DB_fetch_row($result);  
    $mempno=$myrow[0];
    $mnewempno=$myrow[0]+1;
$mnewempno1='';	
$myrow['lastname']='';
    echo "Last Employee ID is =".$mempno." of ".$myrow[2];
    echo "<Br>Next ID is =".$mnewempno;

	//echo "RRRRRRRR";
	echo "<FORM METHOD='post' action='prlEmployeeMasterAddnew1.php'>";  
//	echo "<INPUT TYPE=HIDDEN NAME='empid' VALUE='$EmployeeID'>";
	echo '<CENTER><TABLE>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Employee ID') . ":</TD><TD><input type='Text'  name='empid' value='".$mnewempno1."' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Name') . ":</TD><TD><input type='Text' name='LastName' value='" . $myrow['lastname'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Contractor') . ":</TD><TD><input type='Text'readonly name='contractor' value ='".$kkdept."' SIZE=42 MAXLENGTH=40></TD></TR>";	
   echo '</table>';
	echo"<P><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Add New Employee') . "'>";
	echo'</FORM>';

?>