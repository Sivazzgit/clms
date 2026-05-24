<?php
/* $Revision: 1.0 $ */

//include('punchingreportupdation.php');
$PageSecurity = 10;
include('includes/sess.inc');
$title = _('No Punching Report');

include('includes/headerclms.inc');
//include('includes/headerkancormanpowerentry.inc'); 

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
//$msdt="01-10-2020";
 $msdt=date("01-m-Y");

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
//echo'<INPUT Type="button" VALUE="Back" onClick="history.go(-2);return true;">';



$EmployeeID1=$_POST['empno'];
if($EmployeeID1==''){
	$EmployeeID1=$_POST['empn'];
}

$Sdate=$_POST['stdate'];
$Edate=$_POST['endate'];
$smonth=substr($_POST['stdate'],3,2).substr($_POST['stdate'],8,2);
$emonth=substr($_POST['endate'],3,2).substr($_POST['endate'],8,2);
//echo "Starting month =".$smonth."Ending month =".$emonth;
$date1 = new DateTime($Sdate);
$date2 = new DateTime($Edate);
$Sdatek0=$Sdate;
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
     		 $sql10="select lastname,position from clmsemployeemaster where employeeid='".$kemployeeid."'";
		}
 	  if($EmployeeID1>90000){
		$sql10="select lastname,position from clmsemployeemasterstaff where employeeid='".$EmployeeID1."'";	
		}
		if($mtr=='T'){
		$sql10="select name from dpay1116 where employeeid='".$EmployeeID1."'";	
		}
 
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
echo $mname;

			$sql = 'create temporary table tmp SELECT employeeid,lastname FROM clmsemployeemaster where employeeid>70000 and employeeid<80000';
			$result = DB_query($sql, $db);

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
<!-----
<CENTER><TABLE border="1" style="background-color:1#F0E68C">
 <TR><TD><align=right><b>Employee No</b></TD><TD><INPUT TYPE='text' NAME='empno'value="" ></TD></TR> 
 </table>
 ---->
<CENTER><TABLE border="1" style="background-color:1#F0E68C">

  <TR><TD><align=right><b>Employee No</b></TD><TD><INPUT TYPE='text' NAME='empno' ></TD> 
 <?php
  		   echo "<TD><SELECT  style='text-align:center; id='prdd' name='empn'>"; 
			if ($myrow03[4] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[4].'">'.$myrow03[4];
			} 

		//	$sql = 'SELECT employeeid,lastname FROM prlemployeemaster where orgunit="CATL" and active=0';
		     $sql = 'SELECT employeeid,lastname FROM tmp order by lastname';
			$result = DB_query($sql, $db);
		 
						  echo "<OPTION   VALUE='  '>" ;

			while ($myrow = DB_fetch_array($result)) {

				  echo '<OPTION VALUE="'.$myrow[0].'">' . $myrow[1]."    ".$myrow[0];
               
			} //end while loop
?>
 <TR><TD width=200 height=20> <b>Date From  : </TD><TD width=200 height=20><input type="text" name="stdate"value=<?php echo $msdt; ?> id="datepicker"></b></TD>

 <TD width=200 height=20> <b>Date To  : </TD><TD width=200 height=20><input type="text" name="endate"value=<?php echo $medt; ?> id="datepicker1"></b></TD></TR>
 

</table>
</div>
<?php

echo "<p><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Submit ') . "'>";


 echo'</FORM>';



if (isset($_POST['submit'])) {
//echo '<br><left><A  HREF="attendancepaidwithoutpunching.php?emon='.$emonth.'" >Attendance Paid with outPunching </A>';		 	
	
echo'<div>';
echo'<center><table border=0 width ="50%" cellspacing="0" cellpadding="0">';
echo'<tr>';
echo'<td>';
//	echo '<img src="'.$rootpath.'../../lms/photo/301020.jpg" height=100 width=100 ALIGN="left">';
//	echo '<img src="'.$rootpath.'./lms/photo/'.$mimg .'" height=100 width=100 ALIGN="left">';
echo'</td>';
echo'</tr>';
echo'<table>';
echo'</center>';
echo'</div>';
$sql = "create temporary table tpatt select employeeid, dt,shift as'sshift','       ' as'inpunch','       ' as'outpunch',0000.00 as'inp0',0000.000 as'outp0' from clmsshiftroster".$emonth." 
				where employeeid ='".$EmployeeID1."'"; 
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
//$mmonth1=substr(trim($_POST['stdate']),3,2).substr(trim($_POST['stdate']),6,2);
$mmonth1=$emonth;

	
//echo $sql;	
	
$sql = "create temporary table tpatt1 select *,'       ' as'inpunch','       ' as'outpunch',0000.00 as'phrs',0000.00 as'inp0',0000.00 as'outp0',00000.000 as'tphr',00000.00 as'tpmin'  from clmsmanpower".$emonth." limit 0" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "insert into tpatt1(employeeid,dt,sshift,inpunch,outpunch,inp0,outp0) select employeeid,dt,sshift,inpunch,outpunch,inp0,outp0 from tpatt" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

				
$sql = "create temporary table tpdutya1 select * from tpatt1" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
				
$sql = "create temporary table tpot1 select * from tpatt1" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
				
				
$sql="update tpatt1 a,clmsmanpower".$emonth." b set
		a.shift = b.shift, 
		a.workcenter = b.workcenter,
		a.position = b.position,
		a.category = b.category,
		a.type = b.type,
		a.hrs = b.hrs,
        a.section= b.section, 
		a.grade= b.grade,
		a.actgrade= b.actgrade,
		a.spice= b.spice,
		a.inpunch= b.timein,
		a.outpunch= b.timeout,
		
		a.manning= b.manning
		where a.dt=b.dt and a.employeeid=b.employeeid and trim(b.type)='Duty'";

		$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
//echo'<br>'. $sql;
$sql="update tpdutya1 a,clmsmanpower".$emonth." b set
		a.shift = b.shift, 
		a.workcenter = b.workcenter,
		a.position = b.position,
		a.category = b.category,
		a.type = b.type,
		a.hrs = b.hrs,
        a.section= b.section, 
		a.grade= b.grade,
		a.actgrade= b.actgrade,
		a.spice= b.spice,
		a.manning= b.manning
		where a.dt=b.dt and a.employeeid=b.employeeid and trim(b.type)='DutyA'";

		$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "delete from tpdutya1 where type=''" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "insert into tpatt1 select * from tpdutya1" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
				
$sql="update tpot1 a,clmsmanpower".$emonth." b set
		a.shift = b.shift, 
		a.workcenter = b.workcenter,
		a.position = b.position,
		a.category = b.category,
		a.type = b.type,
		a.hrs = b.hrs,
        a.section= b.section, 
		a.grade= b.grade,
		a.actgrade= b.actgrade,
		a.spice= b.spice,
		a.manning= b.manning
		where a.dt=b.dt and a.employeeid=b.employeeid and trim(b.type)='OT'";

		$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "delete from tpot1 where type=''" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "insert into tpatt1 select * from tpot1" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
				

				
//$sql = "select  dt as'Date',DAYNAME(STR_TO_DATE(dt,'%d-%m-%Y')) as'wkday' ,sshift as'Sch Shift',inpunch,outpunch,
//Workcenter as'Shift Incharge',Shift,Type,HRS,Phrs,Section as 'Working Section',Grade as'Working Grade',Actgrade,Spice,Manning,inp0,outp0,tphr,tpmin from tpatt1 order by dt"; 

$sql = "select  dt as'Date',DAYNAME(STR_TO_DATE(dt,'%d-%m-%Y')) as'wkday' ,sshift as'Sch Shift',inpunch,outpunch,
Workcenter as'Shift Incharge',Shift,Type,HRS,Phrs,Section as 'Working Section',Grade as'Working Grade',Actgrade,Spice,Manning from tpatt1 order by dt"; 


$sql = "create temporary table tnpunch1 select * from clmsmanpower".$emonth ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "update tnpunch1 set spice=0" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "update tnpunch1 set spice=1 where shift='A' and (timein=0 or timeout=0)" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "update tnpunch1 set spice=1 where shift='B' and timein=0" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "update tnpunch1 set spice=1 where shift='C' and timeout=0" ;
				$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
//echo $sql;
$sql = "select  Employeeid,Employeename,dt as'Date',DAYNAME(STR_TO_DATE(dt,'%d-%m-%Y')) as'wkday' ,timein,timeout,
Workcenter as'Shift Incharge',Shift,Type,HRS,Section as 'Working Section' from tnpunch1 where spice=1 and trim(workcenter)='".$_SESSION['UserID']."' order by employeeid"; 

if($_SESSION['UserID']=='clmsadmin'){
$sql = "select  Employeeid,Employeename,dt as'Date',DAYNAME(STR_TO_DATE(dt,'%d-%m-%Y')) as'wkday' ,timein,timeout,
Workcenter as'Shift Incharge',Shift,Type,HRS,Section as 'Working Section' from tnpunch1 where spice=1  order by employeeid"; 
	
}
		
    $DbgMsg = _('The SQL that was used to insert the employee but failed was');
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $column_count = mysql_num_fields($result03);

//	echo "<FORM METHOD='post' id='f1' ACTION='mfgproductiondataupdation1.php'";
$mheading="Manpower Data Updated with out Punching";
   // echo '<br><br><A HREF="exporttoxlspayregister.php?">Export to xls</A>';
    echo '<div>';
//echo' <td><td><left><INPUT type="image" name="submit"  style="width:30px;height:30px;" src="../rcs/includes/back_button.png" VALUE="Back" onClick="history.go(-4);return true;"><br><p style="font-size:20;">&nbsp;&nbsp;Back to Data Entry form</p></td>';
//echo' <td><td><left><INPUT type="image" name="submit"  style="width:30px;height:30px;" src="../rcs/includes/back_button.png" VALUE="manpowerentry.php"><br><p style="font-size:20;">&nbsp;&nbsp;Back to Data Entry form</p></td>';
 echo '<td><td><a href="gatemanpowerentry.php">' . _('Back to Data Entry Form') . '</a><BR>';

 //  echo'<H3>'.$mheading.'</h3>';
 //   echo 'Prode Code'. $KraType  .'  Name '. $mprodname;
    echo'<center><table id="tbl1" Border="1" width ="100%"  cellspacing="0" cellpaddin="0">'; 

    print("<TR>");
	echo "<th colspan='".($column_count)."'>";
    echo'<p>'.$mheading.'</p></th></tr><tr>';

    for($column_num = 0; $column_num < $column_count; $column_num++) 
    {
        $field_name = mysql_field_name($result03, $column_num);
        print("<TH>$field_name</TH>\n");
    }
    print("</TR>");     
    while ($myrow03 = DB_fetch_row($result03))  
    {
      $mcntt=  $myrow03[1];       
	   //echo $myrow1[0];  
        //echo '<tr>';
	if( $myrow03[8]==0){
		$myrow03[8]='';
	}
        print("<TR>"); 
        for($column_num = 0; $column_num < $column_count; $column_num++) 
        {
            //print("<TD>$myrow03[$column_num]</TD>\n"); 
            if ($myrow03[$column_num]<=0)
            {
//                 print ("<TD>&nbsp;&nbsp;</TD>\n");
               print("<TD><center>$myrow03[$column_num]</center></TD>\n");

            }
            else
            { 
               if ($myrow03[1]==''){
		   print("<TD><b><center>$myrow03[$column_num]</b></center></TD>\n");
               }else { 
		   print("<TD><center>$myrow03[$column_num]</center></TD>\n");
               } 
            }
        }
	 	
		//  echo '<TD><A HREF="'. $rootpath . '/recorddelete.php?' . SID . '&LeaveID=' . $myrow03[0] . '&mmn='.$mmonth1.'">' . _('Delete Record') . '</A></TD>';
 	
 
        echo'</tr>';  
	}	
	 
    echo'</table>';
    echo '</div>';
   
	}
		
echo'</div';

}

//include('includes/footer.inc');
?>
