<?php

/* $Revision: 1.0 $ */



$PageSecurity = 15;



$PageSecurity = 10;  

include('includes/sess.inc');

$title = _('Shift Wise Labour Intend');

include('includes/headerclms.inc');

/* 

if($_SESSION['UserID']=='rmsuser'){

	include('mfgheader2.php');

}else{

	include('mfgheader.php');



}

*/



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



//$md0="update clmsintend a, clmsapprovalmatrix b  set a.approver=trim(b.approver) where substr(trim(a.createdby),1,8)=trim(b.creater)";

$md0="update clmsintend a, clmsapprovalmatrix b  set a.approver=trim(b.approver) where trim(a.section)=trim(b.section)";

//echo $md0;

$result = DB_query($md0,$db);



$md0="update clmsintend   set total=ashift+bshift+cshift";

$result = DB_query($md0,$db);





$md0="select olduserid from www_users where userid='".$_SESSION['UserID']."'";

$result = DB_query($md0,$db);

$myrow = DB_fetch_row($result);



if($_SESSION['UserID']=='clms01'){

	$kkdept='UNIVERSAL ASSOCIATES';

}

if($_SESSION['UserID']=='clms02'){ 

	$kkdept='VASS GROUP';

}

if($_SESSION['UserID']=='clms03'){

	$kkdept='KINGDOM SECURITY';

}

if($_SESSION['UserID']=='clmsadmin'){

//	$kkdept='ALL';

}

		

if($_SESSION['UserID'] == "clmsadmin"){

$md="select * from clmsintend order by recordid desc";

}

if(substr($_SESSION['UserID'],0,5)=='clms0' ){

//$md="select * from clmsintend where vendor='".$myrow[0]."'";

$md="select * from clmsintend where trim(vendor)='".$kkdept."' order by recordid desc";

	

}

	

if(substr($_SESSION['UserID'],0,5)=='clms1' ){

$md="select a.* from  clmsintend a,clmsusersection b where trim(a.section)=trim(b.section) and trim(b.employeeid)='".$_SESSION['UserID']."' order by recordid desc";

	

}



	

//$md="select * from clmsintend";

		$result = DB_query($md,$db);

$column_count = mysql_num_fields($result); 

 

//$md="select * from clmsintend";

		$result = DB_query($md,$db);

		

//		echo $md;

echo'<div>';



		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","shiftroster");>Export to XLS</button></center>';	

if(substr($_SESSION['UserID'],0,5)<>'clms0' ){		 

		echo '<br><br><center><a href="intendaddrecord.php">' . _('Add New Record') . '</a><BR>';

		echo '<br><br><center><a href="manpowerentryapprovalstatus.php">' . _('Manpower Status apprval') . '</a><BR>';

}

	if(substr($_SESSION['UserID'],0,5)=='clms0' ){

	//echo '<center><a href="gatemanpowerentry.php">' . _('Back to Attendance Entry Form') . '</a><BR>';

	echo '<center><a href="intendcalmonthly.php">' . _('Intend and Engagement View') . '</a><BR>';



	}	



echo'<center><table Border="1" id="tbl1" width ="75%"  cellspacing="0" cellpaddin="0">';

echo'<tr><td colspan='.$column_count.'>';

echo'<center><H3>Shift Wise Labour Intend</h3><center></td></tr>';

		print("<TR>");

		for($column_num = 0; $column_num < $column_count; $column_num++) {

			$field_name = mysql_field_name($result, $column_num);

			print("<TH>$field_name</TH>");

		}

			print("<TH></TH><TH></TH>");

		print("</TR>");



		while ($myrow1 = DB_fetch_row($result)) {

//echo '<tr>';

print("<TR>");

		for($column_num = 0; $column_num < $column_count; $column_num++) {

			print("<TD><center>$myrow1[$column_num]</center></TD>\n");



}

 

if(substr($_SESSION['UserID'],0,5)=='clmsa' ){		 

		echo '<TD><A HREF="'. $rootpath . '/intendaddrecord.php?' . SID . '&vendor='.$myrow1[1].'&section='.$myrow1[2].'&stdate='.$myrow1[3].'">' . _('Change') . '</A></TD>';

}

if(substr($_SESSION['UserID'],0,5)=='clms1' and trim($myrow1[14])=='' and trim($myrow1[15])<> trim($_SESSION['UserID'])) {		 

		echo '<TD><A HREF="'. $rootpath . '/intendaddrecord.php?' . SID . '&vendor='.$myrow1[1].'&section='.$myrow1[2].'&stdate='.$myrow1[3].'">' . _('Change') . '</A></TD>';

		echo '<TD><A HREF="'. $rootpath . '/intendsendingforapproval.php?' . SID . '&recid='.$myrow1[0].'&vendor='.$myrow1[1].'&section='.$myrow1[2].'&stdate='.$myrow1[3].'">' . _('Send for Approval') . '</A></TD>';

}

if(substr($_SESSION['UserID'],0,5)=='clms1' and substr(trim($myrow1[14]),0,4)=='Sent' and trim($_SESSION['UserID'])==trim($myrow1[15]) and trim($myrow1[13])=='' ) {		 

		echo '<TD><A HREF="'. $rootpath . '/intendaddrecord.php?' . SID . '&vendor='.$myrow1[1].'&section='.$myrow1[2].'&stdate='.$myrow1[3].'">' . _('Change') . '</A></TD>';

		echo '<TD><A HREF="'. $rootpath . '/intendapprove.php?' . SID . '&recid='.$myrow1[0].'&vendor='.$myrow1[1].'&section='.$myrow1[2].'&stdate='.$myrow1[3].'">' . _('Approve') . '</A></TD>';

}



echo'</tr>';

		}		



echo'</table></center>';

echo'</div>';









?>

