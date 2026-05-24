<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Job Master View');
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

$md="select recordid as'RECORDID',CODE,name as'Job',fromdate as'FROM',todate as'TO',BASIC,DA,HRA,UNIFORM,WASHING,nightshift as 'NIGHTSHIFT',safetyshoe as 'SAFETYSHOE',PF,ESI,BONUS,
	leaveencash as'LEAVEENCASH',OTHERS,servicecharge as'SERVICECHARGE',materialcost as 'MATERIALCOST',CGST,SGST from clmsjobmaster";	
//$md="select * from clmsjobmaster";
		$result = DB_query($md,$db);
$column_count = mysql_num_fields($result);
 
//$md="select * from clmsjobmaster"; 

		$result = DB_query($md,$db);
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","shiftroster");>Export to XLS</button></center>';	
if(substr($_SESSION['UserID'],0,5)=='clmsa' ){		 
		echo '<br><br><center><a href="notyet.php">' . _('Add New Record') . '</a><BR>';
}
echo'<center><table Border="1" id="tbl1" width ="75%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo'<center><H3>Job Master Details</h3><center></td></tr>';
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
if(substr($_SESSION['UserID'],0,5)=='clmsa' ){		 
		echo '<TD><A HREF="'. $rootpath . '/jobmasteredit.php?' . SID . '&vendor='.$myrow1[1].'&section='.$myrow1[2].'&stdate='.$myrow1[3].'">' . _('Change') . '</A></TD>';
}

echo'</tr>';
		}		

echo'</table></center>';
echo'</div>';




?>
