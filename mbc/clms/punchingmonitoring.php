<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

include('includes/sess.inc');
//include('PeriodSetting.php');
$title = _('Punching Monitoring');
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
//$Sdate=date('Y-m-d',strtotime($Sdate));
//$Edate=date('Y-m-d',strtotime($Edate));
// $yyy= date('F', strtotime($Sdate));
	
//echo "Date Selected". $Sdate;

include('pdataplottingrevised.php');
 	$sql01 = "create temporary table tpmoni1 select dt,employeeid,'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' as'name'
	,'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' as'dept',shift as'sshift',
	00.00 as'inp',00.00 as'outp','xxxxxxxxxxxx' as'Vendor','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' as'wsection'
	 from clmsshiftroster".$mmonth." where dt='".$Sdate."'";
//echo $sql01;
	$result01 = DB_query($sql01,$db);


$md="update tpmoni1  a, clmsemployeemaster b set a.name=b.lastname,a.dept=b.dept where trim(a.employeeid)=trim(b.employeeid) and b.active=0" ;
 		$result = DB_query($md,$db);
    $md="ALTER TABLE tpmoni1 CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
	$result02 = DB_query($md,$db);

$md="update tpmoni1  a, tpplot b set a.inp=b.inp, a.outp=b.outp  where trim(a.employeeid)=trim(b.column0) and trim(a.dt)=trim(b.dt) and a.dt='".$Sdate."'";
 		$result = DB_query($md,$db);
 		
$md="create temporary table tcrk select * from clmsmanpower".$mmonth;
 		$result = DB_query($md,$db);
     $md="ALTER TABLE tcrk CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
	$result02 = DB_query($md,$db);
		
$md="update tpmoni1  a, tcrk  b set a.vendor=b.workcenter, a.wsection=b.section  where trim(a.employeeid)=trim(b.employeeid) and trim(a.dt)=trim(b.dt) and a.dt='".$Sdate."'";
 		$result = DB_query($md,$db);
$md="update tpmoni1 set vendor='' where vendor='xxxxxxxxxxxx'";
 		$result = DB_query($md,$db);
$md="update tpmoni1 set wsection='' where wsection='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'";
 		$result = DB_query($md,$db);
$md="delete from tpmoni1 where name='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'";
 		$result = DB_query($md,$db);

	


$md="create temporary table tpunched select column0 as'employeeid',dt,inp,'xxxxxxxxxxxxxxxxxxx' as'notseenn' from tpplot where dt='".$Sdate."'";
 		$result = DB_query($md,$db);

    $md="ALTER TABLE tpunched CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
	$result02 = DB_query($md,$db);

$md="update tpunched a, tpmoni1 b set a.notseenn=b.employeeid where trim(a.employeeid)=trim(b.employeeid) and trim(a.dt)=trim(b.dt) and  trim(a.dt)='".$Sdate."'";
 		$result = DB_query($md,$db);



$md="create temporary table tpmonisum select *, 000 as'scheduled',000 as'punched',000 as 'updated',000 as'sectionmapped' from tpmoni1";
 		$result = DB_query($md,$db);
    $md="ALTER TABLE tpmonisum CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
	$result02 = DB_query($md,$db);

$md="update tpmonisum  set scheduled=1";
 		$result = DB_query($md,$db);
$md="update tpmonisum  set punched=1 where inp>0";
 		$result = DB_query($md,$db);
$md="update tpmonisum  set updated=1 where trim(vendor)<>''";
 		$result = DB_query($md,$db);
$md="update tpmonisum  set sectionmapped=1 where trim(wsection)<>''";
 		$result = DB_query($md,$db);
$md="create temporary table tpmonisum1  select dt,dept, sum(scheduled)as 'scheduled',sum(punched) as'punched',sum(updated) as'updated'
,sum(sectionmapped) as'sectionmapped' from tpmonisum where dt='".$Sdate."' group by dept with rollup";
 		$result = DB_query($md,$db);

//Summary  
$md="select * from tpmonisum1 " ;
//$md="select a.lastname as'Employee Name', b.* from clmsprlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
 		$result = DB_query($md,$db);

		$column_count = mysql_num_fields($result);
 
 $md="select dt as'Date',dept as'Vendor',Scheduled,Punched,Updated,Sectionmapped from tpmonisum1 "; 
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
echo '<left><td><td><a href="clms.php">' . _('Back to Home Page') . '</a><BR></left>';
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","tpmon1");>Export to XLS</button></center>';	

echo'<center><table Border="1" id="tbl1" width ="25%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo'<center><H3>Summary of Punching Monitoring  </h3><center></td></tr>';
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


//*************


//Punched but not seen in master 
$md="select Employeeid,inp as 'In Punch' from tpunched where notseenn ='xxxxxxxxxxxxxxxxxxx'" ;
//$md="select a.lastname as'Employee Name', b.* from clmsprlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
 		$result = DB_query($md,$db);

		$column_count = mysql_num_fields($result);
 
 $md="select Employeeid,inp as 'In Punch' from tpunched where notseenn='xxxxxxxxxxxxxxxxxxx'"; 
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
echo '<left><td><td><a href="clms.php">' . _('Back to Home Page') . '</a><BR></left>';
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl2","tnotseen");>Export to XLS</button></center>';	

echo'<center><table Border="1" id="tbl2" width ="25%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo'<center><H3>New Employee Punched on '.$Sdate.'   But Not Seen in Employee Master   </h3><center></td></tr>';
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


//*************
//Attendance marked with out IN punching***********************
$md="select * from tpmoni1  where trim(Name) <> '' and inp=0 and trim(vendor)<>''" ;
//$md="select a.lastname as'Employee Name', b.* from clmsprlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
 		$result = DB_query($md,$db);

		$column_count = mysql_num_fields($result);
 
 $md="select dt as'Date',Employeeid,Name,dept as'Vendor',sshift as'Sch. Shift',inp as'In Punch', outp as'Out Punch',Vendor as'Updated by',
 wsection as'Section Worked' from tpmoni1  where trim(Name) <> '' and inp=0 and trim(vendor)<>'' order by employeeid";
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
echo '<left><td><td><a href="clms.php">' . _('Back to Home Page') . '</a><BR></left>';
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl4","tplist");>Export to XLS</button></center>';	

echo'<center><table Border="1" id="tbl4" width ="25%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo'<center><H3>Employee Marked Attendance With out Punching   </h3><center></td></tr>';
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



//*Attendance marked with out IN punching Ends***********************


$md="select * from tpmoni1  where trim(Name) <> ''" ;
//$md="select a.lastname as'Employee Name', b.* from clmsprlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
 		$result = DB_query($md,$db);

		$column_count = mysql_num_fields($result);
 
 $md="select dt as'Date',Employeeid,Name,dept as'Vendor',sshift as'Sch. Shift',inp as'In Punch',outp as'Out Punch',Vendor as'Updated by',wsection as'Section Worked' from tpmoni1  where trim(Name) <> '' order by employeeid";
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
echo '<left><td><td><a href="clms.php">' . _('Back to Home Page') . '</a><BR></left>';
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl3","tplist");>Export to XLS</button></center>';	

echo'<center><table Border="1" id="tbl3" width ="25%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo'<center><H3>Employee Punching Details  </h3><center></td></tr>';
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
	