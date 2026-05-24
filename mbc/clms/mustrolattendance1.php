<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include('includes/sess.inc');
//include('PeriodSetting.php');
$title = _('Mustrol View');
include('includes/headerclms.inc');
//include('mfgheader.php');    
// Updates uploaded data
//include('includes/header.inc');

//include('mfgheader.php');

//include('mfgheader.php');

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
  </head>




<?php
		//echo "<div style='display: block; margin-left:7%;margin-right:8%;width: 86%; background-color: white; '>";	
//
//echo '<a href="mfgaddslnoforcompundincodemaster.php"><h5>Sl No Adding in  RM Consumption Report   </h5>';
//echo "<center><div style='display: block; width: 1066px;background-color: white; '>";	
echo "<center><div>";	
		//echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","mustrola");>Export to XLS</button></center>';	
$cd=date('d-m-Y',strtotime("-1 days"));
$cdd=date('d-m-Y',strtotime("-1 days"));

 	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "" . SID . "'>";
	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";
	echo '<CENTER><TABLE>';
        echo '<br>'; 
        echo ' <TR><TD width=200 height=20> <b>Date : </TD><TD width=200 height=20><input type="text1" name="datepicker" id="datepicker" value="'.$cd.'"></b></TD> ';
      //  echo ' <TD width=200 height=20> <b>Period To : </TD><TD width=200 height=20><input type="text1" name="datepicker1" id="datepicker1"></b></TD> ';
/*	    
		echo " <TD width=200 height=20> <b>Plant : </TD>
	    <TD><SELECT id = 'plant'  NAME='plant'>"; 
    	DB_data_seek($result, 0);
    	$sql = 'SELECT distinct column14 FROM catlmfgequivalentmaster where column14 !="" order by column14';
    	$result = DB_query($sql, $db);
        echo "<OPTION   VALUE='  '>" ;
    	while ($myrow = DB_fetch_array($result)) 
	    {
        echo '<OPTION VALUE=' . $myrow['column14'] . '>' . $myrow['column14'];
	     } 
	    echo'</TD>';
    */
		echo '</TR>';

	echo "</TR></TABLE><br><p><right><INPUT TYPE='Submit' NAME='submit1' VALUE='" . _('Show') . "'>";
         	

	echo '</FORM>';
  
   
if (isset($_POST['submit1'])) {
	
include('leaveupdateinmustrol.php');
	
//*****	
$Sdate=$_POST['datepicker'];
$Edate=$_POST['datepicker']; 
$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);



//************** 
$mmonth=$mmonth1;;	
$mm=substr($Sdate,3,2);
$my=substr($Sdate,8,2);
$ed=substr($Edate,0,2);
$d=cal_days_in_month(CAL_GREGORIAN,$mm,'20'.$my);
$ed=$d;
$ctable1='clmsmanpower'.$mmonth;
$Sdate=date('Y-m-d',strtotime($Sdate));
$Edate=date('Y-m-d',strtotime($Edate));
 $yyy= date('F', strtotime($Sdate));
	
 	//	echo "<div style='display: block;margin-left: 20px; height: 400px;width: 1300px; background-color: white;overflow-y: scroll; '>";	
 // echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("pruebatabla","Mprep01");>Export to XLS</button></center>';	

$mheading='<H3>Production Mustrol for the month of '.$yyy.' '. substr($mmonth,2,2).'</h3>';
echo $mheading; 
 //echo '<left><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("pruebatabla","mustrollany");>Export to XLS</button></left>';	

 	$sql="create temporary table tempt4 select employeeid,dt,shift,'xxx' as'ws',00 as'dayswkd',00 as 'offdays',00 as'ldays',00 as'abs' from clmsshiftroster".$mmonth1." order by employeeid,dt";   
//echo $sql; 
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="update tempt4 a, clmsmanpower".$mmonth1." b set a.ws=b.shift where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.type)='Duty'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="update tempt4  set ws=shift where ws='xxx' and shift='O'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="update tempt4 a, tempcl3 b set a.ws=b.ws where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.ws)<>'xxx' and trim(a.ws)='xxx'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 
 	$sql="update tempt4  set ws='' where ws='xxx'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="delete from tempt4 where STR_TO_DATE(trim(dt),'%d-%m-%Y') >  STR_TO_DATE(trim('".$cdd."'),'%d-%m-%Y') ";   
//echo $sql;
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
//	where STR_TO_DATE(dt,'%d-%m-%Y')  between str_to_date('".$Sdate."','%d-%m-%Y') and str_to_date('".$Edate."','%d-%m-%Y')
$sql01="create temporary table tempt select Employeeid,'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy' as'Name','yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy' as'Section'";
$sql00="select distinct dt from tempt4 where employeeid='2182' order by dt";
		$result00 = DB_query($sql00,$db);
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
//echo $sql; 
 $sql="update tempt a, tempt5 b  set a.dayswkd=b.dayswkd,a.offdays=b.offdays,a.ldays=b.ldays,a.abs=b.abs  where a.employeeid=b.employeeid ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
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
$md="select sum(dayswkd)  from tempt where trim(Name) <> ''" ;
 		$result = DB_query($md,$db);
$myrow1 = DB_fetch_row($result);
echo"Total Days wkd=".$myrow1[0];
 
$md="select * from tempt where trim(Name) <> ''" ;
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
 		$result = DB_query($md,$db);

		$column_count = mysql_num_fields($result);
 
 $md="select * from tempt where trim(Name) <> '' order by employeeid";
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
echo '<left><td><td><a href="gatemanpowerentry.php">' . _('Back to Data Entry Form') . '</a><BR></left>';
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","mustrola");>Export to XLS</button></center>';	

echo'<center><table Border="1" id="tbl1" width ="25%"  cellspacing="0" cellpaddin="0">';
//echo'<tr><td colspan='.$column_count.'>';
//echo'<center><H3>Employee Shift Roster for the Period from '.$msdt.' To '.$medt.'  </h3><center></td></tr>';
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

?>


