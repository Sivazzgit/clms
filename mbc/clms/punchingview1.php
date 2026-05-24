<?php
/* $Revision: 1.0 $ */

//include('punchingreportupdation.php');
$PageSecurity = 10;
include('includes/sess.inc');
$title = _('Attendance Punching Report');

include('includes/headerkancor.inc');

/*
include('punchingreportupdation.php');
include('punchingreportupdationleave.php');
include('punchingreportupdationwoff.php');
include('punchingreportupdationholiday.php');

 include('punchingreportupdationshiftschedule.php');
*/
//echo '<br><left><A  HREF="attendancepaidwithoutpunchingperiodwise.php" >Attendance Paid with outPunching Any Month </A>';		 	

		$sql = 'select periodfrom from payrollcontrol  order by recordid desc limit 3';
	$result = DB_query($sql, $db);
	while ($myrow = DB_fetch_array($result)) {
		$msdt= trim($myrow[0]);
		//echo"<br>From".$myrow[4]. " To ".$myrow[5].' Mmonth'.$myrow[6];	
    }
$msdt="01-10-2022";
$mm=substr($msdt,3,2);
$yyr=substr($msdt,6,4);
$numberofdaysinamonth2 = cal_days_in_month(CAL_GREGORIAN, $mm, $yyr); // 31
//$medt = date('d-m-Y',strtotime($msdt) + (24*3600*($numberofdaysinamonth2-3)));
$medt = date('d-m-Y',strtotime($msdt) + (24*3600*($numberofdaysinamonth2-1)));
//$medt = date('28-m-Y',strtotime($medt)) ;
//echo $medt;
//echo "<a href='updation.php'>Refresh Data(Please wait for 5 minutes)</a>";
//echo '<br>';
if ($_SESSION['UserID'] <>'demo'){



if (isset($_POST['submit'])) {
echo'<INPUT Type="button" VALUE="Back" onClick="history.go(-2);return true;">';



$EmployeeID1=$_POST['empno'];
$Sdate=$_POST['stdate'];
$Edate=$_POST['endate'];
$smonth=substr($_POST['stdate'],3,2).substr($_POST['stdate'],8,2);
$emonth=substr($_POST['endate'],3,2).substr($_POST['endate'],8,2);
//echo "Starting month =".$smonth."Ending month =".$emonth;
$date1 = new DateTime($Sdate);
$date2 = new DateTime($Edate);

$diff = $date2->diff($date1)->format("%a");
//$diff=$diff+1;

//echo "Difference between Dates ".$diff;
//echo date('Y-m-d', strtotime("+30 days"));

$mm=substr($Sdate,3,2);
$yyr=substr($Sdate,6,4);
$numberofdaysinamonth = cal_days_in_month(CAL_GREGORIAN, $mm, $yyr); // 31
if($diff >= $numberofdaysinamonth ){
    $numberofdaysinamonth=$numberofdaysinamonth;
}
$numberofdaysinamonth=$diff;

//echo "No of Days in the month of the starting Date ".$numberofdaysinamonth;

//$Edate = date('d-m-Y',strtotime($Sdate) + (24*3600*$numberofdaysinamonth));
//echo "New date ".$Edate;
//echo"Starting and ending date";
$Sdate=date('Y-m-d',strtotime($Sdate));
$Edate=date('Y-m-d',strtotime($Edate));
//echo $Sdate;
//echo $Edate;
//echo dayname($sdate);


//echo"Employee ID ";
echo'&nbsp;&nbsp;';
echo'&nbsp;&nbsp;';
echo'&nbsp;&nbsp;';
echo 'Clock No ';
echo'&nbsp;&nbsp;';
echo'&nbsp;&nbsp;';
echo'&nbsp;&nbsp;';
echo $EmployeeID1;
$kemployeeid=$EmployeeID1;
echo'&nbsp;&nbsp;';
echo'&nbsp;&nbsp;';
echo'&nbsp;&nbsp;';
          $mtr=substr($EmployeeID1,0,1); 
     //     echo'tttt'.substr($EmployeeID1,0,1);  
//         echo'tttt'.$EmployeeID1);  
		if($mtr<>''){
     		 $sql10="select lastname,position from prlemployeemaster where employeeid='".$kemployeeid."'";
		}
 	  if($EmployeeID1>2000){
		$sql10="select lastname,position from prlemployeemasterstaff where employeeid='".$EmployeeID1."'";	
		}
		if($mtr=='T'){
		$sql10="select name from dpay1116 where employeeid='".$EmployeeID1."'";	
		}
$_POST['LastName']=''; 
		$ErrMsg = _('The employee') . ' ' . $_POST['LastName'] . ' ' . _('could not be added because');
//	echo $sql10;
    	$DbgMsg = _('The SQL that was used to insert the employee but failed was 1');
			$result10 = DB_query($sql10, $db, $ErrMsg, $DbgMsg);
		while ($myrow10 = DB_fetch_row($result10)) {
		
   $mname=$myrow10[0];
   $mpos=$myrow10[1];

   }
}
//echo $kemployeeid;
//echo $mname;
//$msdt='01-11-2020';
//$medt='30-11-2020';
$msdt=date("01-m-Y");

$mm=substr($msdt,3,2);
$yyr=substr($msdt,6,4);
$numberofdaysinamonth2 = cal_days_in_month(CAL_GREGORIAN, $mm, $yyr); // 31
//$medt = date('d-m-Y',strtotime($msdt) + (24*3600*($numberofdaysinamonth2-3)));
$medt = date('d-m-Y',strtotime($msdt) + (24*3600*($numberofdaysinamonth2-1)));


echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";
?>
  

<html>
  <head>
    <!-- Load jQuery from Google's CDN -->
    <!-- Load jQuery UI CSS  -->
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="script.js"></script>
    
    <link rel="stylesheet" href="runnable.css" />
   <script type="text/javascript"> 
    function ddate{
      return "01/02/2016";
    }
 </script>

  </head>

<CENTER><TABLE border="1" style="background-color:1#F0E68C">
 <TR><TD><align=right><b>Employee No</b></TD><TD><INPUT TYPE='text' NAME='empno'value="" ></TD></TR> 
 </table>
<CENTER><TABLE border="1" style="background-color:1#F0E68C">

 <TR><TD width=200 height=20> <b>Date From  : </TD><TD width=200 height=20><input type="text" name="stdate"value=<?php echo $msdt; ?> id="datepicker"></b></TD>
 <TD width=200 height=20> <b>Date To  : </TD><TD width=200 height=20><input type="text" name="endate"value=<?php echo $medt; ?> id="datepicker1"></b></TD></TR>
 

</table>
</div>
<?php

echo "<p><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Submit ') . "'>";


 echo'</FORM>';


}
if (isset($_POST['submit'])) {
//echo 'Start Date'.substr(trim($_POST['stdate']),3,2).substr(trim($_POST['stdate']),6,2); 	
//$mmonth1='1020';	
//**************
$mmonth1=substr(trim($_POST['stdate']),3,2).substr(trim($_POST['stdate']),6,2);
include('punchingupdates.php');
	
//***********	
//echo"rrrr".$_POST['empno'] ; 

//echo '<br><left><A  HREF="attendancepaidwithoutpunching.php?emon='.$emonth.'" >Attendance Paid with outPunching </A>';		 	
//	$sql = "SELECT recordis,employeeid,date,time,in_out,terminal,DAYNAME(STR_TO_DATE(date,'%d-%m-%Y')) as'wkday' FROM punching 	where employeeid ='".$EmployeeID1."'";
	$sql = "SELECT column0,column4,column5,column2,column3,dt,time0 from punching 	where column0 ='".$EmployeeID1."'";
if(trim($_POST['empno'])=='ALL'){ 
	$sql = "SELECT column0,column4,column5,column2,column3,dt,time0 from punching where column0>10 and column0<200 order by recordid desc  ";
}



			$ErrMsg = _('The employee') . ' ' . $_POST['LastName'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the employee but failed was8');
			$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
echo'<div>';
echo'<center><table border=0 width ="50%" cellspacing="0" cellpadding="0">';
echo'<tr>';
echo'<td>';
//	echo '<img src="'.$rootpath.'../../lms/photo/3010201.jpg" height=100 width=100 ALIGN="left">';
//	echo '<img src="'.$rootpath.'./lms/photo/'.$mimg .'" height=100 width=100 ALIGN="left">';
echo'</td>';
echo'</tr>';
echo'<table>';
echo'</center>';
echo'</div>';
echo'<div>';
	
//	echo '<br><A HREF="ExportToXlsKraList.php?" >Export to xls </A>';
 echo '<left><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","payregany");>Export to XLS</button></left>';	

$column_count=7;
//echo"<h3>Employee Attendance Punching Details ".$EmployeeID1."  Mr. ".$mname."</h3>";
//$mphto='<img src="'.$rootpath.'/photo/'.$mimg .'" height=100 width=100 ALIGN="left">';
//$mname='Raman Kutty'; 
$mheading=	"<h3>Employee Attendance Punching View ".$EmployeeID1."  Mr. ".$mname."<br>".$mpos."</h3>";
echo '<CENTER><table border=1 id="tbl1" width ="60%" cellspacing="0" cellpaddin="0">';
						echo "<th colspan='".($column_count)."'>";
 echo'<p><br>'.$mheading.'</p></th></tr>';

        echo "<tr>
		<td class='tableheader'>" . _('Employee ID ') . "</td>
		<td class='tableheader'>" . _('Date Eff ') . "</td>
		<td class='tableheader'>" . _('Time Eff ') . "</td>
		<td class='tableheader'>" . _('Shift ') . "</td>
		<td class='tableheader'>" . _('IN OUT ') . "</td>
		<td class='tableheader'>" . _('Date Act ') . "</td>
		<td class='tableheader'>" . _('Time Act ') . "</td>


	</tr>"; 
	$k=0; //row colour counter

		while ($myrow = DB_fetch_row($result)) {
        
		if ($k==1){
			echo "<TR BGCOLOR='#CCCCCC'>";
			$k=0;
		} else {
			echo "<TR BGCOLOR='#EEEEEE'>";
			$k++;
		}

//		echo '<TD width="7%">'  $myrow[13]  '</TD>';
if ($myrow[3]<=0){
      //  $myrow[3]='&nbsp;&nbsp;';
}
if ($myrow[4]<=0){
      //  $myrow[4]='&nbsp;&nbsp;';
}

if ($myrow[5]<=0){
      //  $myrow[5]='&nbsp;&nbsp;';
}

if ($myrow[6]<=0){
    //    $myrow[6]='&nbsp;&nbsp;';
}
if ($myrow[7]<=0){ 
       // $myrow[7]='&nbsp;&nbsp;';
}



if ($myrow[8]<=0){
      //  $myrow[8]='&nbsp;&nbsp;';
}
if ($myrow[9]==''){
   //     $myrow[9]='&nbsp;&nbsp;';
}
if ($myrow[10]==''){
   //     $myrow[10]='&nbsp;&nbsp;';
}
if ($myrow[11]==0){
   //     $myrow[11]='&nbsp;&nbsp;';

		}
if ($myrow[13]==0){
   //     $myrow[13]='&nbsp;&nbsp;';

		}


       if($myrow[13]=='PO'){
		          echo "<tr>";
		echo"<td>$myrow[1]</td>
		<td><CENTER>$myrow[2] </CENTER></td>
		<td><CENTER>  $myrow[3]</CENTER></td>
		<td><CENTER>$myrow[4] </CENTER></td>
		<td><CENTER>  $myrow[5]</CENTER></td>
		<td><CENTER>$myrow[6] </CENTER></td>
	</tr>";
		   
	   }else {
	
        echo "<tr>";
		echo"<td><CENTER>  $myrow[0]</CENTER></td>
		<td><CENTER>  $myrow[1]</CENTER></td>
		<td><CENTER>$myrow[2] </CENTER></td>
		<td><CENTER>  $myrow[3]</CENTER></td>
		<td><CENTER>$myrow[4] </CENTER></td>
		<td><CENTER>  $myrow[5]</CENTER></td>
		<td><CENTER>$myrow[6] </CENTER></td>
	</tr>";
	   }

	} //END WHILE LIST LOOP

//end of ifs and buts!
}

//include('includes/footer.inc');
?>
