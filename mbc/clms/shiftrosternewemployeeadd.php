<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Shift Roster View');
include('includes/headerclms.inc');
/* 
if($_SESSION['UserID']=='rmsuser'){
	include('mfgheader2.php');
}else{
	include('mfgheader.php');

}
*/
$mfmonth='0122';
 $sql="create temporary table tnewemp  select * from clmsshiftroster".$mfmonth." where employeeid>='74000' order by employeeid" ;	
  $result = DB_query($sql,$db);
 echo $sql;
$sql="update tnewemp  set employeeid=employeeid+32" ;	
  $result = DB_query($sql,$db);
  
$sql="delete from tnewemp where employeeid='32'" ;	
  $result = DB_query($sql,$db);
  
//$sql="insert into clmsshiftroster".$mfmonth."(employeeid,shift,dt) select employeeid,shift,dt  from tnewemp" ;	
//  $result = DB_query($sql,$db);


 $md="select * from tnewemp";
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
		$column_count = mysql_num_fields($result);
 
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","clmsshroster");>Export to XLS</button></center>';	

echo'<center><table Border="1" id="tbl1" width ="25%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo'<center><H3>Employee Shift Roster for the Period from '.$msdt.' To '.$medt.'  </h3><center></td></tr>';
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


?>