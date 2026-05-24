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

echo "Employee ID ".$EmployeeID;

$sql = "SELECT employeeid,
				lastname,
			dept, 
            section,birthdate,			
			DATE_FORMAT(birthdate,'%d-%m-%Y')as'birthdate',
			DATE_FORMAT(hiredate,'%d-%m-%Y')as'hiredate',
			DATE_FORMAT(confdate,'%d-%m-%Y')as'confdate',
 			address1,
                        phone1,
                        phone2,
						active,esino,pfno,aadhar,pan



		FROM clmsemployeemaster where employeeid='".$EmployeeID."'";
//echo $sql;	
	$ErrMsg = _('The employee master could not be retrieved because'); 
	$result = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($result);
	echo "<FORM METHOD='post' action='prlEmployeeMasterEdit1.php'>";  
	echo "<INPUT TYPE=HIDDEN NAME='empid' VALUE='$EmployeeID'>";
	echo '<CENTER><TABLE>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Name') . ":</TD><TD><input type='Text' name='LastName' value='" . $myrow['lastname'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Contractor') . ":</TD><TD><input type='Text' name='contractor' value ='".$myrow['dept']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
    echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Section') . ':</TD><TD><SELECT  style="text-align:left; id="sec" name="section">'; 
			if ($myrow['section'] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow['section'].'">'.$myrow['section'];
			} 
		     $sql01 = 'SELECT distinct section  FROM clmssectionmaster order by section';
			$result01 = DB_query($sql01, $db);
		 
						  echo "<OPTION   VALUE='  '>" ;

			while ($myrow01 = DB_fetch_array($result01)) {

				  echo '<OPTION VALUE="'. $myrow01[0].'">' . $myrow01[0];

			} //end while loop

			 echo "</SELECT></TD>";
 	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('D O B') . ":</TD><TD><input type='Text' name='dob' value ='".$myrow['birthdate']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('D O J') . ":</TD><TD><input type='Text' name='doj' value ='".$myrow['hiredate']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Address') . ":</TD><TD><input type='Text' name='add' value ='".$myrow['address1']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Phone') . ":</TD><TD><input type='Text' name='phone1' value ='".$myrow['phone1']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('ESI No') . ":</TD><TD><input type='Text' name='esino' value ='".$myrow['esino']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('PF No') . ":</TD><TD><input type='Text' name='pfno' value ='".$myrow['pfno']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Aadhar No') . ":</TD><TD><input type='Text' name='aadhar' value ='".$myrow['aadhar']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Pan No') . ":</TD><TD><input type='Text' name='pan' value ='".$myrow['pan']."' SIZE=42 MAXLENGTH=40></TD></TR>";	
    echo '</table>';
	echo"<P><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Update Employee') . "'>";
	echo'</FORM>';
?>