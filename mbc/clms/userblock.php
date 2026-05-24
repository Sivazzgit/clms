<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

$PageSecurity = 10;
include('includes/sess.inc'); 
include('includes/headerclms.inc');
//include('catllocationadmin.php') ;

$title = _('User Blocking');
/*
if(rtrim($_SESSION['UserID'])!="demo" && rtrim($_SESSION['UserID']!="admin" ))
{
	echo "<center<h2 style='color:Red;'>".$_SESSION['UserID'].":Only Admin has the access to this page.</h2>";
	exit();
}
*/
$sql = "SELECT 
			userid,
			realname  
		FROM www_users where substr(userid,0,4)='clms'
		";
	$ErrMsg = _('The employee master could not be retrieved because');
	$result = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_row($result);
	if($myrow){
	$dept = $myrow[0];
    $location = $myrow[1];
}else{
	$dept = '';
    $location = '';
    
}
//include('includes/header.inc');

if ( isset($_GET['SelectedStatusID']) )
	$SelectedStatusID = $_GET['SelectedStatusID'];
elseif (isset($_POST['SelectedStatusID']))
	$SelectedStatusID = $_POST['SelectedStatusID'];

if (isset($_GET['blocked'])) {

	//initialise no input errors assumed initially before we test

	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

/*
	if (strpos($_POST['EmploymentName'],'&')>0 OR strpos($_POST['EmploymentName'],"'")>0) {
		//$InputError = 1;
		prnMsg( _('The employment description cannot contain the character') . " '&' " . _('or the character') ." '",'error');
	}
	*/
	if ($_GET['SelectedStatusID']!='' AND $InputError !=1) {

		/*SelectedStatusID could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
		// Check the name does not clash
      /*
		$sql = "SELECT count(*) FROM goalmaster	WHERE employmentid <> " . $SelectedStatusID ." 	AND goal ".LIKE." '" . $_POST['EmploymentName'] . "'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
      */
		//if ( $myrow[0] > 0 ) {
		if ( $myrow) {
			$InputError = 1;
			prnMsg( _('The employment description can not be renamed because another with the same goal name already exist.'),'error');
		} else {
			// Get the old name and check that the record still exist neet to be very carefull here
			// idealy this is one of those sets that should be in a stored procedure simce even the checks are 
			// relavant
			$sql = "SELECT * FROM www_users
				WHERE trim(userid) = '" . trim($SelectedStatusID)."'"; 
			$result = DB_query($sql,$db);
			if ( DB_num_rows($result) != 0 ) {
				// This is probably the safest way there is
				$myrow = DB_fetch_row($result);
				$OldEmploymentName = $myrow[0];
				$sql = array();
				$sql[] = "UPDATE www_users
					SET blocked=" . DB_escape_string($_GET['blocked']) . "
					WHERE trim(userid) ='" . trim($SelectedStatusID)."'";
				//$sql[] = "UPDATE stockmaster
				//	SET units='" . DB_escape_string($_POST['EmploymentName']) . "'
				//	WHERE units ".LIKE." '" . $OldEmploymentName . "'";
				//$sql[] = "UPDATE contracts
				//	SET units='" . DB_escape_string($_POST['EmploymentName']) . "'
				//	WHERE units ".LIKE." '" . $OldEmploymentName . "'";
			} else {
				$InputError = 1;
				prnMsg( _('The employment description no longer exist.'),'error');
			}
		}
		$msg = _('Action completed successfully');
	} elseif ($InputError !=1) {
		/*SelectedStatusID is null cos no item selected on first time round so must be adding a record*/
		$sql = "SELECT count(*) FROM goalmaster 
				WHERE goal " .LIKE. " '".$_POST['EmploymentName'] ."'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('The employment description can not be created because another with the same name already exists.'),'error');
		} else {
			$sql = "INSERT INTO goalmaster (
                                                employmentid,level, 
						goal,
                                                 goaldes,
                                                 dept,
                                                 unit )
				VALUES (
					'" . DB_escape_string($_POST['SelectedStatusID']) ."',
					'" . DB_escape_string($_POST['level']) ."',
					'" . DB_escape_string($_POST['EmploymentName']) ."',
					'" .DB_escape_string($_POST['EmploymentGoaldes']) ."',
					'" .DB_escape_string($_POST['dept']) ."',
					'" .DB_escape_string($_POST['unit']) ."'
					)";
		}
		$msg = _('New employment description added');
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
	if (is_array($sql)) {
			$result = DB_query('BEGIN',$db);
			$tmpErr = _('Could not update Employment description');
			$tmpDbg = _('The sql that failed was') . ':';
			foreach ($sql as $stmt ) {
				$result = DB_query($stmt,$db, $tmpErr,$tmpDbg,true);
				if(!$result) {
					$InputError = 1;
					break;
				}
			}
			if ($InputError!=1){
				$result = DB_query('COMMIT',$db);
			} else {
				$result = DB_query('ROLLBACK',$db);
			}
		} else {
			$result = DB_query($sql,$db);
		}
		prnMsg($msg,'success');
	}
	unset ($SelectedStatusID);
	unset ($_POST['SelectedStatusID']);
	unset ($_POST['EmploymentName']);
	unset ($_POST['level']);

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button
// PREVENT DELETES IF DEPENDENT RECORDS IN 'stockmaster'
	// Get the original name of the employment status the ID is just a secure way to find the employment status
	$sql = "SELECT goal FROM goalmaster
		WHERE employmentid = " . DB_escape_string($SelectedStatusID);
	$result = DB_query($sql,$db);
	if ( DB_num_rows($result) == 0 ) {
		// This is probably the safest way there is
		prnMsg( _('Cannot delete this employment description because it no longer exist'),'warn');
	} else {
		$myrow = DB_fetch_row($result);
		$OldEmploymentName = $myrow[0];
		$sql= "SELECT COUNT(*) FROM workcentres";
// WHERE units ".LIKE." '" . DB_escape_string($OldEmploymentName) . "'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]=0) {
			prnMsg( _('Cannot delete this as there are employees with this grade'),'warn');
//			echo '<br>' . _('There are') . ' ' . $myrow[0] . ' ' . _('inventory items that refer to this employment status') . '</FONT>';
		} else {
			$sql= "SELECT COUNT(*) FROM workcentres";
                // WHERE units ".LIKE." '" . DB_escape_string($OldEmploymentName) . "'";
			$result = DB_query($sql,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]=0)  {
				prnMsg( _('Cannot delete this employment status because there exist employees with this grade'),'warn');
			echo '<br>' . _('There are') . ' ' . $myrow[0] . ' ' . _('contracts that refer to this employment status') . '</FONT>';
			} else {
				$sql="DELETE FROM goalmaster  WHERE goal ".LIKE."'" . DB_escape_string($OldEmploymentName) . "'";
				$result = DB_query($sql,$db);
				prnMsg( $OldEmploymentName . ' ' . _('Goal details has been deleted') . '!','success');
			}
		}

	} //end if account group used in GL accounts
	unset ($SelectedStatusID);
	unset ($_GET['SelectedStatusID']);
	unset($_GET['delete']);
	unset ($_POST['SelectedStatusID']);
	unset ($_POST['StatusID']);
	unset ($_POST['EmploymentName']);
}

 if (1) {

/* An employment status could be posted when one has been edited and is being updated 
  or GOT when selected for modification
  SelectedStatusID will exist because it was sent with the page in a GET .
  If its the first time the page has been displayed with no parameters
  then none of the above are true and the list of account groups will be displayed with
  links to delete or edit each. These will call the same page again and allow update/input
  or deletion of the records*/

		if($_SESSION['UserID']=="demo" || $_SESSION['UserID']=="admin" || $_SESSION['UserID']=="CIEL" )
		$sql="SELECT userid,realname,blocked from www_users";
		else
     $sql="SELECT userid,realname,olduserid,blocked from www_users where substr(trim(userid),1,4)='clms'";
//	echo $sql;  

	$ErrMsg = _('Could not get employment status because');
	$result = DB_query($sql,$db,$ErrMsg);
    echo"<h3><CENTER>User Blocking Status</CENTER></h3>";
	echo "<CENTER><TABLE class='mytb' border='1'>
		<TR>
		<Th class='tableheader'>" . _('User Id') . "</TH>
		<TH class='tableheader'>" . _('Name') . "</TH>
		<TH class='tableheader'>" . _('Area') . "</TH>
		<TH class='tableheader'>" . _('Blocked') . "</TH>
		<TH class='tableheader'></TH>
		<TH class='tableheader'></TH>
		</TR>";
       $k=0; //row colour counter
	while ($myrow = DB_fetch_row($result)) {

		
		echo '<TD>' . $myrow[0] . '</TD>';
		echo '<TD>' . $myrow[1] . '</TD>';
		echo '<TD>' . $myrow[2] . '</TD>';
		if($myrow[3] == 0)
		{	
			$linktext = "Block";
			$myrow[3] = "Active";
			$active =1;
		}
		else
		{
			$myrow[3] ="Blocked";
			$linktext = "UnBlock";
			$active =0;
		}
		echo '<TD>' .$myrow[3]  . '</TD>';
		echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&SelectedStatusID='.$myrow[0].'&blocked=' . ($active) .'">' .$linktext . '</A></TD>';
	//	echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&SelectedStatusID=' . $myrow[0] . '&delete=1">' . _('Delete') .'</A></TD>';
		echo '</TR>';

	} //END WHILE LIST LOOP
	echo '</table></CENTER><p>';
} //end of ifs and buts!


if (isset($SelectedStatusID)) {
	echo '<CENTER><A HREF=' . $_SERVER['PHP_SELF'] . '?' . SID .'>' . _('User Blocking Status') . '</a></Center>'; 
}

echo '<P>';

//if (! isset($_GET['delete'])) {
	if(0){

	echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

	if (isset($SelectedStatusID)) {
		//editing an existing section

		$sql = "SELECT userid,blocked
				FROM www_users
				WHERE userid=" . DB_escape_string($SelectedStatusID);
		$result = DB_query($sql, $db);
		if ( DB_num_rows($result) == 0 ) {
			prnMsg( _('Could not retrieve the requested employment status, please try again.'),'warn');
			unset($SelectedStatusID);
		} else {
			$myrow = DB_fetch_array($result);

			$_POST['SelectedStatusID'] = $myrow['userid'];
			$_POST['blocked']  = $myrow['blocked'];

			echo "<INPUT TYPE=HIDDEN NAME='SelectedStatusID' VALUE='" . $_POST['SelectedStatusID'] . "'>";
			echo "<CENTER><TABLE>";
		}

	}  else {
		$_POST['SelectedStatusID']='';
		$_POST['level']='';
		$_POST['EmploymentName']='';
		$_POST['EmploymentGoaldes']='';
		echo "<CENTER><TABLE>";
	}
//	echo "<TR>
//<TD>" . _('SLNO') . ':' . "</TD>
//		<TD><input type='Text' name='Grade Code' SIZE=50 MAXLENGTH=50 value='" . //$_POST['SelectedStatusID'] . "'></TD>
//</TR>";
///*

/*
	echo '<TR><TD width=300 height=20><div align="left">' . _('Employee') . ":</TD><TD><SELECT NAME='EmploymentName' ";
	DB_data_seek($result, 0);
	$sql = 'SELECT goaleeid, lastname FROM prlemployeemaster';
	$result = DB_query($sql, $db);
	while ($myrow = DB_fetch_array($result)) {
		if ($_POST['EmploymentName'] == $myrow['employeeid']){
			echo '<OPTION SELECTED VALUE=' . $myrow['employeeid'] . '>' . $myrow['lastname'];
		} else {
                        echo '<OPTION VALUE=' . $myrow['employeeid'] . '>' . $myrow['lastname'];

		}
	} //end while loop
          
*/
	
	
    
	
	echo "<TR>
		<TD>" . _('Status') . ':' . "</TD>
		<TD><input type='Text' name='blocked' SIZE=50 MAXLENGTH=50 value='" . $_POST['blocked'] . "'></TD>
		</TR>";

	
		echo '</TABLE>';

	echo '<CENTER><input type=Submit name=submit value=' . _('Enter Information') . '>';

	echo '</FORM>';

} //end if record deleted no point displaying form to add record

//include('includes/footer.inc');
?>