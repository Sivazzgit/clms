<?php
$PageSecurity = 10;  
include('includes/sess.inc');
//$title = _('Shift Wise Labour Intend');
include('includes/headerclms.inc');

//include('leaveupdateinmustrol.php');

$cdd=date('d-m-Y',strtotime("-1 days"));

echo $_GET['vendor'];
//exit("Break-1");
$Sdate=$_GET['stdate'];
$Todate=$_GET['endate'];
//$Edate=$_POST['datepicker1']; 
$Edate=$_GET['stdate']; 
$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);
$mlmonth=substr($Sdate,3,2).substr($Sdate,8,2);
$mcmonth=substr($Todate,3,2).substr($Todate,8,2);
$mvendor=$_GET['vendor'];
$mcostcenter=$_GET['costcenter'];
$msection=$_GET['section'];

if(trim($mvendor) == 'ALL'){
   $mvendor='';
}

echo "VENDOR".$mvendor;
if($mcostcenter=='ALL'){
	$mcostcenter='';
}

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

//include('leaveupdateinmustrol.php');

/*
echo "Last Month".$mlmonth;
echo "<br>Current Month".$mcmonth;
echo "<br>Vendor".$mvendor; 
echo "<br>Cost Center".$mcostcenter;
*/ 
$mfdate=date('Y-m-d',strtotime($Sdate));
$mtdate=date('Y-m-d',strtotime($Todate));

//echo $mheading; 
 //echo '<left><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("pruebatabla","mustrollany");>Export to XLS</button></left>';	
 //STR_TO_DATE(dt,'%d-%m-%Y')  between' ".$Sdate."' and '".$Edate."' 
// 	$sql="create temporary table tempt4 select employeeid,dt,shift,'xxx' as'ws',00 as'dayswkd',00 as 'offdays',00 as'ldays',00 as'abs' from clmsshiftroster".$mmonth1." order by employeeid,dt";   
 	$sql="create temporary table tempt4 select employeeid,dt,shift,'xxx' as'ws',00 as'dayswkd',00 as 'offdays',00 as'ldays',00 as'abs'
    ,'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' as'section','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' as'costcenter',
	'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx	xxxxxxxxxxxx' as'vendor' ,000 as'ashifteng',000 as'bshifteng',000 as'cshifteng'
	,000 as'totaleng' 	from clmsshiftroster".$mlmonth."  where STR_TO_DATE(dt,'%d-%m-%Y')  between' ".$mfdate."' and '".$mtdate."' order by employeeid,dt";   
// echo $sql; 
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

    if($mlmonth<>$mcmonth){
	  $sql="insert into tempt4(employeeid,dt)  select employeeid,dt from clmsshiftroster".$mcmonth."  where STR_TO_DATE(dt,'%d-%m-%Y')  between' ".$mfdate."' and '".$mtdate."' order by employeeid,dt";   
	  $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	}
	if($mcostcenter==''){	
 	$sql="create temporary table tsec0 select distinct section from clmssectionmaster where trim(section )= '".$msection."' ";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
	}else{
 //	$sql="create temporary table tsec0 select distinct section from clmssectionmaster where trim(costcenter) like '%".$mcostcenter."%'";   
 	$sql="create temporary table tsec0 select distinct section from clmssectionmaster where trim(section) = '".$msection."'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
		
	}	
//	echo $sql;
    if($mvendor==''){
 	$sql="create temporary table tmanp0 select a.* from clmsmanpower".$mlmonth." a, tsec0 b where substr(a.section,1,10)=substr(b.section,1,10) ";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	//$sql="create temporary table tmanp1 select a.* from clmsmanpower".$mcmonth." a, tsec0 b where a.section like CONCAT('%',b.section,'%') ";   
 	$sql="create temporary table tmanp1 select a.* from clmsmanpower".$mcmonth." a, tsec0 b where substr(a.section,1,10)=substr(b.section,1,10) ";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
//	column2 LIKE CONCAT('%', column1, '%');	
	}else{	
 	$sql="create temporary table tmanp0 select a.* from clmsmanpower".$mlmonth." a, tsec0 b where substr(a.section,1,10)=substr(b.section,1,10) and a.category like '".trim($mvendor)."%'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="create temporary table tmanp1 select a.* from clmsmanpower".$mcmonth." a, tsec0 b where substr(a.section,1,10)=substr(b.section,1,10)  and a.category like '".trim($mvendor)."%'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	
	}
//echo $sql;  
	
	$sql="select section from tsec0 ";
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 
	$myrow = DB_fetch_row($result03);
//echo"Section Name=".$myrow[0]; 
	
	
	$sql="select count(*) from tmanp1 ";
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 
	$myrow = DB_fetch_row($result03);
//echo"Count in tmanp1=".$myrow[0]; 

 
	//$sql="update tempt4 a, clmsmanpower".$mmonth1." b set a.ws=b.shift where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.type)='Duty'";   
	$sql="update tempt4 a, tmanp0 b set a.ws=b.shift,a.section=b.section,a.vendor=b.category where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.type)='Duty'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	$sql="update tempt4 a, tmanp1 b set a.ws=b.shift ,a.section=b.section,a.vendor=b.category  where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.type)='Duty'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="update tempt4  set ws=shift where ws='xxx' and shift='O'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
// 	$sql="update tempt4 a, tempcl3 b set a.ws=b.ws where a.employeeid=b.employeeid and trim(a.dt)=trim(b.dt) and trim(b.ws)<>'xxx' and trim(a.ws)='xxx'";   
//	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 
//echo $sql; 
 	$sql="update tempt4  set ws='' where ws='xxx'";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="delete from tempt4 where STR_TO_DATE(trim(dt),'%d-%m-%Y') >  STR_TO_DATE(trim('".$cdd."'),'%d-%m-%Y') ";   
//echo $sql;
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sqlr="update  tempt4 set ashifteng=1 where trim(ws)='A'";
$resultr = DB_query($sqlr, $db); 

$sqlr="update  tempt4  set bshifteng=1 where trim(ws)='B'";
$resultr = DB_query($sqlr, $db); 
$sqlr="update  tempt4  set cshifteng=1 where trim(ws)='C'";
$resultr = DB_query($sqlr, $db); 

$sqlr="update  tempt4  set totaleng=ashifteng+bshifteng+cshifteng";
$resultr = DB_query($sqlr, $db); 
	
 if($_SESSION['UserID']=='clms01'){
	$kkdept='CARE & CONCERN';
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
if($mcostcenter==''){
$mcostcenter='ALL';
}	
if($vendor==''){
//$mvendor='ALL';
}	

$mheading='<center><H3> Date wise Contract Laboutr Engagement for the Period from  '.$Sdate.' To '. $Todate.'- Vendor -'.$mvendor.' - Section '.$msection.'</h3></center>';
 $md="create temporary table tempt5 select dt,sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng) as'totaleng'  from tempt4 group by dt  order by   STR_TO_DATE(trim(dt),'%d-%m-%Y')   ";
		$result = DB_query($md,$db);

		$md="create temporary table tempt6 select dt,sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng) as'totaleng'  from tempt5  ";
		$result = DB_query($md,$db);
$md="update tempt6 set dt='Total'  ";
		$result = DB_query($md,$db);
$md="insert into tempt5 select * from tempt6  ";
		$result = DB_query($md,$db);
$md="select dt,ashifteng,bshifteng,cshifteng,totaleng from tempt5 " ;
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
//$md="select *  from tempt4 " ;
 		$result = DB_query($md,$db);

		$column_count = mysql_num_fields($result);
 //$md="select dt,sum(ashifteng) as'ashifteng),sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng) as'totaleng)  from tempt4 group by dt  order by   STR_TO_DATE(trim(dt),'%d-%m-%Y')   ";
 $md="select dt as'Date',Ashifteng,Bshifteng,Cshifteng,Totaleng from tempt5 " ;
// $md="select *  from tempt4 " ;

// $md="select * from tempt where trim(Name) <> '' and dayswkd>0 order by employeeid";
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
        //echo'<br>Total Manpower Engaged ='.$mtotdayswkd;
		echo '<br><left><td><td><a href="billingmonthly.php">' . _('Back to Monthly Billing Form') . '</a><BR></left>';
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
			print("<TD><center>$myrow1[$column_num]</center></TD>\n");

}
echo'</tr>';
		}		

echo'</table></center>';
echo'</div>';


  
 ?> 