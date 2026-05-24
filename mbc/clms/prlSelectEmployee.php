<?php
/* $Revision: 1.0 $ */

$PageSecurity = 10;
include('includes/sess.inc'); 
$title = _('Employee Master ');
 
include('includes/headerclms.inc');


 //  echo '<left><a href="employeecard.php">' . _('Employee Card') . '</a><BR>';
 // echo '<left><a href="notyettt.php">' . _('Employee Card') . '</a><BR>';
$_SESSION['']='';
    echo "<CENTER><TABLE WIDTH=30% BORDER=2><TR></TR>";		
	echo '<TR><TD WIDTH=100%>';
	//echo '<CENTER><a href="' . $rootpath . '/prlEmployeeMaster.php">' . _('Add employee records') . '</a><BR>';
    echo '<CENTER><a href="' . $rootpath . '/prlEmployeeMasterAddnew.php?SelectedAccountr=' . $_SESSION[''] . '">' . _('Add employee records') . '</a><BR>';
	echo '<CENTER><a href="' . $rootpath . '/separation.php">' . _('Employee Separation') . '</a><BR>';
	echo '</TD><TD WIDTH=100%>';
    echo '</TD></TR></TABLE><BR></CENTER>';

if ( isset($_GET['EmployeeID']) )
	$EmployeeID = $_GET['EmployeeID'];
elseif (isset($_POST['EmployeeID']))
	$EmployeeID = $_POST['EmployeeID'];
	
	
	
if (isset($_GET['delete']))
 {
//the link to delete a selected record was clicked instead of the submit button
	// Get the original name of the marital status the ID is just a secure way to find the marital status
	//$sql = "SELECT employeemaster.employeeid FROM employeemaster
	//	WHERE employeemaster.employeeid = " . DB_escape_string($SelectedEmployeeID);
	//$result = DB_query($sql,$db);
	//if ( DB_num_rows($result) == 0 ) {
		// This is probably the safest way there is
	//	prnMsg( _('Cannot delete this marital description because it no longer exist'),'warn');
	//} else {
    //    $myrow = DB_fetch_row($result);
	//	$OldEmployeeID = $myrow[0];
				$sql="DELETE FROM clmsemployeemaster WHERE employeeid ".LIKE."'" . DB_escape_string($SelectedEmployeeID) . "'";
				$result = DB_query($sql,$db);
				prnMsg( 'employee id has been deleted' . '!','success');
	//}
	//end if account group used in GL accounts
	unset ($EmployeeID);
	unset ($_GET['EmployeeID']);
	unset($_GET['delete']);
	unset ($_POST['EmployeeID']);
	//unset ($_POST['EmployeeID']);
 }	

if (isset($EmployeeID)) {
/* It could still be the second time the page has been run and a record has been selected for modification - SelectedAccount will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of ChartMaster will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

    $sql="Select count(*) FROM clmsemployeemaster where orgunit <> ''"; 
    $result0 = DB_query($sql,$db,$ErrMsg);
    $myrow0 = DB_fetch_row($result0);
	$mrecordcount=$myrow0[0];
	$mrecordcount = $mrecordcount - 1;

 /*
	$sql = "SELECT employeeid,
			firstname,
			lastname,
			position,  
			birthdate,
                        hiredate,
                        confdate,
                        father,  
			bloodgroup,
                        qualification,
                        Bank_eid,
                        bank_sbi,
                        bank_default,
			address1,
                        phone1,
                        phone2,
						active



		FROM clmsemployeemaster where orgunit = 'CATL'
		ORDER BY employeeid";
	$ErrMsg = _('The employee master could not be retrieved because');
	$result = DB_query($sql,$db,$ErrMsg);

	echo '<CENTER><table border=1>';
	echo "<tr>
		<td class='tableheader'>" . _('SL.No') . "</td>
		<td class='tableheader'>" . _('Employee ID') . "</td>
		<td class='tableheader'>" . _('Title ') . "</td>
		<td class='tableheader'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _('Name') . "</td>
		<td class='tableheader' >" . _('Position  ') . "</td>
		<td class='tableheader'>" . _('Date of Birth ') . "</td>
		<td class='tableheader'>" . _('Date of Joining') . "</td>
		<td class='tableheader'>" . _('Date of Confirmation ') . "</td>
		<td class='tableheader'>" . _('Fathers Name ') . "</td>
		<td class='tableheader'>" . _('Blood Group ') . "</td>
		<td class='tableheader'>" . _('Qualification ') . "</td>
		<td class='tableheader'>" . _('Bank EID No ') . "</td>
		<td class='tableheader'>" . _('Bank SBI NO ') . "</td>
		<td class='tableheader'>" . _('Default Bank ') . "</td>
		<td class='tableheader'>" . _('Address') . "</td>
		<td class='tableheader'>" . _('Contact-1 ') . "</td>
		<td class='tableheader'>" . _('Contact-2 ') . "</td>
		<td class='tableheader'>" . _('Status') . "</td>
 
	
	</tr>";
*/	

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


$sql = "SELECT employeeid,
				lastname,
			dept,section, 
			DATE_FORMAT(birthdate,'%d-%m-%Y')as'birthdate',
			DATE_FORMAT(hiredate,'%d-%m-%Y')as'hiredate',
 			address1,
                        phone1,
 						esino,pfno,aadhar,pan,active 



		FROM clmsemployeemaster where orgunit = 'Kancor' and active=0 and trim(dept) like'".$kkdept."%'
		ORDER BY (-1000 - employeeid) desc";

if($_SESSION['UserID']=='clmsadmin'){
	
$sql = "SELECT employeeid,
				lastname,
			dept,section, 
			DATE_FORMAT(birthdate,'%d-%m-%Y')as'birthdate',
			DATE_FORMAT(hiredate,'%d-%m-%Y')as'hiredate',
 			address1,
                        phone1,
 						esino,pfno,aadhar,pan,active 



		FROM clmsemployeemaster where orgunit = 'Kancor' and active=0
		ORDER BY (-1000 - employeeid) desc";
		
				
}	

	$ErrMsg = _('The employee master could not be retrieved because');
	$result = DB_query($sql,$db,$ErrMsg);
		echo '<left><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","empdetails");>Export to XLS</button></left>';	

	echo '<CENTER><table class="mytd" id="tbl1" border=1 width="100%">';
	echo "<tr>
		<th><b>" . _('Edit') . "</td>
		<th><b>" . _('SL.No') . "</td>
		<th><b>" . _('Employee ID') . "</td>
		<th><b>" . _('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') . "</td>
		<th><b>" . _('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contractor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ') . "</td>
		<th><b>" . _('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Section&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ') . "</td>
		<th><b>" . _('<br>Date of <br> Birth &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') . "</td>
		<th><b>" . _('Date of Joining&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') . "</td>
		<th ><b>" . _('Address') . "</td>
		<th ><b>" . _('Contact-1 ') . "</td>  
		<th ><b>" . _('ESI No') . "</td> 
 		<th ><b>" . _('PF No') . "</td> 
		<th ><b>" . _('Aadhar No') . "</td> 
		<th ><b>" . _('Pan No') . "</td> 
		<th ><b>" . _('Status') . "</td> 
 		<th></th>
	
	</tr>"; 

	$k=0; //row colour counter
$mslno=0;
		while ($myrow = DB_fetch_row($result)) {
         $mslno++;
		if ($k==1){
			echo "<TR BGCOLOR='#CCCCCC'>";
			$k=0;
		} else {
			echo "<TR BGCOLOR='#EEEEEE'>";
			$k++;
		}
		echo '<TD><A HREF="'. $rootpath . '/prlEmployeeMasterEdit.php?' . SID . '&EmployeeID='.$myrow[0].'">' . _('Edit') . '</A></TD>'; 
		echo '<TD><b>' . $mslno . '</TD>';
		echo '<TD><b>' . $myrow[0]. '</TD>';
		echo '<TD><b>' . $myrow[1] . '</TD>';
		echo '<TD><b>' . $myrow[2] . '</b></TD>';
		echo '<TD><b>' . $myrow[3] . '</b></TD>';
		echo '<TD><b>' . $myrow[4] . '</TD>';
		echo '<TD><b>' . $myrow[5] . '</b></TD>';
		echo '<TD><b>' . $myrow[6] . '</b></TD>';
		echo '<TD><b>' . $myrow[7] . '</b></TD>';
		
		
		echo '<TD><b>' . $myrow[8] . '</TD>';
		echo '<TD><b>' . $myrow[9] . '</TD>';
/*
		echo '<TD>' . $myrow[12] . '</TD>';
		echo '<TD>' . $myrow[13] . '</TD>';
		echo '<TD>' . $myrow[14] . '</TD>';
		echo '<TD>' . $myrow[15] . '</TD>';
		echo '<TD>' . $myrow[16] . '</TD>';
*/
	

		echo '<TD>' . $myrow[10] . '</TD>';

		echo '<TD>' . $myrow[11] . '</TD>';
		if ($myrow[12]==0){
		echo '<TD><b>Active</TD>';
		}else{
		echo '<TD>IN Active</TD>';
		}
		//echo '<TD>' . $myrow[12] . '</TD>';

		//echo '<TD>' . $myrow[17] . '</TD>';
	//	echo '<TD><A HREF="'. $rootpath . '/prlEmployeeMasterEdit.php?' . SID . '&EmployeeID='.$myrow[0].'">' . _('Edit') . '</A></TD>'; 
		//echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&EmployeeID=' . $myrow[0] . '&delete=1">' . _('Delete') .'</A></TD>';		
		echo '</TR>';

	} //END WHILE LIST LOOP

	//END WHILE LIST LOOP
} //END IF SELECTED ACCOUNT


echo '</CENTER></TABLE>';
//end of ifs and buts!

//include('includes/footer.inc');
?>