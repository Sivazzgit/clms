<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Shift Roster View');
include('includes/headerclms.inc');
$InputError=0;
$sql0='';
$mdaynam='';	
$mdaydt='';
	
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
		//echo "<div style='display: block; margin-left:7%;margin-right:8%;width: 86%; background-color: white; '>";	
$cd=date('d-m-Y',strtotime("-1 days"));
$cd1=date('01-m-Y',strtotime("-1 days"));
$cd2=date('d-m-Y',strtotime("-1 days"));
		echo "<center><div style='display: block; width: 1066px;background-color: white; '>";	

 	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";
	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";
	echo '<CENTER><TABLE>';
        echo '<br>'; 
        echo ' <TR><TD width=200 height=20> <b>Date : </TD><TD width=200 height=20><input type="text1" name="datepicker" id="datepicker"  value="'.$cd1.'"></b></TD> ';
      echo ' <TD width=200 height=20> <b>Period To : </TD><TD width=200 height=20><input type="text1" name="datepicker1" id="datepicker1" value="'.$cd2.'"></b></TD> ';
	/*    
		echo " <TD width=200 height=20> <b>Item : </TD>
	    <TD><SELECT id = 'plant'  NAME='plant'>"; 
    	DB_data_seek($result, 0);
    	$sql = 'SELECT distinct item,b.dt FROM catlmfgspecmaster a,catlmfgproduction0117 b where a.prodcode=b.prodcode';
    	$result = DB_query($sql, $db);
        echo "<OPTION   VALUE='  '>" ;
    	while ($myrow = DB_fetch_array($result))  
	    {
        echo '<OPTION VALUE="'.$myrow['item'].'">' . $myrow['item'].$myrow['dt'];
	     } 
	    echo'</TD>';
  */  
		echo '</TR>';

	echo "</TR></TABLE><br><p><right><INPUT TYPE='Submit' NAME='submit1' VALUE='" . _('Show') . "'>";
         	

	echo '</FORM></div>';
  
   
if (isset($_POST['submit1'])) {
$msdt=$_POST['datepicker'];
$medt=$_POST['datepicker1'];
	
$date1 = new DateTime($msdt);
$date2 = new DateTime($medt);
$diff0 = $date2->diff($date1)->format("%a");

//echo"<br>Date Diff=".$diff0 ;

$time1 = strtotime($msdt);
$dt1 = date('Y-m-d',$time1);
//$tdate = date('Y-m-d');
$time2 = strtotime($medt);

$dt2=date('Y-m-d',$time2);

//echo"Changed Date".$newdt1;
//echo"current date ".$tdate;
if($diff0>30){
$InputError=1;	
 echo "<ul><li>Please select a period of maximun 31 days.</li></ul>";
// echo "Wrong Date Selection";	
}

if($dt1>$dt2){
$InputError=1;	
 echo "<ul><li>To date must be greater than From date.</li></ul>";
 //echo "Wrong Date Selection";	
}
if ($InputError == 1){
		prnMsg(_('Please Check') . _('<br>Ensure Correct Date Entry'),'warn');
}else{
	
//echo"<br>Date from  =".$date1;
//echo"<br>Date To =".$date2;
//echo"Hai";
	
$prgstarted = date('m/d/Y h:i:s a', time());
		
   
$Sdate=$_POST['datepicker'];
$Edate=$_POST['datepicker'];
$Edate1=$_POST['datepicker1'];
$cd1=$Sdate; 
//$KraType = $_POST['rtype'];
$mfmonth=substr($msdt,3,2).substr($msdt,8,2);
$mlmonth=substr($medt,3,2).substr($medt,8,2);
if($mfmonth==$mlmonth){
  $sql="create temporary table tfmon select * from clmsshiftroster".$mfmonth." where dt>='".$msdt."' and dt<='".$medt."'" ;	
  $result = DB_query($sql,$db);
}else{
  $sql="create temporary table tfmon select * from clmsshiftroster".$mfmonth." where dt>='".$msdt."'" ;	
  $result = DB_query($sql,$db);
  $sql="insert into tfmon select * from clmsshiftroster".$mlmonth." where  dt<='".$medt."'" ;	
  $result = DB_query($sql,$db);
	
}	
 //echo $sql;
$sql01="create temporary table tmon select Employeeid,'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy' as'Name',Grade,Section,Shiftgroup,Normal,Offday";
$sql00="select distinct dt,dayname(STR_TO_DATE(dt,'%d-%m-%Y')) as'daynam' from tfmon";
		$result00 = DB_query($sql00,$db);
		while ($myrow00 = DB_fetch_row($result00)) {
		$sql0=$sql0.',"xxx" as d'.substr($myrow00[0],0,2);	 
		$mdaynam=$mdaynam.",'".substr($myrow00[1],0,3)."'";	
		$mdaydt=$mdaydt.",d".substr($myrow00[0],0,2);
		}
//		echo $mdaynam;
  $sql0=$sql0.' from tfmon limit 0';
  $sql01=$sql01.$sql0;
//echo $sql01;
  $result01 = DB_query($sql01,$db);
$sql="insert into tmon(grade".$mdaydt.") values(' '".$mdaynam.")"; 
$result = DB_query($sql,$db);
 $sql02="select Employeeid,Grade,Section,Shiftgroup,Normal,Offday,shift,dt from tfmon order by employeeid ";
$result02 = DB_query($sql02,$db);
 		while ($myrow02 = DB_fetch_row($result02)) {
		$sql03="select count(*) from tmon where employeeid='".$myrow02[0]."'";
		$result03 = DB_query($sql03,$db);
		$myrow03 = DB_fetch_row($result03);
         if($myrow03[0]==0){
            $sql04="insert into tmon(Employeeid,Grade,Section,Shiftgroup,Normal,Offday) values('".$myrow02[0]."','".$myrow02[1]."','".$myrow02[2]."','".$myrow02[3]."',
			'".$myrow02[4]."','".$myrow02[5]."')";
		  $result04 = DB_query($sql04,$db);
		  //$myrow04 = DB_fetch_row($result04); 
         }		 
            $sql05="update tmon set d".substr($myrow02[7],0,2)."='".trim($myrow02[6])."' where trim(employeeid)='".trim($myrow02[0])."'";
           $result05 = DB_query($sql05,$db);
//echo $sql05;
	
	
		}

$md="update tmon a,  clmsemployeemaster b set a.Name=b.lastname where trim(a.employeeid)=trim(b.employeeid)";   
//echo $md;
	$result = DB_query($md,$db);
	
//$md="select * from clmsshiftroster".$mmonth;
//$md="select * from tsrost order by employeeid"; 
$md="select * from tfmon ";
$md="select * from tmon "; 
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
 		$result = DB_query($md,$db);
$column_count = mysqli_num_fields($result);
 
$md="select * from tsrost order by employeeid,dt";
$md="select * from tfmon order by employeeid";
 $md="select * from tmon order by shiftgroup,(1000-employeeid) desc";
//$md="select a.lastname as'Employee Name', b.* from prlemployeemaster a, tmon b where a.employeeid=b.employeeid"; 
		$result = DB_query($md,$db);
echo'<div>';

		echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","clmsshroster");>Export to XLS</button></center>';	

echo'<center><table Border="1" id="tbl1" width ="25%"  cellspacing="0" cellpaddin="0">';
echo'<tr><td colspan='.$column_count.'>';
echo'<center><H3>Employee Shift Roster for the Period from '.$msdt.' To '.$medt.'  </h3><center></td></tr>';
		print("<TR>");
		for($column_num = 0; $column_num < $column_count; $column_num++) {
		//	$field_name = mysql_field_name($result, $column_num);
        $field_name = mysqli_fetch_field_direct($result, $column_num)->name;
			
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
}
?>
