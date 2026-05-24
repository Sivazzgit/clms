<?php



/* $Revision: 1.0 $ */

//exit("Break-1");



$PageSecurity = 10;

include('includes/sess.inc');

$title = _('Contract Labour Engagement : Add New Record');



include('includes/headerclms.inc'); 

 echo'   <script src="script.js"></script>';



if (isset($_GET['EmployeeID'])){

	$EmployeeID = strtoupper($_GET['EmployeeID']);

} elseif (isset($_POST['EmployeeID'])){

	$EmployeeID = strtoupper($_POST['EmployeeID']);

} else {

	unset($EmployeeID);

}

if(isset($_GET['vendor'])){

$mvendor=trim($_GET['vendor']); 

$msection=trim($_GET['section']); 

$mstdate=$_GET['stdate'];

}else{

$mvendor=''; 

$msection=''; 

$mstdate='';

    

    

}



//echo"Vendor ".$mvendor;

//echo"<br>St Date ".$mstdate;
if(substr($_SESSION['UserID'],0,5)=='clms0' ){
	$mvendor=$_SESSION['UserID'];
}
if(trim($mvendor)=='CARE'){

   $mvendor='CARE & CONCERN';	

}
//echo"Vendor ".$mvendor;

//echo"<br>St Date ".$mstdate;

//echo"<br>section ".$msection;





$sql09 = "SELECT olduserid from www_users where userid='".$_SESSION['UserID']."'";

	$ErrMsg = _('The employee master could not be retrieved because'); 

//echo $sql09;	



	$result09 = DB_query($sql09,$db,$ErrMsg);

	$myrow09 = DB_fetch_array($result09);

	





//echo"Vendor ".$mvendor; 

//echo"<br>St Date ".$mstdate; 



//echo "RRRRRRRR".$EmployeeID; 



 $sql = "SELECT vendor,section,stdate,endate,ashift,bshift,cshift,urgent from clmsengaged where trim(vendor) like '".$mvendor."%' and trim(section) like '".$msection."%' and trim(stdate)='".$mstdate."'";

	$ErrMsg = _('The employee master could not be retrieved because'); 

//echo $sql;	

	$result = DB_query($sql,$db,$ErrMsg);

	$myrow = DB_fetch_array($result);

	

	if(!$myrow){

	 $myrow['stdate']=$myrow['endate']='';   

	 $myrow['ashift']=$myrow['bshift']=$myrow['cshift']=$myrow['urgent']=0;

	}

	//echo "RRRRRR". $myrow[0].$myrow[1].$myrow[2];

//	echo "<FORM METHOD='post' action='" . $_SERVER['PHP_SELF'] . '?' . SID ."'>";  

	echo "<FORM METHOD='post' action='addlabourengagementdata1.php'>";  

//	echo "<INPUT TYPE=HIDDEN NAME='empid' VALUE='$EmployeeID'>";

	echo '<CENTER><TABLE>';

//	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Vendor') . ":</TD><TD><input type='Text' name='vendor' value='" . $myrow['vendor'] . "' SIZE=42 MAXLENGTH=40></TD></TR>";	

 

 //  if($mvendor==''){

   if($msection<>''){

	  if ($myrow09[0] <> 'ADMIN'){

			echo "<TR><TD hidden width=200 height=20><div align='right'><b>Vendor </TD><TD hidden><SELECT  style='text-align:center; id='vendor' name='vendor'>"; 

	  }else{

			echo "<TR><TD width=200 height=20><div align='right'><b>Vendor </TD><TD><SELECT  style='text-align:center; id='vendor' name='vendor'>"; 

   // echo'<TR><TD  width=200 height=20><div align="right"><b>Vendor </TD><TD width=200 height=20><input type="text" name="mvendor" id="mvendor" value="'.$mvendor.'"  autocomplete="off"></b></TD></TD>'; 

    //echo'<TR><TD  width=200 height=20><div align="right"><b>Section </TD><TD width=200 height=20><input type="text" name="msection" id="msection" value="'.$msection.'"  autocomplete="off"></b></TD></TD>'; 

    //echo'<TR><TD  width=200 height=20><div align="right"><b>ST Date </TD><TD width=200 height=20><input type="text" name="mstdate" id="mstdate" value="'.$mstdate.'"  autocomplete="off"></b></TD></TD>'; 

	 	  

	  }	  



			if ($myrow[0] <> ''){

				echo '<OPTION SELECTED VALUE="'.$myrow[0].'">'.$myrow[0];

			} 

 

            $sql0 = 'SELECT name from clmsvendormaster';

			$result0 = DB_query($sql0, $db);

	        echo "<OPTION   VALUE='  '>" ;

			while ($myrow0 = DB_fetch_array($result0)) {

				  echo '<OPTION VALUE="'.$myrow0[0].'">'.$myrow0[0];   



			} //end while loop 

 	        echo "<OPTION   VALUE=''>Any Contractor" ;



			 echo "</SELECT></TD></TR>";

			echo "<TR><TD width=200 height=20><div align='right'><b>Section </TD><TD><SELECT  style='text-align:center; id='section' name='section'>"; 

			if ($myrow[1] <> ''){

				echo '<OPTION SELECTED VALUE="'.$myrow[1].'">'.$myrow[1];

			} 

  			if (substr($_SESSION['UserID'],0,5)=='clms1'){

				//echo '<OPTION SELECTED VALUE="'.$myrow09[0].'">'.$myrow09[0];

				//echo '<OPTION SELECTED VALUE="'.$myrow09[0].'">'.$myrow09[0];

				$sql0 = 'SELECT distinct section from clmsusersection where trim(employeeid)="'.$_SESSION['UserID'].'"';

			}else {



            $sql0 = 'SELECT distinct section from clmsusersection';

			}

 			$result0 = DB_query($sql0, $db);

	        echo "<OPTION   VALUE='  '>" ;

			while ($myrow0 = DB_fetch_array($result0)) {

				  echo '<OPTION VALUE="'.$myrow0[0].'">'.$myrow0[0];   



			} //end while loop

  

			 echo "</SELECT></TD></TR>";

			

     echo'<TR><TD width=200 height=20> <div align="right"><b>Date From: </TD><TD width=200 height=20><input type="text" name="datepicker" id="datepicker" value="'.$myrow['stdate'].'"  autocomplete="off"></b></TD></TD>'; 

	}else{

/*

    echo'<TR><TD hidden width=200 height=20><input type="text" name="mvendor" id="mvendor" value="'.$mvendor.'"  autocomplete="off"></b></TD></TD>'; 

    echo'<TR><TD hidden width=200 height=20><input type="text" name="msection" id="msection" value="'.$msection.'"  autocomplete="off"></b></TD></TD>'; 

    echo'<TR><TD hidden width=200 height=20><input type="text" name="mstdate" id="mstdate" value="'.$mstdate.'"  autocomplete="off"></b></TD></TD>'; 



	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Vendor') . ":</TD><TD type='Text' name='vendor'  SIZE=42 MAXLENGTH=40>".$mvendor."</TD></TR>";	

	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Section') . ":</TD><TD type='Text' name='section'  SIZE=42 MAXLENGTH=40>".$msection."</TD></TR>";	

	echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Date From') . ":</TD><TD type='Text' name='datepicker'  SIZE=42 MAXLENGTH=40>".$mstdate."</TD></TR>";	

*/

  if ($myrow09[0] <> 'ADMIN'){

	echo "<TR><TD hidden width=200 height=20><div align='right'><b>Vendor </TD><TD hidden><SELECT  style='text-align:center; id='vendor' name='vendor'>"; 

  }else{

	echo "<TR><TD width=200 height=20><div align='right'><b>Vendor </TD><TD><SELECT  style='text-align:center; id='vendor' name='vendor'>"; 

	  

  }	  

	if ($myrow[0] <> ''){

				echo '<OPTION SELECTED VALUE="'.$myrow[0].'">'.$myrow[0];

			} 

 

            $sql0 = 'SELECT name from clmsvendormaster';

			$result0 = DB_query($sql0, $db);

	        echo "<OPTION   VALUE='  '>" ;

			while ($myrow0 = DB_fetch_array($result0)) {

				  echo '<OPTION VALUE="'.$myrow0[0].'">'.$myrow0[0];   



			} //end while loop 

 	        echo "<OPTION   VALUE=''>Any Contractor" ;



			 echo "</SELECT></TD></TR>";

			echo "<TR><TD width=200 height=20><div align='right'><b>Section </TD><TD><SELECT  style='text-align:center; id='section' name='section'>"; 

			if ($myrow[1] <> ''){

				echo '<OPTION SELECTED VALUE="'.$myrow[1].'">'.$myrow[1];

			} 

 			//if ($myrow09[0] <> 'ADMIN'){

 			if (substr($_SESSION['UserID'],0,5)=='clms1'){

				//echo '<OPTION SELECTED VALUE="'.$myrow09[0].'">'.$myrow09[0];

				$sql0 = 'SELECT distinct section from clmsusersection where trim(employeeid)="'.$_SESSION['UserID'].'"';

			}else{ 

 

            //$sql0 = 'SELECT area from clmssectionmaster';

            $sql0 = 'SELECT distinct section from clmsusersection';

			}

			$result0 = DB_query($sql0, $db);

	        echo "<OPTION   VALUE='  '>" ;

			while ($myrow0 = DB_fetch_array($result0)) {

				  echo '<OPTION VALUE="'.$myrow0[0].'">'.$myrow0[0];   



			} //end while loop

  

			 echo "</SELECT></TD></TR>";

			

     echo'<TR><TD width=200 height=20> <div align="right"><b>Date From: </TD><TD width=200 height=20><input type="text" name="datepicker" id="datepicker" value="'.$myrow['stdate'].'"  autocomplete="off"></b></TD></TD>'; 

	

		

	}	

    echo'<TR><TD hidden width=200 height=20><input type="text" name="mvendor" id="mvendor" value="'.$mvendor.'"  autocomplete="off"></b></TD></TD>'; 

    echo'<TR><TD hidden width=200 height=20><input type="text" name="msection" id="msection" value="'.$msection.'"  autocomplete="off"></b></TD></TD>'; 

    echo'<TR><TD hidden width=200 height=20><input type="text" name="mstdate" id="mstdate" value="'.$mstdate.'"  autocomplete="off"></b></TD></TD>'; 





	 echo'<TR><TD width=200 height=20> <div align="right"><b>Date To: </TD><TD width=200 height=20><input type="text" name="datepicker1" id="datepicker1" value="'.$myrow['endate'].'" autocomplete="off"" /></b></TD></TR>'; 

	 echo '<TR><TD width=200 height=20><div align="right"><b>' . _('A Shift') . ":</TD><TD><input type='Text' name='ashift' value='" . $myrow['ashift'] . "' SIZE=20 MAXLENGTH=20></TD></TR>";	

	 echo '<TR><TD width=200 height=20><div align="right"><b>' . _('B Shift') . ":</TD><TD><input type='Text' name='bshift' value='" . $myrow['bshift'] . "' SIZE=20 MAXLENGTH=20></TD></TR>";	

	 echo '<TR><TD width=200 height=20><div align="right"><b>' . _('C Shift') . ":</TD><TD><input type='Text' name='cshift' value='" . $myrow['cshift'] . "' SIZE=20 MAXLENGTH=20></TD></TR>";	

	 echo '<TR><TD width=200 height=20><div align="right"><b>' . _('Remarks') . ":</TD><TD><input type='Text' name='urgent' value='" . $myrow['urgent'] . "' SIZE=20 MAXLENGTH=20></TD></TR>";	



 

    echo '</table>';

	echo"<P><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Update Engagement') . "'>";

	echo'</FORM>';

?>