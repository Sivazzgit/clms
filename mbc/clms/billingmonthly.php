

    <script type="text/javascript">  
	function passval(app){
		
		var mapp = document.getElementById('app').options[document.getElementById('app').selectedIndex].text;
   alert("You entered: " +mapp);
	
	 }

	 function onapproval(url,id){
		
		var mapp = document.getElementById(id).value;
   		document.location=encodeURI(url+"&approve="+mapp);
	
	 }
	</script>	
<style type="text/css">
#circle {
 width: 180px;
 height: 180px;
 background: #abcdef;
 -moz-border-radius: 50%;
 -webkit-border-radius: 50%;
 border-radius: 50%;
 padding:15px;
}
</style>

<?php
$PageSecurity = 10;  


include('includes/sess.inc');

$title = _('Contract Labour Engagement Monthly Billing Details');
if(substr($_SESSION['UserID'],0,4)<>'clms' ){
	exit("Not allowed. Unauthorised User");
}

include('includes/headerclms.inc'); 

	if($dt  == '')
	{
	  $_POST['dt']  = date("d-m-Y");
      $dt=date("01-m-Y");
	  }

	   if (isset($_POST['submit1'])) 
	   {
	   		$Sdate=$_POST['datepicker'];
	   }
	   else
	   {
	   	 $Sdate=date("01-m-Y");
	   }
 echo'   <script src="script.js"></script>' ;
 
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
	$kkdept='';
}		 
$mvend=$kkdept;

 echo "User ID ".$_SESSION['UserID'];
//	 $Todate=date("26-m-Y");
//echo date("27-m-Y", strtotime("last day of previous month"));
$Sdate=date("27-m-Y", strtotime("last day of previous month"));
$curdt=date('d-m-Y',strtotime("-0 days"));
$mcd=substr($curdt,0,2);
if($mcd<9){
	$cd0=date('d-m-Y',strtotime("-60 days"));
	$cd1=date('d-m-Y',strtotime("-30 days"));
}else{
	$cd0=date('d-m-Y',strtotime("-30 days"));
	$cd1=date('d-m-Y',strtotime("-0 days"));
}	
$lastDateOfLastMonth =strtotime('last day of last month') ;

//$lastDay = date('27-m-Y', $lastDateOfLastMonth);
$lastDay = date('27-m-Y', trim($Sdate));
$lastDay = date("27-m-Y", strtotime($cd0));
$Todate=date("26-m-Y", strtotime($cd1));
$Sdate=$lastDay;

  echo'</div>';
	if(substr($_SESSION['UserID'],0,5)=='clms0' ){
//	echo '<center><a href="gatemanpowerentry.php">' . _('Back to Attendance Entry Form') . '</a><BR>';

	}	
// echo '<center><a href="intend.php">' . _('Intend Creation/Approval') . '</a><BR>';
	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "" . SID . "'>";
	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";
	echo '<CENTER><TABLE>';
        echo '<br>'; 
    echo ' <TR><TD align="right" width=200 height=20> <b>Date From : </TD><TD width=200 height=20><input type="text" name="datepicker" id="datepicker" value="'.$Sdate.'"></b></TD>
			   <TD align="right" width=200 height=20> <b>To : </TD><TD width=200 height=20><input type="text" name="datepicker1" id="datepicker1" value="'.$Todate.'"></b></TD>
	</TR> ';
	echo "<TR><TD width=200 height=20><div align='right'><b>Vendor </TD><TD><SELECT  style='text-align:center; id='vendor' name='vendor'>"; 
			if ($mvend <> ''){
				echo '<OPTION SELECTED VALUE="'.$mvend.'">'.$mvend;
			} 
 
		if($_SESSION['UserID']=='clmsadmin'){
        $sql0 = 'SELECT name from clmsvendormaster';
	    echo "<OPTION   VALUE=''>" ;
		}else{
	      $sql0 = 'SELECT name from clmsvendormaster where trim(name) ="'.$mvend.'"';
		
		}
			$result0 = DB_query($sql0, $db);
			while ($myrow0 = DB_fetch_array($result0)) {
				  echo '<OPTION VALUE="'.$myrow0[0].'">'.$myrow0[0];   

			} //end while loop 
 	  //      echo "<OPTION   VALUE=''>Any Contractor" ;

			 echo "</SELECT></TD>";
			echo "<TD width=200 height=20><div align='right'><b>Cost Center </TD><TD><SELECT  style='text-align:center; id='costcenter' name='costcenter'>"; 
			if ($myrow[1] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow[1].'">'.$myrow[1];
			} 
  			if (substr($_SESSION['UserID'],0,5)=='clms1'){
				//echo '<OPTION SELECTED VALUE="'.$myrow09[0].'">'.$myrow09[0];
				//echo '<OPTION SELECTED VALUE="'.$myrow09[0].'">'.$myrow09[0];
				$sql0 = 'SELECT distinct costcenter from clmssectionmaster ';
			}else {

            $sql0 = 'SELECT distinct costcenter from clmssectionmaster';
			}
 			$result0 = DB_query($sql0, $db);
	        echo "<OPTION   VALUE=''>" ;
			while ($myrow0 = DB_fetch_array($result0)) {
				  echo '<OPTION VALUE="'.$myrow0[0].'">'.$myrow0[0];   

			} //end while loop
  
			 echo "</SELECT></TD></TR>";
	
	echo "</TR></TABLE>";
echo'<div id="circle">';
    echo "<p><center><INPUT TYPE='Submit' NAME='submit1' VALUE='" . _('Show Mustrol') . "'>";
	echo "<br> <p><center><INPUT TYPE='Submit' NAME='submit2' VALUE='" . _('Date wise Summary') . "'>";
	echo "<p><center><INPUT TYPE='Submit' NAME='submit3' VALUE='" . _('Section Wise Summary') . "'>";
	echo "<p><center><INPUT TYPE='Submit' NAME='submit4' VALUE='" . _('Cost Center wise Summary') . "'>"; 
	echo "<p><center><INPUT TYPE='Submit' NAME='submit5' VALUE='" . _('Invoice') . "'>"; 
	echo'</div>';
//echo'<div id="circle"><br/><br/>Web Programming Courses:<br/>CoursesWeb.net</div>';
	echo '</FORM><br>'; 

 if (isset($_POST['submit2'])) {
  // echo "Ok Under  Development";
   include('Billingdatewisesummary.php');

 }
if (isset($_POST['submit3'])) {
  // echo "Ok Under  Development";
   include('Billingsectionwisesummary.php');

 }	
 
if (isset($_POST['submit4'])) {
  // echo "Ok Under  Development";
   include('Billingcostcenterwisesummary.php');

 } 
 if (isset($_POST['submit5'])) {
  // echo "Ok Under  Development";
   include('invoiceview.php');

 } 
 
  if (isset($_POST['submit1'])) {
// echo"XXXXXXXX";

include('leaveupdateinmustrol.php');

$cdd=date('d-m-Y',strtotime("-1 days"));

$Sdate=$_POST['datepicker'];
$Todate=$_POST['datepicker1'];
//$Edate=$_POST['datepicker1']; 
$Edate=$_POST['datepicker']; 
$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);
$mlmonth=substr($Sdate,3,2).substr($Sdate,8,2);
$mcmonth=substr($Todate,3,2).substr($Todate,8,2);
$mvendor=$_POST['vendor'];
$mcostcenter=$_POST['costcenter'];

$nodaysinstartingmonth = cal_days_in_month(CAL_GREGORIAN, substr($Sdate,3,2), substr($Sdate,8,4));  
$date1 = new DateTime($Sdate);
$date2 = new DateTime($Todate);
$diff0 = $date2->diff($date1)->format("%a");
$diff0=$diff0+1; 
//echo "No of days in last month".$nodaysinstartingmonth; 
//echo "No of days in between ".$diff0; 
if($diff0>$nodaysinstartingmonth){

exit("<br>One month Report only is available ");	
}
/*
echo "Last Month".$mlmonth;
echo "<br>Current Month".$mcmonth;
echo "<br>Vendor".$mvendor; 
echo "<br>Cost Center".$mcostcenter;
*/ 
$mfdate=date('Y-m-d',strtotime($Sdate));
$mtdate=date('Y-m-d',strtotime($Todate));

$mheading='<center><H3>'.$mvendor.' - Mustrol for the Period from  '.$Sdate.' To '. $Todate.'- Cost center '.$mcostcenter.'</h3></center>';
//echo $mheading; 
 //echo '<left><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("pruebatabla","mustrollany");>Export to XLS</button></left>';	
 //STR_TO_DATE(dt,'%d-%m-%Y')  between' ".$Sdate."' and '".$Edate."' 
// 	$sql="create temporary table tempt4 select employeeid,dt,shift,'xxx' as'ws',00 as'dayswkd',00 as 'offdays',00 as'ldays',00 as'abs' from clmsshiftroster".$mmonth1." order by employeeid,dt";   
 	$sql="create temporary table tempt4 select employeeid,dt,shift,'xxx' as'ws',00 as'dayswkd',00 as 'offdays',00 as'ldays',00 as'abs' from clmsshiftroster".$mlmonth."  where STR_TO_DATE(dt,'%d-%m-%Y')  between' ".$mfdate."' and '".$mtdate."' order by employeeid,dt";   
// echo $sql; 
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

    if($mlmonth<>$mcmonth){
	  $sql="insert into tempt4(employeeid,dt)  select employeeid,dt from clmsshiftroster".$mcmonth."  where STR_TO_DATE(dt,'%d-%m-%Y')  between' ".$mfdate."' and '".$mtdate."' order by employeeid,dt";   
	  $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	}
	if($mcostcenter==''){	
 	$sql="create temporary table tsec0 select distinct section from clmssectionmaster ";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
	}else{
 	$sql="create temporary table tsec0 select distinct section from clmssectionmaster where trim(costcenter) like '%".$mcostcenter."%'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
		
	}	
	
    if($mvendor==''){
 	$sql="create temporary table tmanp0 select a.* from clmsmanpower".$mlmonth." a, tsec0 b where trim(a.section) like  trim(b.section) ";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="create temporary table tmanp1 select a.* from clmsmanpower".$mcmonth." a, tsec0 b where a.section like CONCAT('%',b.section,'%') ";   
 	$sql="create temporary table tmanp1 select a.* from clmsmanpower".$mcmonth." a, tsec0 b where substr(a.section,1,10)=substr(b.section,1,10) ";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
//	column2 LIKE CONCAT('%', column1, '%');	
	}else{	
 	$sql="create temporary table tmanp0 select a.* from clmsmanpower".$mlmonth." a, tsec0 b where trim(a.section) like trim(b.section) and a.category like '".$mvendor."%'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="create temporary table tmanp1 select a.* from clmsmanpower".$mcmonth." a, tsec0 b where trim(a.section) like trim(b.section)  and a.category like '".$mvendor."%'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
	}
//	echo $sql;

	$sql="select section from tsec0 ";
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 
	$myrow = DB_fetch_row($result03);
//echo"Section Name=".$myrow[0]; 
	
	
	$sql="select count(*) from tmanp1 ";
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 
	$myrow = DB_fetch_row($result03);
//echo"Count in tmanp1=".$myrow[0]; 

 
	//$sql="update tempt4 a, clmsmanpower".$mmonth1." b set a.ws=b.shift where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.type)='Duty'";   
	$sql="update tempt4 a, tmanp0 b set a.ws=b.shift where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.type)='Duty'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	$sql="update tempt4 a, tmanp1 b set a.ws=b.shift where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.type)='Duty'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="update tempt4  set ws=shift where ws='xxx' and shift='O'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="update tempt4 a, tempcl3 b set a.ws=b.ws where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.ws)<>'xxx' and trim(a.ws)='xxx'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 
//echo $sql; 
 	$sql="update tempt4  set ws='' where ws='xxx'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="delete from tempt4 where STR_TO_DATE(trim(dt),'%d-%m-%Y') >  STR_TO_DATE(trim('".$cdd."'),'%d-%m-%Y') ";   
//echo $sql;
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
//	where STR_TO_DATE(dt,'%d-%m-%Y')  between str_to_date('".$Sdate."','%d-%m-%Y') and str_to_date('".$Edate."','%d-%m-%Y')
$sql01="create temporary table tempt select Employeeid,'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy' as'Name','yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy' as'Section'";
$sql00="select distinct dt from tempt4 where employeeid='2182' order by STR_TO_DATE(dt,'%d-%m-%Y')";
		$result00 = DB_query($sql00,$db); 
//echo $sql00;
		$sql0='';
		while ($myrow00 = DB_fetch_row($result00)) {
		$sql0=$sql0.',"xxx" as d'.substr($myrow00[0],0,2);	 
		}
//		echo $mdaynam;
$sql11=",00 as'dayswkd',00 as 'offdays',00 as 'ldays',00 as 'abs' ";
  $sql0=$sql0.$sql11.'  from clmsemployeemaster limit 0';
  $sql01=$sql01.$sql0;
  $result01 = DB_query($sql01,$db);
//echo $sql01; 

	$sql="insert into tempt(employeeid) select distinct employeeid from tempt4";   
//echo $sql;
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

 $sql02="select Employeeid,dt,ws from tempt4 ";
$result02 = DB_query($sql02,$db);
 		while ($myrow02 = DB_fetch_row($result02)) {
           $sql05="update tempt set d".substr($myrow02[1],0,2)."='".trim($myrow02[2])."' where trim(employeeid)='".trim($myrow02[0])."'";
           $result05 = DB_query($sql05,$db);
//echo $sql05;
	
	  
		}
 
 $sql="update tempt4 set dayswkd=1 where trim(ws)='A' or trim(ws)='B' or trim(ws)='C' or trim(ws)='G' "  ; 
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 
//echo $sql;
 $sql="update tempt4 set offdays=1 where ws='O' "; 
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 $sql="update tempt4 set ldays = 1 where trim(ws)='CL' or trim(ws)='PL' or trim(ws)='SL' " ;      
 //$sql="update tempt4 set ldays = 1" ;      
//echo $sql;
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 //echo $sql;
$sql="update tempt4 set abs=1 where ws='' "; 
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 $sql="create temporary table tempt5 select employeeid,sum(dayswkd) as'dayswkd', sum(offdays) as'offdays', sum(ldays) as'ldays', sum(abs) as'abs' from tempt4 group by employeeid "; 
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
//echo $sql ;
 $sql="update tempt a, tempt5 b  set a.dayswkd=b.dayswkd,a.offdays=b.offdays,a.ldays=b.ldays,a.abs=b.abs  where a.employeeid=b.employeeid ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

 $sql="select sum(dayswkd) from tempt  "; 
 $result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 $myrow = DB_fetch_row($result);
 $mtotdayswkd=$myrow[0];
 if($_SESSION['UserID']=='clms01'){
	$kkdept='CORPORATE STAFFING SOLUTIONS';
}
if($_SESSION['UserID']=='clms02'){ 
	$kkdept='VASS GROUP';
}
if($_SESSION['UserID']=='clms03'){
	$kkdept='KINGDOM SECURITY';
}
if($_SESSION['UserID']=='clmsadmin'){
	//$kkdept='ALL';
}		
if($_SESSION['UserID']=='clmsadmin'){
		
$md="update tempt a,  clmsemployeemaster b set a.Name=b.lastname where trim(a.employeeid)=trim(b.employeeid)";   
//echo $md;
	$result = DB_query($md,$db);
$md="update tempt a,  clmsemployeemaster b set a.Section=b.section where trim(a.employeeid)=trim(b.employeeid)";   
//echo $md;
	$result = DB_query($md,$db);
}else{

$md="update tempt a,  clmsemployeemaster b set a.Name=b.lastname where trim(a.employeeid)=trim(b.employeeid) and b.dept like '".$kkdept."%'";   
//echo $md;
	$result = DB_query($md,$db);
$md="update tempt a,  clmsemployeemaster b set a.Section=b.section where trim(a.employeeid)=trim(b.employeeid)";   
//echo $md;
	$result = DB_query($md,$db);

}	

$md="select * from tempt where trim(Name) <> ''" ;
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
 		$result = DB_query($md,$db);

		$column_count = mysql_num_fields($result);
 
 $md="select * from tempt where trim(Name) <> '' and dayswkd>0 order by employeeid";
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
        echo'<br>Total Manpower Engaged ='.$mtotdayswkd;
		echo '<br><left><td><td><a href="gatemanpowerentry.php">' . _('Back to Data Entry Form') . '</a><BR></left>';
		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","mustr");>Export to XLS</button></center>';	

echo'<div>';


echo'<center><table Border="1" id="tbl1" width ="100%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo $mheading.'</td></tr>';
		print("<TR>");
		for($column_num = 0; $column_num < $column_count; $column_num++) {
			$field_name = mysql_field_name($result, $column_num);
			print("<TH>$field_name</TH>");
		}
		print("</TR>");

		while ($myrow1 = DB_fetch_row($result)) {
//echo '<tr>';
print("<TR>");
		for($column_num = 0; $column_num < $column_count; $column_num++) {
			print("<TD>$myrow1[$column_num]</TD>\n");

}
echo'</tr>';
		}		

echo'</table></center>';
echo'</div>';


  }