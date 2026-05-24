<?php
$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Contract Labour Engagement Approval Status');
if(substr($_SESSION['UserID'],0,4)<>'clms' ){
	exit("Not allowed. Unauthorised User");
}

include('includes/headerclms.inc'); 


	if($dt  == '')
	{
	  $_POST['dt']  = date("d-m-Y");
      $dt=date("01-m-Y");
	  }
 echo "User ID ".$_SESSION['UserID'];

  echo'</div>';	
 	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "" . SID . "'>";
	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";
	echo '<CENTER><TABLE>';
        echo '<br>'; 
    echo ' <TR><TD align="right" width=200 height=20> <b>Date From : </TD><TD width=200 height=20><input type="text1" name="datepicker" id="datepicker" value="' . $dt. '"></b></TD></TR> ';
	echo "</TR></TABLE><br><br><p><center><INPUT TYPE='Submit' NAME='submit1' VALUE='" . _('Show') . "'>";

	echo '</FORM><br>'; 
  if (isset($_POST['submit1'])) {
// echo"XXXXXXXX";
$Sdate=$_POST['datepicker'];
//$Edate=$_POST['datepicker1']; 
$Edate=$_POST['datepicker']; 
$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);
echo $mmonth1;

$md="create temporary table t01 select Dt,Shift,Section,count(*) as'Engaged',000 as'Approved' from clmsmanpower".$mmonth1." group by dt,shift,section";
		$result = DB_query($md,$db);
$md="create temporary table t02 select dt,shift,section,count(*) as'engaged',000 as'Approved' from clmsmanpowerap".$mmonth1." group by dt,shift,section";
		$result = DB_query($md,$db);

$md="update t01 a, t02 b set a.Approved=b.engaged where a.dt=b.dt and a.shift=b.shift and a.section=b.section";
		$result = DB_query($md,$db);

$md="select * from t01";
		$result = DB_query($md,$db);
$column_count = mysql_num_fields($result); 
 
 
 $md="select * from t01 where engaged <> Approved";
$md="select * from t01 ";
		$result = DB_query($md,$db);
		
		//echo $md;
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","approval");>Export to XLS</button></center>';	
//		echo '<br><br><center><a href="intendaddrecord.php">' . _('Add New Record') . '</a><BR>';
echo '<center><a href="intend.php">' . _('Intend Approval') . '</a><BR>';

echo'<center><table Border="1" id="tbl1" width ="75%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo'<center><H3>Contract Labour Engagement Approval Status</h3><center></td></tr>';
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
		 
//		echo '<TD><A HREF="'. $rootpath . '/intendaddrecord.php?' . SID . '&vendor='.$myrow1[1].'&section='.$myrow1[2].'&stdate='.$myrow1[3].'">' . _('Change') . '</A></TD>';

echo'</tr>';
		}		

echo'</table></center>';
echo'</div>';


}
?>