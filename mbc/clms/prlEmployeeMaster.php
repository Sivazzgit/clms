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

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	// actions to take once the user has clicked the submit 'Insert New Employee' 
   // Checking if Employee ID is set
       if ($EmployeeID=="")
       {
           echo "<ul><li>Employee ID Not Set.</li></ul>";
           $InputError=1;	
       }
	   
       if ($_POST[LastName]=="")
       {
           echo "<ul><li>LastName must not be empty.</li></ul>";
           $InputError=1;	
       }
	   
/*  
  if ($_POST[FirstName]=="")
       {
           echo "<ul><li>FirstName must not be empty.</li></ul>";
           $InputError=1;	
       }

	   
       if ($_POST[HourlyRate]==0)
       {
           echo "<ul><li>Hourly rate must not be 0.</li></ul>"; 
           $InputError=1;	
       }

       if ($_POST[PeriodRate]==0)
       {
			$MyPeriodDesc=GetPayTypeDesc($_POST['PayType']);
			if ($MyPeriodDesc=='Salary')
			{
				echo "<ul><li>Pay per period must not be 0 for salaried employess.</li></ul>";
				$InputError=1;		
			}	
       }
   */
	   //if (!isset($_POST['New'])) {
	    if (!is_date($_POST['BirthDate'])) {
	   // Checking if Month, Day and Year fields have been filled  
			if (($_POST[Month]=="") or ($_POST[Day]=="") or ($_POST[Year]==""))   
			{
				prnMsg( _('The birthdate field must be a date in the format') . ' ' . $_SESSION['DefaultDateFormat'],'error');
				$InputError=1;	
			} else	{ 
				// Concatenating Month, Day and Year
				// for MySQL type Date (YYYY-MM-DD)
				$BirthDate=$_POST[Month]."/".$_POST[Day]."/".$_POST[Year];
				if (!is_date($BirthDate)) {
					prnMsg( _('The birthdate field must be a date in the format') . ' ' . $_SESSION['DefaultDateFormat'],'error');
					$InputError=1;	
				} else	{ 
					$SQL_BirthDate = FormatDateForSQL($BirthDate);

				}	
			}
	   } else {
			$SQL_BirthDate = FormatDateForSQL($_POST['BirthDate']);

	   }
	//		$SQL_hiredate = FormatDateForSQL($_POST['hiredate']);
	//		$SQL_confdate = FormatDateForSQL($_POST['confdate']);

	if ($InputError != 1){

	//	$SQL_SupplierSince = FormatDateForSQL($_POST['SupplierSince']);
		if (!isset($_POST['New'])) {
				$sql = "UPDATE clmsemployeemaster SET
					lastname='" . DB_escape_string($_POST['LastName']) . "',
					firstname='" . DB_escape_string($_POST['FirstName']) . "',				
					middlename='" . DB_escape_string($_POST['MiddleName']) . "',				
					address1='" . DB_escape_string($_POST['Address1']) . "',
					address2='" . DB_escape_string($_POST['Address2']) . "',
					city='" . DB_escape_string($_POST['City']) ."',
					state='" . DB_escape_string($_POST['State']) . "',
					zip='" . DB_escape_string($_POST['Zip']) . "',
					country='" . DB_escape_string($_POST['Country']) . "',
					costcenterid='" . $_POST['CostCenterID'] . "',	
					position='" . DB_escape_string($_POST['Position']) . "',
					birthdate='" . $SQL_BirthDate . "',
					marital='" . $_POST['Marital'] . "',
					gender='" . $_POST['Gender'] . "',
					active='" . $_POST['Active'] . "',
					esino='" . $_POST['esino'] . "',
					pfno='" . $_POST['pfno'] . "',
					address1='" . DB_escape_string($_POST['address1']) . "',
					hiredate='" . DB_escape_string($_POST['hiredate']) . "',
					confdate='" .DB_escape_string($_POST['confdate']) . "',
					phone1='" . DB_escape_string($_POST['phone1']) . "',
					phone2='" . DB_escape_string($_POST['phone2']) . "',
					pan='" . DB_escape_string($_POST['pan']) . "',
					aadhar='" . DB_escape_string($_POST['aadhar']) . "'
				
					
                WHERE employeeid = '$EmployeeID'";
			$ErrMsg = _('The employee could not be updated because');
			$DbgMsg = _('The SQL that was used to update the employee but failed was');
			$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
			prnMsg(_('The employee master record for') . ' ' . $EmployeeID . ' ' . _('has been updated'),'success');

		} else { //its a new employee
       			$sql = "INSERT INTO clmsemployeemaster (		
					employeeid,
					lastname,
					firstname,
					middlename,
					address1,
					address2,
					city,
					state,
					zip,
					country,
					costcenterid,
					position,
					birthdate,
					marital,
					gender,
                                        orgunit,
					active
					)
				VALUES ('$EmployeeID', 
					'" . DB_escape_string($_POST['LastName']) ."',
					'" . DB_escape_string($_POST['FirstName']) ."',
					'" . DB_escape_string($_POST['MiddleName']) ."',
					'" . DB_escape_string($_POST['Address1']) ."',
					'" . DB_escape_string($_POST['Address2']) . "',
					'" . DB_escape_string($_POST['City']) . "',
					'" . DB_escape_string($_POST['State']) . "',			
					'" . DB_escape_string($_POST['Zip']) . "',
					'" . DB_escape_string($_POST['Country']) . "',
					'" . $_POST['CostCenterID'] . "',				
					'" . DB_escape_string($_POST['Position']) . "',
					'" .$SQL_BirthDate. "',
					'" . $_POST['Marital'] . "',				
					'" . $_POST['Gender'] . "',				
                                           'CATL',
					'" . $_POST['Active'] . "'
					)";

			$ErrMsg = _('The employee') . ' ' . $_POST['LastName'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the employee but failed was');
			$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);

			prnMsg(_('A new employee for') . ' ' . $_POST['LastName'] . ' ' . _('has been added to the database'),'success');

			unset ($EmployeeID);
			unset($_POST['LastName']);
			unset($_POST['FirstName']);
			unset($_POST['MiddleName']);
			unset($_POST['Address1']);
			unset($_POST['Address2']);
			unset($_POST['City']);
			unset($_POST['State']);
			unset($_POST['Zip']);
			unset($_POST['Country']);
			unset($_POST['CostCenterID']);
			unset($_POST['Position']);
			unset($_POST['ATM']);
			unset($_POST['TAN']);
			unset($_POST['SSS']);
			unset($_POST['HDMF']);
			unset($_POST['PhilHealth']);
			unset($_POST['BirthDate']);
			unset($_POST['Marital']);
			unset($_POST['Gender']);
			unset($_POST['TaxStatusID']);
			unset($_POST['PayPeriodID']);
			unset($_POST['PayType']);
			unset($_POST['PeriodRate']);
			unset($_POST['HourlyRate']);
			unset($_POST['EmpStatID']);
			unset($_POST['Active']);
		}
		
	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'),'warn');

	}

} elseif (isset($_POST['delete']) AND $_POST['delete'] != '') {

//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;
	
		$sql = "SELECT counterindex,overtimeid,employeeid 
					FROM prlottrans
			        WHERE prlottrans.employeeid='" . $EmployeeID . "'";
					$EmpDetails = DB_query($sql,$db);
					if(DB_num_rows($EmpDetails)>0)
					{
						$CancelDelete = 1;
						exit("This employee has payroll records can not be deleted..");
					}

	if ($CancelDelete == 0) {
		$sql="DELETE FROM clmsemployeemaster WHERE employeeid='$EmployeeID'";
		$result = DB_query($sql, $db);
		prnMsg(_('Employee record for') . ' ' . $EmployeeID . ' ' . _('has been deleted'),'success');
		unset($EmployeeID);
		unset($_SESSION['EmployeeID']);
	} //end if Delete employee
} //end of (isset($_POST['submit'])) 


if (!isset($EmployeeID)) {
/*If the page was called without $EmployeeID passed to page then assume a new employee is to be entered show a form 
with a Employee Code field other wise the form showing the fields with the existing entries against the employee will 
show for editing with only a hidden EmployeeID field*/
	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";
	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";
	echo '<CENTER><TABLE>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Employee ID') . ":</TD><TD><INPUT TYPE='text' NAME='EmployeeID'  SIZE=11 MAXLENGTH=10></TD>;
	     '<TD><align=right><b>Accept Alpha Numeric Character</b></TD>'</TR>";
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Last Name') . ":</TD><TD><input type='Text' name='LastName' SIZE=42 MAXLENGTH=40></TD></TR>";
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('First Name') . ":</TD><TD><input type='Text' name='FirstName' SIZE=42 MAXLENGTH=40></TD></TR>";		
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Middle Name') . ":</TD><TD><input type='Text' name='MiddleName' SIZE=42 MAXLENGTH=40></TD></TR>";
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Address') . ":</TD><TD><input type='Text' name='Address1' SIZE=42 MAXLENGTH=40></TD></TR>";
	echo '<TR><TD>' . _('') . "</TD><TD><input type='Text' name='Address2' SIZE=42 MAXLENGTH=40></TD></TR>";
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('City') . ":</TD><TD><input type='Text' name='City' SIZE=42 MAXLENGTH=40></TD></TR>";		
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('State') . ":</TD><TD><input type='Text' name='State' SIZE=22 MAXLENGTH=20></TD></TR>";				
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Zip Code') . ":</TD><TD><input type='Text' name='Zip' SIZE=17 MAXLENGTH=15></TD></TR>";
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Country') . ":</TD><TD><input type='Text' name='Country' SIZE=42 MAXLENGTH=40></TD></TR>";
	echo '</SELECT></TD></TR>';	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Cost Center') . ":</TD><TD><SELECT NAME='CostCenterID'>";
	DB_data_seek($result, 0);
	$sql = 'SELECT code, description FROM workcentres';
	$result = DB_query($sql, $db);
	while ($myrow = DB_fetch_array($result)) {
		if ($_POST['CostCenterID'] == $myrow['code']){
			echo '<OPTION SELECTED VALUE=' . $myrow['code'] . '>' . $myrow['description'];
		} else {
			echo '<OPTION VALUE=' . $myrow['code'] . '>' . $myrow['description'];
		}
	} //end while loop

	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Position') . ":</TD><TD><input type='Text' name='Position' SIZE=42 MAXLENGTH=40></TD></TR>";		
	?>
	       <tr> 
	          <td width=200 height="20"> 
              <div align="right"><b>Date of Birth :</b></div>
              </td>
              <td height="20"> 
                <select name="Month">
   	            <option value="" SELECTED>Month</option>
                <option value=01>January</option>
                <option value=02>February</option>
                <option value=03>March</option>
                <option value=04>April</option>
                <option value=05>May</option>
                <option value=06>June</option>
                <option value=07>July</option>
                <option value=08>August</option>
                <option value=09>September</option>
                <option value=10>October</option>
                <option value=11>November</option>
                <option value=12>December</option>
              </select>
              <select name="Day">
                <option value="" SELECTED>Day</option>
                <option value=01>01</option>
                <option value=02>02</option>
                <option value=03>03</option>
                <option value=04>04</option>
                <option value=05>05</option>
                <option value=06>06</option>
                <option value=07>07</option>
                <option value=08>08</option>
                <option value=09>09</option>
                <option value=10>10</option>
                <option value=11>11</option>
                <option value=12>12</option>
                <option value=13>13</option>
                <option value=14>14</option>
                <option value=15>15</option>
                <option value=16>16</option>
                <option value=17>17</option>
                <option value=18>18</option>
                <option value=19>19</option>
                <option value=20>20</option>
                <option value=21>21</option>
                <option value=22>22</option>
                <option value=23>23</option>
                <option value=24>24</option>
                <option value=25>25</option>
                <option value=26>26</option>
                <option value=27>27</option>
                <option value=28>28</option>
                <option value=29>29</option>
                <option value=30>30</option>
                <option value=31>31</option>
              </select>
              <select name="Year">
              <option value="" Selected>Year</option>
              <?php
              
                    for ($yy=1900;$yy<=2010;$yy++)
                    {
                       
                        echo "<option value=$yy>$yy</option>\n";
                    	
                    }
              ?>
              </select>
              <?php echo $star; ?>
              </td>
          </tr>
		            <tr> 
            <td width=200> 
              <div align="right"><b>Marital Status :</b></div>
            </td>
            <td> 
              <select name="Marital">
                <option SELECTED value=""> Select One </option>
                <option value="Single"> Single </option>
                <option value="Married"> Married </option>
                <option value="Sep/Div"> Separated/Divorced </option>
                <option value="Widowed"> Widowed </option>
              </select>
            </td>
          </tr>
          <tr> 
            <td width=200 height="21"> 
              <div align="right"><b>Gender : </b></div>
            </td>
            <td height="21"> 
              <input type=radio CHECKED value=M name=Gender>
              Male 
              <input type=radio value=F name=Gender>
              Female </td>
          </tr>
	<?php	  

	echo "</SELECT></TD></TR></TABLE><p><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Insert New Employee') . "'>";
	echo '</FORM>';

} else {
//EmployeeID exists - either passed when calling the form or from the form itself
	echo "<FORM METHOD='post' action='" . $_SERVER['PHP_SELF'] . '?' . SID ."'>";
	echo '<CENTER><TABLE>';
		if (!isset($_POST['New'])) {
		$sql = "SELECT  employeeid,
					lastname,
					firstname,
					middlename,
					address1,
					address2,
					city,
					state,
					zip,
					country,
					costcenterid,
					position,
					birthdate,
					marital,
					gender,
					active,
					esino,
					pfno,
			            hiredate,
                        confdate,
 			            address1,
                        phone1,
                        phone2,
						pan,
						aadhar
						
			FROM clmsemployeemaster
			WHERE employeeid = '$EmployeeID'";
			$result = DB_query($sql, $db);
			$myrow = DB_fetch_array($result);
		$_POST['LastName'] = $myrow['lastname'];
		$_POST['FirstName'] = $myrow['firstname'];
		$_POST['MiddleName'] = $myrow['middlename'];
		$_POST['Address1']  = $myrow['address1'];
		$_POST['Address2']  = $myrow['address2'];
		$_POST['City']  = $myrow['city'];
		$_POST['State']  = $myrow['state'];	
		$_POST['Zip']  = $myrow['zip'];
		$_POST['Country']  = $myrow['country'];
		$_POST['CostCenterID']  = $myrow['costcenterid'];
		$_POST['Position']  = $myrow['position'];
		$_POST['BirthDate']  = ConvertSQLDate($myrow['birthdate']);
		$_POST['Marital']  = $myrow['marital'];
		$_POST['Gender']  = $myrow['gender'];		
		$_POST['Active']  = $myrow['active'];		
		$_POST['esino']  = $myrow['esino'];		
		$_POST['pfno']  = $myrow['pfno'];		
		$_POST['hiredate']  = $myrow['hiredate'];		
		$_POST['confdate']  = $myrow['confdate'];		
		$_POST['address1']  = $myrow['address1'];		
		$_POST['phone1']  = $myrow['phone1'];		
		$_POST['pan']  = $myrow['pan'];		
		$_POST['aadhar']  = $myrow['aadhar'];		
		
		echo "<INPUT TYPE=HIDDEN NAME='EmployeeID' VALUE='$EmployeeID'>";
		} else {
		// its a new employee  being added
		echo "<INPUT TYPE=HIDDEN NAME='New' VALUE='Yes'>";
		echo '<TR><TD>' . _('Employee ID') . ":</TD><TD><INPUT TYPE='text' NAME='EmployeeID' VALUE='$EmployeeID' SIZE=12 MAXLENGTH=10></TD></TR>";
		}
		
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Last Name') . ":</TD><TD><input type='Text' name='LastName' value='" . $_POST['LastName'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";	
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('First Name') . ":</TD><TD><input type='Text' name='FirstName' value='" . $_POST['FirstName'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";			
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Middle Name') . ":</TD><TD><input type='Text' name='MiddleName' value='" . $_POST['MiddleName'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";			
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Address') . ":</TD><TD><input type='Text' name='Address1' value='" . $_POST['Address1'] . "' SIZE=42 ></TD></TR>";			
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('') . ":</TD><TD><input type='Text' name='Address2' value='" . $_POST['Address2'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";			
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('City') . ":</TD><TD><input type='Text' name='City' value='" . $_POST['City'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";		
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('State') . ":</TD><TD><input type='Text' name='State' value='" . $_POST['State'] . "' SIZE=22 MAXLENGTH=20></TD></TR>";				
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Zip Code') . ":</TD><TD><input type='Text' name='Zip' value='" . $_POST['Zip'] . "' SIZE=17 MAXLENGTH=15></TD></TR>";
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Country') . ":</TD><TD><input type='Text' name='Country' value='" . $_POST['Country'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";
	echo '</SELECT></TD></TR>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Cost Center') . ":</TD><TD><SELECT NAME='CostCenterID'>";
	DB_data_seek($result, 0);
	$sql = 'SELECT code, description FROM workcentres';
	$result = DB_query($sql, $db);
	while ($myrow = DB_fetch_array($result)) {
	     if ($myrow['code'] == $_POST['CostCenterID']) {  
		 	echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['code'] . '>' . $myrow['description'];
	} //end while loop
	echo '</SELECT></TD></TR>';
    echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Position') . ":</TD><TD><input type='Text' name='Position' value='" . $_POST['Position'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";		
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Date of Birth') . ":</TD><TD><input type='Text' name='BirthDate' value='" . $_POST['BirthDate'] . "' SIZE=22 MAXLENGTH=20></TD>";
	echo '<TD><align=right><b>format (mm/dd/yyyy)</b></TD>';
	echo '</SELECT></TD></TR>';
	echo '</SELECT></TD></TR><TR><TD width=200 height=20><div align="right"><b>' . _('Marital Status') . ":</TD><TD><SELECT NAME='Marital'>";
	if ($_POST['Marital']=='Single'){
		echo '<OPTION SELECTED VALUE="Single">' . _('Single');
		echo '<OPTION VALUE="Married">' . _('Married');
		echo '<OPTION VALUE="Sep/Div">' . _('Separated/Divorced');
		echo '<OPTION VALUE="Widowed">' . _('Widowed');
	} elseif ($_POST['Marital']=='Married'){
		echo '<OPTION SELECTED VALUE="Married">' . _('Married');
		echo '<OPTION VALUE="Single">' . _('Single');
		echo '<OPTION VALUE="Sep/Div">' . _('Separated/Divorced');
		echo '<OPTION VALUE="Widowed">' . _('Widowed');	
	} elseif ($_POST['Marital']=='Sep/Div'){
	    echo '<OPTION SELECTED VALUE="Sep/Div">' . _('Separated/Divorced');
	    echo '<OPTION VALUE="Single">' . _('Single');
		echo '<OPTION VALUE="Married">' . _('Married');
	    echo '<OPTION VALUE="Widowed">' . _('Widowed');	
    } elseif ($_POST['Marital']=='Widowed'){
	    echo '<OPTION SELECTED VALUE="Widowed">' . _('Widowed');
	    echo '<OPTION VALUE="Single">' . _('Single');
		echo '<OPTION VALUE="Married">' . _('Married');
		echo '<OPTION VALUE="Sep/Div">' . _('Separated/Divorced');
	} else {
		echo '<OPTION SELECTED VALUE="">' . _('Select One');
		echo '<OPTION VALUE="Single">' . _('Single');
		echo '<OPTION VALUE="Married">' . _('Married');
		echo '<OPTION VALUE="Sep/Div">' . _('Separated/Divorced');
		echo '<OPTION VALUE="Widowed">' . _('Widowed');
	}
	
	echo '</SELECT></TD></TR>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Gender') . ":</TD><TD><SELECT NAME='Gender'>";
	if ($_POST['Gender'] == 'M'){
		echo '<OPTION SELECTED VALUE="M">' . _('Male');
		echo '<OPTION VALUE="F">' . _('Female');
	} else {
		echo '<OPTION SELECTED VALUE="F">' . _('Female');
		echo '<OPTION VALUE="M">' . _('Male');
	}
	echo '</SELECT></TD></TR>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Employment Status') . ":</TD><TD><SELECT NAME='Active'>";		
	if ($_POST['Active'] == 0){
		echo '<OPTION SELECTED VALUE=0>' . _('Active');
		echo '<OPTION VALUE=1>' . _('InActive');
	} else {
		echo '<OPTION VALUE=0>' . _('Active');
		echo '<OPTION SELECTED VALUE=1>' . _('InActive');
	}
    echo '<TD><align=right><b>Active employee only are included in payroll</b></TD>';
	echo '</SELECT></TD></TR>';		
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("ESI No") . ':</TD><TD><input type="Text" name="esino" SIZE=14 MAXLENGTH=12 value="' . $_POST["esino"] . '"></TD>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("PF No") . ':</TD><TD><input type="Text" name="pfno" SIZE=14 MAXLENGTH=12 value="' . $_POST["pfno"] . '"></TD>';

	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("Address") . ':</TD><TD><input type="Text" name="address1"  value="' . $_POST["address1"] . '"></TD>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("D O J") . ':</TD><TD><input type="Text" name="hiredate" SIZE=22 MAXLENGTH=22 value="' . $_POST["hiredate"] . '"></TD>';
	echo '<TD><align=right><b>format (YYYY/MM/DD)</b></TD>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("Conf.Date") . ':</TD><TD><input type="Text" name="confdate" SIZE=22 MAXLENGTH=22 value="' . $_POST["confdate"] . '"></TD>';
	echo '<TD><align=right><b>format (YYYY/MM/DD)</b></TD>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("Phone 1") . ':</TD><TD><input type="Text" name="phone1" SIZE=14 MAXLENGTH=12 value="' . $_POST["phone1"] . '"></TD>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("Phone 2") . ':</TD><TD><input type="Text" name="phone2" SIZE=14 MAXLENGTH=12 value="' . $_POST["phone2"] . '"></TD>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("Pan No") . ':</TD><TD><input type="Text" name="pan" SIZE=14 MAXLENGTH=12 value="' . $_POST["pan"] . '"></TD>';
	echo '<TR><TD width=200 height=20><div align="right"><b>' . _("Aadhar No") . ':</TD><TD><input type="Text" name="aadhar" SIZE=14 MAXLENGTH=12 value="' . $_POST["aadhar"] . '"></TD>';

	if (isset($_POST['New'])) {
		echo "</TABLE><P><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Add These New Employee Details') . "'></FORM>";
	} else {
		echo "</TABLE><P><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Update Employee') . "'>";
		echo '<P><FONT COLOR=red><B>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure there are no outstanding purchase orders or existing accounts payable transactions before the deletion is processed') . '<BR></FONT></B>';
		echo "<INPUT TYPE='Submit' NAME='delete' VALUE='" . _('Delete Employee') . "' onclick=\"return confirm('" . _('Are you sure you wish to delete this employee?') . "');\"></FORM>";
	}

} // end of main ifs

include('includes/footer.inc');

?>