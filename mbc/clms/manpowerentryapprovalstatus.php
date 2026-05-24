<?php

$PageSecurity = 10;  

include('includes/sess.inc');

?>



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



<?php



$title = _('Contract Labour Engagement Approval Status');

if(substr($_SESSION['UserID'],0,4)<>'clms' ){

	exit("Not allowed. Unauthorised User");

}



include('includes/headerclms.inc'); 

$dt='';

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



 echo "User ID ".$_SESSION['UserID'];

	 $Todate=date("d-m-Y");



  echo'</div>';

	if(substr($_SESSION['UserID'],0,5)=='clms0' ){

	echo '<center><a href="gatemanpowerentry.php">' . _('Back to Attendance Entry Form') . '</a><BR>';



	}	

 echo '<center><a href="intend.php">' . _('Intend Creation') . '</a><BR>';
 echo '<center><a href="notyetkancor.php">' . _('Manning Deployment Review') . '</a><BR>';

	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "" . SID . "'>";

	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";

	echo '<CENTER><TABLE>';

        echo '<br>'; 

    echo ' <TR><TD align="right" width=200 height=20> <b>Date From : </TD><TD width=200 height=20><input type="text" name="datepicker" id="datepicker" value="'.$Sdate.'"></b></TD>

			   <TD align="right" width=200 height=20> <b>To : </TD><TD width=200 height=20><input type="text" name="datepicker1" id="datepicker1" value="'.$Todate.'"></b></TD>

	</TR> ';

	echo "</TR></TABLE><br><br><p><center><INPUT TYPE='Submit' NAME='submit1' VALUE='" . _('Show') . "'>";



	echo '</FORM><br>'; 

	

  if (isset($_POST['submit1'])) {

// echo"XXXXXXXX";

$Sdate=$_POST['datepicker'];

$Todate=$_POST['datepicker1'];

//$Edate=$_POST['datepicker1']; 

$Edate=$_POST['datepicker']; 

$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);

//echo $mmonth1;



	$sql0="CREATE TABLE IF NOT EXISTS clmsmanpowerapstatus".$mmonth1." LIKE  clmsmanpowerapstatus1221" ;

	$result0 = DB_query($sql0,$db); 



$md="create temporary table t01 select Dt,Shift,Section,category as'vendor',count(*) as'Engaged',000 as'Approved',000 as'intend' from clmsmanpower".$mmonth1." group by dt,shift,section,vendor";



		$result = DB_query($md,$db);

		

$sql="create temporary table t02 select distinct dt from clmsshiftroster".$mmonth1;

$result = DB_query($sql, $db); 





//$sqlr="select sum(ashift),sum(bshift),sum(cshift) from clmsintend where vendor='".$kkdept."' and STR_TO_DATE('".$Sdate."','%d-%m-%Y')  between str_to_date(stdate,'%d-%m-%Y') and str_to_date(endate,'%d-%m-%Y') group by vendor";

//if($_SESSION['UserID']=='clmsadmin'){

$sqlr="create temporary table t03 select distinct a.dt,b.section,b.vendor, sum(b.ashift) as'ashift',sum(b.bshift) as'bshift',sum(b.cshift) as'cshift'from t02  a,clmsintend b where  STR_TO_DATE(trim(a.dt),'%d-%m-%Y')  between str_to_date(trim(b.stdate),'%d-%m-%Y') and str_to_date(trim(b.endate),'%d-%m-%Y') group by a.dt,b.section,b.vendor  ";

//}	

//echo $sqlr; 



$resultr = DB_query($sqlr, $db); 

		

		$md="update t01 a,t03 b set a.intend = b.ashift where trim(a.dt)=trim(b.dt) and trim(a.section)=trim(b.section) and trim(a.vendor)=trim(b.vendor) and trim(a.shift)='A'";

		$result = DB_query($md,$db);

		$md="update t01 a,t03 b set a.intend = b.bshift where trim(a.dt)=trim(b.dt) and trim(a.section)=trim(b.section) and trim(a.vendor)=trim(b.vendor) and trim(a.shift)='B'";

		$result = DB_query($md,$db);

		$md="update t01 a,t03 b set a.intend = b.cshift where trim(a.dt)=trim(b.dt) and trim(a.section)=trim(b.section) and trim(a.vendor)=trim(b.vendor) and trim(a.shift)='C'";

		$result = DB_query($md,$db);



		$md="select * from t01";

		$result = DB_query($md,$db);



		while ($myrow = DB_fetch_row($result)) { 

		$md01="select count(*) from clmsmanpowerapstatus".$mmonth1." where trim(dt)='".trim($myrow[0])."'and trim(shift)='".trim($myrow[1])."' and trim(section)='".trim($myrow[2])."' and trim(vendor)='".trim($myrow[3])."'";

		$result01 = DB_query($md01,$db);

		$myrow01 = DB_fetch_row($result01);

	

         if($myrow01[0]==0){

			$md02="insert into clmsmanpowerapstatus".$mmonth1."(dt,shift,section,vendor) values('".trim($myrow[0])."','".trim($myrow[1])."','".trim($myrow[2])."','".trim($myrow[3])."')";

			$result02 = DB_query($md02,$db);

			}

			

			$md02="update clmsmanpowerapstatus".$mmonth1." set engaged=".$myrow[4]." where trim(dt)='".trim($myrow[0])."' and trim(shift) ='".trim($myrow[1])."' and trim(section)='".trim($myrow[2])."' and trim(vendor)='".trim($myrow[3])."' and trim(approvedby)=''";

			$result02 = DB_query($md02,$db);

			$md02="update clmsmanpowerapstatus".$mmonth1." set approved=".$myrow[4]." where  trim(dt)='".trim($myrow[0])."' and trim(shift) ='".trim($myrow[1])."' and trim(section)='".trim($myrow[2])."' and trim(vendor)='".trim($myrow[3])."'  and trim(approvedby)=''";

//		   echo $md02;

			$result02 = DB_query($md02,$db);

			$md02="update clmsmanpowerapstatus".$mmonth1." set intend=".$myrow[6]." where  trim(dt)='".trim($myrow[0])."' and trim(shift) ='".trim($myrow[1])."' and trim(section)='".trim($myrow[2])."' and trim(vendor)='".trim($myrow[3])."' ";

//		echo $md02;	

			$result02 = DB_query($md02,$db);

				

					

		}

		

$md="select * from clmsmanpowerapstatus".$mmonth1." where STR_TO_DATE(dt,'%d-%m-%Y')  between str_to_date('".$Sdate."','%d-%m-%Y') and str_to_date('".$Todate."','%d-%m-%Y')";

		$result = DB_query($md,$db);

$column_count = mysql_num_fields($result); 

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

	$kkdept='ALL';

}





$md="create temporary table tappr select * from clmsmanpowerapstatus".$mmonth1 ." where STR_TO_DATE(dt,'%d-%m-%Y')  between str_to_date('".$Sdate."','%d-%m-%Y') and str_to_date('".$Todate."','%d-%m-%Y')";

//echo $md;



if(substr($_SESSION['UserID'],0,5)=='clms0' ){

//  $md="select a.* from clmsmanpowerapstatus".$mmonth1." a,clmsusersection b where trim(a.section)=trim(b.section) and (b.employeeid)='".trim($_SESSION['UserID'])."'";

$md="create temporary table tappr select * from clmsmanpowerapstatus".$mmonth1 ." where trim(vendor)='".$kkdept."' and STR_TO_DATE(dt,'%d-%m-%Y')  between str_to_date('".$Sdate."','%d-%m-%Y') and str_to_date('".$Todate."','%d-%m-%Y')";

 }



 if(substr($_SESSION['UserID'],0,5)=='clms1' ){

//  $md="select a.* from clmsmanpowerapstatus".$mmonth1." a,clmsusersection b where trim(a.section)=trim(b.section) and (b.employeeid)='".trim($_SESSION['UserID'])."'";

  $md="create temporary table tappr select a.* from clmsmanpowerapstatus".$mmonth1." a,clmsusersection b where trim(a.section)=trim(b.section) and (b.employeeid)='".trim($_SESSION['UserID'])."' 

   and   STR_TO_DATE(a.dt,'%d-%m-%Y')  between str_to_date('".$Sdate."','%d-%m-%Y') and str_to_date('".$Todate."','%d-%m-%Y')";

 }

 //$md="select * from t01 ";

		$result = DB_query($md,$db);

$md="create temporary table tappr1 select dt,sum(engaged) as'engaged',sum(approved) as'approved',sum(intend) as'intend' from tappr ";

		$result = DB_query($md,$db);

$md="update  tappr1 set dt='Total' ";

		$result = DB_query($md,$db);

$md="insert into  tappr(dt,engaged,approved,intend) select dt,engaged,approved,intend from tappr1 ";

		$result = DB_query($md,$db);

		

$md="select * from tappr ";

		$result = DB_query($md,$db);





		 

//		echo $md;

echo'<div>';



		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","approval");>Export to XLS</button></center>';	

//		echo '<br><br><center><a href="intendaddrecord.php">' . _('Add New Record') . '</a><BR>';



echo'<center><table Border="1" id="tbl1" width ="100%"  cellspacing="0" cellpaddin="0">';

echo'<tr><td colspan='.$column_count.'>';

echo'<center><H3>Contract Labour Engagement Approval Status</h3><center></td></tr>';

		print("<TR>");

		for($column_num = 0; $column_num < $column_count; $column_num++) {

			$field_name = mysql_field_name($result, $column_num);

			print("<TH>$field_name</TH>");

		}

		print("</TR>");

		$z=0;

		while ($myrow1 = DB_fetch_row($result)) {

//echo '<tr>';

print("<TR>");

	$z++;

	$approvalid = "app".$z;

		for($column_num = 0; $column_num < $column_count; $column_num++) {

		

			if($column_num ==6 and trim($myrow1[8])=='') {

				print("<TD><input type='Text' id='".$approvalid."' name='approved' value='" . $myrow1[6] . "'></TD>\n");

			//	print("<TD><center>$myrow1[$column_num]</center></TD>\n");

			}else{	

				print("<TD><center>$myrow1[$column_num]</center></TD>\n");

            } 

}

		

			$url =  $rootpath . '/manpowerapproved.php?' . SID . '&shift='.$myrow1[2].'&section='.$myrow1[3].'&vendor='.$myrow1[4].'&mmonth='.$mmonth1.'&stdate='.$myrow1[1];



	if($myrow1[1]<>'Total'){

		

	 if(substr($_SESSION['UserID'],0,5)<>'clms0' ){

		 

		echo '<TD><A HREF="#" onclick="onapproval('."'".$url."','".$approvalid."'".')">' . _('Approve') . '</A></TD>';

//		echo '<TD><onclick(passval)="'. $rootpath . '/manpowerapproved.php?' . SID . '&shift='.$myrow1[3].'&section='.$myrow1[2].'&approve='.$_POST['approved'].'&stdate='.$myrow1[1].'">' . _('Approve') . '</A></TD>';

		echo '<TD><A HREF="'. $rootpath . '/manpowerapsendback.php?' . SID . '&shift='.$myrow1[2].'&section='.$myrow1[3].'&mmonth='.$mmonth1.'&stdate='.$myrow1[1].'">' . _('Send Back') . '</A></TD>';

		echo '<TD><A HREF="'. $rootpath . '/manpowerreportshow.php?' . SID . '&shift='.$myrow1[2].'&section='.$myrow1[3].'&mmonth='.$mmonth1.'&stdate='.$myrow1[1].'">' . _('Show Attendance') . '</A></TD>';

	 }

	}	

echo'</tr>';

		}		



echo'</table></center>';

echo'</div>';





}



?>