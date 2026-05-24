<?php
/* $Revision: 1.0 $ */

$PageSecurity = 15;

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Shift Roster Add New Employee');
include('includes/headerclms.inc');
$cthismon=date('01-m-Y',strtotime("-1 days"));
$cthismonth=substr($cthismon,3,2).substr($cthismon,8,2);
include('selectkkdept.inc');
    $md="create temporary table tmp SELECT employeeid,lastname FROM clmsemployeemaster WHERE trim(dept)='".$kkdept."' and  employeeid  NOT IN (SELECT distinct employeeid 
	from clmsshiftroster".$cthismonth." where clmsemployeemaster.employeeid=clmsshiftroster".$cthismonth.".employeeid )";
if($kkdept=='ALL'){
   $md="create temporary table tmp SELECT employeeid,lastname FROM clmsemployeemaster WHERE employeeid  NOT IN (SELECT distinct employeeid 
	from clmsshiftroster".$cthismonth." where clmsemployeemaster.employeeid=clmsshiftroster".$cthismonth.".employeeid)";
}
   	$result = DB_query($md,$db);

    $md="create temporary table tmp1 SELECT distinct b.employeeid,a.lastname FROM clmsemployeemaster a, clmsshiftroster".$cthismonth." b WHERE
	a.employeeid=b.employeeid and a.dept='".$kkdept."'";
if($kkdept=='ALL'){
    $md="create temporary table tmp1 SELECT distinct b.employeeid,a.lastname FROM clmsemployeemaster a, clmsshiftroster".$cthismonth." b WHERE
	a.employeeid=b.employeeid";
   }	
			$result = DB_query($md,$db);


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
$cd=date('01-m-Y',strtotime("-1 days"));
$cdd=date('d-m-Y',strtotime("-1 days"));

 	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "" . SID . "'>";
	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";
	echo '<CENTER><TABLE>';
        echo '<br>'; 
        echo ' <TR><TD width=400 height=20> <b>Starting Date of the Month : </TD><TD width=300 height=20><input type="text1" name="datepicker" id="datepicker" value="'.$cd.'"></b></TD> ';

        echo"<TR><TD><align=right><b>Employee ID of the New Employee </b></TD> ";
  		   echo "<TD><SELECT  style='text-align:center; id='prdd' name='empidnew'>"; 
			if ($myrow03[4] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[4].'">'.$myrow03[4];
			} 

		//	$sql = 'SELECT employeeid,lastname FROM prlemployeemaster where orgunit="CATL" and active=0';
		     $sql = 'SELECT employeeid,lastname FROM tmp order by lastname';
			$result = DB_query($sql, $db);
		 
						  echo "<OPTION   VALUE='  '>" ;

			while ($myrow = DB_fetch_array($result)) {

				  echo '<OPTION VALUE="'.$myrow[0].'">' . $myrow[1]."    ".$myrow[0];
               
			} //end while loop

        echo"<TR><TD><align=right><b>Employee ID of a Shift Mate</b></TD> ";
  		   echo "<TD><SELECT  style='text-align:center; id='prdd' name='empidsmate'>"; 
			if ($myrow03[4] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[4].'">'.$myrow03[4];
			} 

		//	$sql = 'SELECT employeeid,lastname FROM prlemployeemaster where orgunit="CATL" and active=0';
		     $sql = 'SELECT employeeid,lastname FROM tmp1 order by lastname';
			$result = DB_query($sql, $db);
		 
						  echo "<OPTION   VALUE='  '>" ;

			while ($myrow = DB_fetch_array($result)) {

				  echo '<OPTION VALUE="'.$myrow[0].'">' . $myrow[1]."    ".$myrow[0];
               
			} //end while loop



 //       echo ' <TR><TD width=400 height=20> <b>Employee ID of the New Employee : </TD><TD width=200 height=20><input type="text1" name="empidnew" id="empidnew"></b></TD> ';
 //       echo ' <TR><TD width=400 height=20> <b>Employee ID of a Shift  Mate : </TD><TD width=200 height=20><input type="text1" name="empidsmate" id="empidsmate"></b></TD> ';
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

$mmonth=$mmonth1;;	
$mempidnew=$_POST['empidnew'];
$mempidsmate=$_POST['empidsmate'];
 $sql="create temporary table tnewemp  select * from clmsshiftroster".$mmonth." where employeeid=".$mempidsmate." order by dt" ;	
 $result = DB_query($sql,$db);

 $sql="update tnewemp  set employeeid =".$mempidnew ;	
 $result = DB_query($sql,$db);

 $sql="select count(*) from clmsshiftroster".$mmonth." where employeeid=".$mempidnew;	
 $result = DB_query($sql,$db);
 $myrow = DB_fetch_row($result);
 //echo $sql;
 if($myrow[0]>0){
	exit("Found Shift Roster Updated "); 
 }else{

$sql="insert into clmsshiftroster".$mmonth."(employeeid,normal,shift,dt) select employeeid,normal,shift,dt  from tnewemp" ;	
  $result = DB_query($sql,$db);

   echo "New Updation Done";	
   
 }
 

  
//$sql="insert into clmsshiftroster".$mfmonth."(employeeid,shift,dt) select employeeid,shift,dt  from tnewemp" ;	
//  $result = DB_query($sql,$db);


 $md="select Employeeid,dt,normal,Shift from tnewemp";
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

}
?>