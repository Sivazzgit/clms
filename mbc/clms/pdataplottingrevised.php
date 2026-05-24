<?php
/* $Revision: 1.0 $ */
// include('includes/sess.inc');
$title = _('Contract Labour Engagement Recording at Gate');

$tim01= date('m/d/Y h:i:s a', time());
//echo "<br>1 Starting Time".$tim01;
//$Sdate='14-02-2022';
$nextday = date('d-m-Y',strtotime($Sdate . "+1 days"));
//echo 'Next Day='.$nextday;
  
	$sql02 = "create temporary table temclmspunching like clmspunching  ";
	$result02 = DB_query($sql02,$db);

    $md="ALTER TABLE temclmspunching CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
	$result02 = DB_query($md,$db);


  $sql = "insert into temclmspunching(column0,column2,column3,column6,dt,time0) select column0,column2,column3,column6,dt,time0 
	 FROM clmspunchingupload where  column0>=2000 and column0<3000 
	 and  (STR_TO_DATE(trim(dt),'%d-%m-%Y') >= STR_TO_DATE('".$Sdate."','%d-%m-%Y'))
	 and  (STR_TO_DATE(trim(dt),'%d-%m-%Y') <= STR_TO_DATE('".$nextday."','%d-%m-%Y') ) order by dt,time0";
    $result = DB_query($sql,$db);

 

	
	$sql02 = "select * from temclmspunching ";

$sql02="CREATE TEMPORARY TABLE tpv ENGINE=InnoDB  SELECT @row_no := IF(@prev_val = t.column0, @row_no + 1, 1) AS row_number
   ,@prev_val := t.column0 AS column0
   ,t.dt,t.time0  
FROM temclmspunching t,
  (SELECT @row_no := 0) x,
  (SELECT @prev_val := '') y
ORDER BY t.dt,t.column0,time0"; 


	$result02 = DB_query($sql02,$db);
$sql02="select * from tpv";	
	$result02 = DB_query($sql02,$db);
$sql02="create temporary table tpplot SELECT column0,dt,
sum(IF(row_number=1, time0, 0)) AS p1,
sum(IF(row_number=2, time0, 0)) AS p2,
sum(IF(row_number=3, time0, NULL)) AS p3,
sum(IF(row_number=4, time0, NULL)) AS p4,00.00 as 'inp',00.00 as'outp'
FROM tpv
GROUP BY column0,dt";
	$result02 = DB_query($sql02,$db);


 	$sql01 = "update  tpplot set inp=p1";
	$result01 = DB_query($sql01,$db);
 	$sql01 = "update  tpplot set outp=p2";
	$result01 = DB_query($sql01,$db);

 	$sql01 = "update  tpplot set inp=p2,outp=p3 where p3>20";
	$result01 = DB_query($sql01,$db);

 	$sql01 = "update  tpplot set inp=p2,outp=p1 where p1>0 and p1<7 and p2>20";
	$result01 = DB_query($sql01,$db);

 	$sql01 = "update  tpplot set inp=0  where p1<7 and  p2=0";
	$result01 = DB_query($sql01,$db);
	//echo $sql01;
 	$sql01 = "update  tpplot set outp=p1 where p1<7 and p2=0";
	$result01 = DB_query($sql01,$db);
 	$sql01 = "select count(*) from tpplot where dt='".$Sdate."' and inp>0 ";
	$result01 = DB_query($sql01,$db);
	$myrow01 = DB_fetch_row($result01);
	$mpres=$myrow01[0];

 //	$sql01 = "create temporary table tcout select employeeid,dt from tpplot where inp>20 and dt='".$Sdate."'";
//	$result01 = DB_query($sql01,$db);
 	$sql01 = "create temporary table tcout1 select column0,dt,outp from tpplot where outp>0 and outp< 9 and dt='".$nextday."'";
	$result01 = DB_query($sql01,$db);
	
    $md="ALTER TABLE tcout1 CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
	$result02 = DB_query($md,$db);

 	$sql01 = "update tpplot a, tcout1 b set a.outp=b.outp where a.column0=b.column0 and a.inp>20 and  a.dt='".$Sdate."'";
	$result01 = DB_query($sql01,$db);



	echo "<br> No. of employees punched on '".$Sdate."' = ".$mpres;
	

?>