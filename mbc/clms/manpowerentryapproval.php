<?php

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Contract Labour Engagement Approval');
if(substr($_SESSION['UserID'],0,4)<>'clms' ){
	exit("Not allowed. Unauthorised User");
}

include('includes/headerclms.inc'); 
//include('includes/headerclmsmanpowerentry.inc');   

//include('mfgheader1.php'); 
//include('payrollcontrol.php');
//exit('Not Activated');
//$datamonth='0417'; 
/*
	$hostname = "localhost";
	$username = "anahaw";
	$password = "anahaw";
	$database = "anahaw";


	$conn = mysql_connect("$hostname","$username","$password") or die(mysql_error());
	mysql_select_db("$database", $conn) or die(mysql_error());
	
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

	<style>
		.headtable span{
			color:#20561d;
			font-weight: bold;
			text-decoration: none;
		}
	</style>  
    <script type="text/javascript">  
	function timevalidation(mcount){
		
		var fromsthrsmcount = document.getElementById('fromsthrs'+mcount).options[document.getElementById('fromsthrs'+mcount).selectedIndex].text;
		var fromstminmcount = document.getElementById('fromstmin'+mcount).options[document.getElementById('fromstmin'+mcount).selectedIndex].text;
		var tosthrsmcount   = document.getElementById('tosthrs'+mcount).options[document.getElementById('tosthrs'+mcount).selectedIndex].text;
		var tostminmcount   = document.getElementById('tostmin'+mcount).options[document.getElementById('tostmin'+mcount).selectedIndex].text;

		var starttime = parseInt(fromsthrsmcount*60)+parseInt(fromstminmcount);
		var endtime   = parseInt(tosthrsmcount*60)+parseInt(tostminmcount);
		//alert("You entered: " +starttime+"end time "+endtime);
	
		if(endtime < starttime)
		{
			endtime   = (parseInt(tosthrsmcount)+24)*60+parseInt(tostminmcount);
		}
		
		document.getElementById('duration'+mcount).focus();
		if((endtime -starttime) >480)
		{
			alert("start time  should be less than end time");
			//return true;
		}else
		{ 
			document.getElementById('duration'+mcount).value=(endtime -starttime);
	    }
	}
		
   function batchupdate (id) {
        // return true or false, depending on whether you want to allow the `href` property to follow through or not
 alert('hai');
	
/*
	if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest(); 
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{

					
				var idval = id.split("empid");
				var prdel ="prd"+idval[1];
				//alert(prdel);
			   // alert(idval);
				var x = document.getElementById(prdel);
				var i =0;
				var length = x.options.length;
				for (i = 0; i < length; i++) {
				  x.remove(0);
				}
				var str =xmlhttp.responseText;
				var vals = str.split("\n");
				for ( i = 0; i < vals.length-1; i++) 
				{
						
					var value = vals[i].split(">");
					
					var option = document.createElement("option");
					option.text = value[1];					
					option.value=value[0];	
					x.add(option);							
					//	
					
				}
				
				
			}
        };	
		xmlhttp.open("GET","onlinebatchupdate.php?column3="+document.getElementById(id).value,true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
	*/	
    }





function profilupdate (id) {
        // return true or false, depending on whether you want to allow the `href` property to follow through or not
		if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{

					
				var idval = id.split("empid");
				var prdel ="prd"+idval[1];
				//alert(prdel);
			   // alert(idval);
				var x = document.getElementById(prdel);
				var i =0;
				var length = x.options.length;
				for (i = 0; i < length; i++) {
				  x.remove(0);
				}
				var str =xmlhttp.responseText;
				var vals = str.split("\n");
				for ( i = 0; i < vals.length-1; i++) 
				{
						
					var value = vals[i].split(">");
					
					var option = document.createElement("option");
					option.text = value[1];					
					option.value=value[0];	
					x.add(option);							
					//	
					
				}
				
				
			}
        };	
		xmlhttp.open("GET","onlineupdate.php?plant="+document.getElementById(id).value,true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
    }
	
	function updatecompound (id) {
        // return true or false, depending on whether you want to allow the `href` property to follow through or not
		var myTextField = document.getElementById('workcenter');
		//alert(myTextField);
		if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest(); 
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{
				//var mc =0;  
							for (mc = 1; mc < 2; mc++) { 
		                       var prdel='compcode'+mc;
							   
							
				//var idval = id.split("compcode");
				//var prdel ="compcode"+idval[1];
				//alert("workcenter");
			   // alert(idval);
				var x = document.getElementById(id);
				var i =0;
				var length = x.options.length;
				for (i = 0; i < length; i++) {
				  x.remove(0);
				}
				var str =xmlhttp.responseText;
				var vals = str.split("\n");
				 //alert (prdel);  
				for ( i = 0; i < vals.length-1; i++) 
				{
						
					var value = vals[i].split(">");
					
					var option = document.createElement("option");
					option.text = value[1];					
					option.value=value[0];	
					x.add(option);							
					//	
					
				}
							}
				
			}
        };	
		xmlhttp.open("GET",
					"onlineupdatecompound.php?workcenter="
					+document.getElementById("workcenter").value
					+"&datepicker="+document.getElementById("datepicker").value
					+"&shift="+document.getElementById("shift").value);
					
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
    }

	function updateoperator (id) {
        // return true or false, depending on whether you want to allow the `href` property to follow through or not
		var myTextField = document.getElementById('workcenter');
		//alert(myTextField);
		if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{

					
				//var idval = id.split("empid");
				var prdel ="empid";
				//alert("workcenter");
			   // alert(idval);
				var x = document.getElementById(id);
				var i =0;
				var length = x.options.length;
				for (i = 0; i < length; i++) {
				  x.remove(0);
				}
				var str =xmlhttp.responseText;
				var vals = str.split("\n");
				for ( i = 0; i < vals.length-1; i++) 
				{
						
					var value = vals[i].split(">");
					
					var option = document.createElement("option");
					option.text = value[1];					
					option.value=value[0];	
					x.add(option);							
					//	
					
				}
				
				
			}
        };	
		xmlhttp.open("GET",
					"onlineupdateoperator.php?workcenter="
					+document.getElementById("workcenter").value
					+"&datepicker="+document.getElementById("datepicker").value
					+"&shift="+document.getElementById("shift").value);
					
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
    }
	
</script>



<?php


	if($dt  == '')
	{
	  $_POST['dt']  = date("d-m-Y");
      $dt=date("d-m-Y");
	  }
      $curtime=date("H.i");
	if($curtime<=7.59){  
	   $dt=date('d-m-Y',strtotime("-1 days"));
    }
//	  echo'Current Time'. $curtime;
if ($ms<>'1'){
	
	  if($curtime>7.59 and $curtime<16.00){
		  //$ms='A Shift';
		  $ms='A';

	  }	  
	  if($curtime>15.59 and $curtime<=23.59){
		  $ms='B Shift';
		  $ms='B';
	  }	  
	  if($curtime>23.59 or $curtime<8.00){
		  $ms='C Shift';
		  $ms='C';
	  }	
   }	  
	  if($_POST['fromtime']  == 0)
	{
		$_POST['fromtime']= 6.01;
		$_POST['totime']= 6.00;
	} 
echo "User ID ".$_SESSION['UserID'];

if($_SESSION['UserID']<>'0000' ){
	
}else{
	exit("Not allowed. Unauthorised User");
	

}
		$sql1 = "select  distinct employeeid,area from usersection where employeeid='".$_SESSION['UserID']."'";
		$result1 = DB_query($sql1,$db);
        $myrow1 = DB_fetch_row($result1);
        if($myrow1[0]<>''){
			
			$marea=$myrow1[1];
		}
//	echo "Area =".$marea; 
$md0="select olduserid from www_users where userid='".$_SESSION['UserID']."'";
$result = DB_query($md0,$db);
$myrow = DB_fetch_row($result);
$msection=$myrow[0];
    echo'</div>';	
/*	
//	echo "<div style='display: block; margin-left:0%;margin-right:8%;width: 100%; background-color: rgb(255, 15, 128); overflow: auto; '>";
	echo "<div class='datadiv' style='display: block; margin-left:15%;margin-right:8%;width: 77%;  overflow: auto; '>";
    //echo"<h2><center>Delay Data Entry</center></h2>";
//	echo '<center><button style="margin-left:30px;" id = "send" title="Export to XLS"   onclick = exporttoXl("tbl1","timesheet");>Export</button></center>';	
//	echo '<center><h1>Daily Shift Wise - Manpower Data Entry Form</h1></center><br><br>';	
//echo '<Left><a href="mfgdelaydataentrynew.php">' . _('Delay Entry') . '</a><BR>';
//echo '<Left><a href="mfgproductiondataentrynew.php">' . _('Production Entry') . '</a><BR>';
echo '<center><a href="manpowerdataentrysummary.php">' . _('Show Manpower Data Entry Summary') . '</a><BR>';
echo '<center><a href="manpowerdataentryshow.php">' . _('Show Manpower Data') . '</a><br>';
*/
//echo '<center><a href="prlSelectEmployee.php">' . _('Add Employee') . '</a><BR>';
echo '<center><a href="manpowerentryapprovalstatus.php">' . _('Contract Labour Engagement Approval Status') . '</a><BR>';
echo '<center><a href="intend.php">' . _('Intend Approval') . '</a><BR>';
//echo '<center><a href="mustrolattendance1.php">' . _('Show Attendance Mustroll') . '</a><BR>';
/*
echo '<center><a href="punchingattendancereport.php">' . _('Show Monthly Punching Attendance Report') . '</a><BR>';
//echo '<center><a href="earlygoingreport.php">' . _('Show Early Going Report') . '</a><BR>';
//echo '<center><a href="presentwithnopunchingreport.php">' . _('Show Attendance Without Punching Report') . '</a><BR>';
echo '<center><a href="punchingwithoutpresentreport.php">' . _('Show Punching  Without Attendance Report') . '</a><BR>';
echo '<center><a href="shiftchangeattendancemarkedreport.php">' . _('Show Shift Change Attendance Report') . '</a><BR>';
*/

//echo "MMMMSSS".$curtime."Shift".$ms;
 	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "" . SID . "'>";
	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";
	echo '<CENTER><TABLE>';
        echo '<br>'; 
        echo ' <TR><TD align="right" width=200 height=20> <b>Date : </TD><TD width=200 height=20><input type="text1" name="datepicker" id="datepicker" value="' . $dt. '"></b></TD> ';
	echo "<TD align='right' width=200 height=20><b>Shift</TD><TD><SELECT  id ='shift' NAME='shift' value='" . $ms. "'>";
//	echo "<TD align='right' width=200 height=20><b>Shift</TD><TD><SELECT  id ='shift' NAME='shift' >";
//	echo '<OPTION SELECTED VALUE="'.$ms.'">'.$ms;
	echo '<OPTION  VALUE="'.$ms.'">'.$ms.' Shift';
//	echo  "<OPTION VALUE=''>".$ms;
	echo  "<OPTION VALUE='A'>A Shift"; 
	echo  "<OPTION VALUE='B'>B Shift"; 
	echo  "<OPTION VALUE='C'>C Shift";
	echo  "</TD></SELECT>  ";
	if($marea<>''){
	echo "<INPUT TYPE='hidden' NAME='workcenter' VALUE='".$marea."'>";

	}else{
/*	
	echo  "<TD  align='right' style='color: ublue;' width=200 height=20><b>Plant</TD><TD><SELECT id ='wc' NAME='workcenter' >"; 
	echo  "	<OPTION VALUE=''> "; 
	echo  "<OPTION VALUE='SEP 1'>SEP 1";
	echo  "<OPTION VALUE='SEP 2'>SEP 2";
	echo  "<OPTION VALUE='FGS'>FGS"; 
	echo  "<OPTION VALUE='EOD'>EOD"; 
	echo  "<OPTION VALUE='PP6'>PP6"; 
	echo  "<OPTION VALUE='Utility'>Utility"; 
		    
	echo  "</TD></SELECT>  ";
	*/	
		echo " <TD  hidden width=80 height=20> <b>Area : </TD> 
	    <TD hidden><SELECT id = 'wc'  NAME='workcenter'>"; 
    	DB_data_seek($result, 0);
    	$sql = 'SELECT  userid,realname,olduserid FROM www_users where substr(userid,1,4)="clms" order by olduserid';
    	$result = DB_query($sql, $db);
        echo "<OPTION   VALUE='  '>" ;
    	while ($myrow = DB_fetch_array($result)) 
	    {
        echo '<OPTION VALUE=' . trim($myrow['userid']) . '>' . $myrow['olduserid'].'  '. $myrow['realname'];
	     } 
	    echo'</TD>';
		
 		//echo '</TR>';
		
}
	echo'<TD hidden><a href="usersectionupdation1.php">' . _('Select Section') . '</a></TD>';
	echo "</TR></TABLE><br><br><p><center><INPUT TYPE='Submit' NAME='submit1' VALUE='" . _('Show') . "'>";
         	

	echo '</FORM><br>'; 
  
 //echo '</div>';  
if (isset($_POST['submit1'])) {
// echo"XXXXXXXX";
$Sdate=$_POST['datepicker'];
//$Edate=$_POST['datepicker1']; 
$Edate=$_POST['datepicker']; 
$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);
$mshift=trim($_POST['shift']); 
$mwc=trim($_POST['workcenter']);
//echo $mmonth1; 
if($mmonth1<>'1018'){
	$sql0="CREATE TABLE IF NOT EXISTS clmsmanpowerap".$mmonth1." LIKE  manpower0921" ;
	$result0 = DB_query($sql0,$db); 
}

if($mwc<>''){
	
	$sql0="CREATE temporary table tsec select section from usersection where area='".$mwc."'" ;
	$result0 = DB_query($sql0,$db); 
}else{
	$sql0="CREATE temporary table tsec select section from usersection " ;
	$result0 = DB_query($sql0,$db); 
	
}
	$sql0="CREATE temporary table tsec01 select a.section,b.employeeid from tsec a,clmsemployeemaster b where a.section=b.section " ;
	$result0 = DB_query($sql0,$db); 
	
	$sql0="create temporary table tareacheck select a.*  from 	clmsmanpowerap".$mmonth1." a, tsec01 b where trim(a.employeeid)=trim(b.employeeid) and  a.dt between '".$Sdate."' and '".$Edate."' and trim(a.shift)='".$mshift."'";
	$result0 = DB_query($sql0,$db); 
	$sql="select count(*) from tareacheck where workcenter <>'$mwc'";
	$result = DB_query($sql,$db); 

	$myrow = DB_fetch_row($result);  
    $mcnn=$myrow[0];
    if($mcnn >0 and trim($mwc)<>''){
//		$sql="select * from tareacheck where workcenter <>'$mwc'";  
		$sql="select workcenter as 'Done by',dt as 'Date',Shift,Employeename,Type,HRS,Grade,Actgrade,Section,SPICE,Manning,Timein,Sshift as'Sch Shift' from tareacheck where workcenter <>'$mwc'";  
         $mheading="Manpower Data Allready Updated";
	
    $DbgMsg = _('The SQL that was used to insert the employee but failed was');
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $column_count = mysql_num_fields($result03);
//		 	echo"Check-3";
 //          echo $sql; 
	  echo'<center><table id="tbl1" Border="1" width ="100%"  cellspacing="0" cellpaddin="0">'; 

    print("<TR>");
echo' <td colspan="'.($column_count).'"><center><INPUT type="image" name="submit"  style="width:30px;height:30px;" src="../rcs/includes/back_button.png" VALUE="Back" onClick="history.go(-2);return true;"><br><p style="font-size:20;">&nbsp;&nbsp;Back to Data Entry form</p></td>';
	echo "</tr><tr><th colspan='".($column_count)."'>";
    echo'<p>'.$mheading.'</p></th></tr><tr>';
    for($column_num = 0; $column_num < $column_count; $column_num++) 
    {
        $field_name = mysql_field_name($result03, $column_num);
        print("<TH>$field_name</TH>\n");
    }
    print("</TR>");     
 	   
		     while ($myrow03 = DB_fetch_row($result03))  
    {
      $mdoneby = $myrow03[1];     
	  print("<TR>"); 
        for($column_num = 0; $column_num < $column_count; $column_num++) 
        {
                print("<TD><center>$myrow03[$column_num]</center></TD>\n");

        }
     }
	echo"</table>";
 
		//exit('Data entry done by '.$mdoneby);
	}	

	$sql0="select count(*) from 	clmsmanpower".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and trim(shift)='".$mshift."' and section='".$msection."'";
			$result0 = DB_query($sql0,$db); 
//echo $sql0;

while ($myrow0 = DB_fetch_row($result0))  
    {
	$mcn=$myrow0[0];
    }	
	if($mcn ==0){

	$sql = "CREATE TEMPORARY TABLE TempT1 as (SELECT *,00 as'slno'  FROM clmsmanpower".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and trim(shift)='".$mshift."' and trim(yarea)='Submitted')";
		$result = DB_query($sql,$db);

	$sql=="delete from TempT1";	
	$sql = "select recordid from TempT1 ";

    $DbgMsg = _('The SQL that was used to insert the employee but failed was');
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$msln=1 ;
 while ($msln <10)  
    {
		$mrid=$myrow03[0];
		$sql1 = "insert into  TempT1(slno) values(".$msln.")";
		$result1 = DB_query($sql1,$db);
        $msln=$msln+1;
	} 
	//echo "<FORM METHOD='post' id='f1' ACTION='" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";
	}else{

echo"MCN".$mcn;
 	$sql = "CREATE TEMPORARY TABLE TempT1 as (SELECT * ,00 as'slno' FROM clmsmanpower".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and trim(shift)='".$mshift."' and trim(section)='".$msection."' and trim(yarea)='Submitted')";
	$result = DB_query($sql,$db);
//		echo "rcd cnt=".$mcn;
		$msln=$mcn+1 ;
		while ($msln <10)  
		{
		$mrid=$myrow03[0];
		$sql1 = "insert into  TempT1(employeeid,type) values('','Duty')";
		$result1 = DB_query($sql1,$db);
        $msln=$msln+1;
	}
	}
	
//echo'uuuu'. $mcn;
//echo $sql ; 

if($mcn == 0){
//	echo "Hai";
}
			$sql = "delete from punching";
			$result = DB_query($sql,$db);

//			$sql = "insert into punching(column0,column3,dt,time0,column6) select employeeid,in_out,date,(trim(time)/1),time  FROM punchingapi where trim(date)= '".$Sdate."' and employeeid>10 and employeeid<200 ";
//			$sql = "insert into punching(column0,column3,dt,time0,column6) select employeeid,in_out,date,(trim(time)/1),time  FROM punchingapi where  employeeid>10 and employeeid<200 ";
//			$sql = "insert into punching(column0,column3,dt,time0,column6) select employeeid,in_out,date,(trim(time)/1),time  FROM punchingapi where  employeeid>10 and employeeid<200  and substr(trim(date),3,7)=substr(trim('".$Sdate."'),3,7)";
			$sql = "insert into punching(column0,column3,dt,time0,column6) select employeeid,in_out,date,(trim(time)/1),time  FROM punchingapi where  employeeid>10 and employeeid<200  and (STR_TO_DATE(date,'%d-%m-%Y') >= STR_TO_DATE('".$Sdate."','%d-%m-%Y') )";

//echo $sql;
			$result = DB_query($sql,$db);
			$sql = "update  punching set time0=substr(column6,1,2)+(substr(column6,4,2)/100)";
			$result = DB_query($sql,$db);
include('punchingdateround.php'); 
			$sql = "update punching a,clmsshiftroster".$mmonth1."  b  set a.column2=b.shift where trim(a.column0)=trim(b.employeeid) and trim(a.column4)=trim(b.dt)";  
//			$sql = "update punching a,clmsshiftroster".$mmonth1."  b  set a.column2=b.shift where trim(a.column0)=trim(b.employeeid) and trim(a.dt)=trim(b.dt)";  
			$result = DB_query($sql,$db);
			$sql = "update punching a,shiftchange  b  set a.column2=b.chshift where trim(a.column0)=trim(b.employeeid) and trim(a.column4)=trim(b.dt)";  
			$result = DB_query($sql,$db);

			$sql = "update  punching set column3=''   ";
			$result = DB_query($sql,$db);


			$sql = "update  punching set column3='IN' where column2='1' and column5 >5.00 and column5<10.00  ";
			$result = DB_query($sql,$db);

			$sql = "update  punching set column3='OUT' where column2='1' and column5 >15.00 and column5<18.00  ";
			$result = DB_query($sql,$db);
			
			$sql = "update  punching set column3='IN' where column2='2' and column5 >12.00 and column5<22.00  "; 
			$result = DB_query($sql,$db);
			$sql = "update  punching set column3='OUT' where column2='2' and column5 >23.00 and column5<27.00  ";
			$result = DB_query($sql,$db);

			$sql = "update  punching set column3='IN' where column2='3' and column5 >21.00 and column5<26.00  ";
			$result = DB_query($sql,$db);
			$sql = "update  punching set column3='OUT' where column2='3' and column5 >27.00 and column5<33.00  ";
			$result = DB_query($sql,$db);
			$sql = "update  punching set column3='OUT' where column2='3' and column5 >5.00 and column5<9.00  ";
			$result = DB_query($sql,$db);

			$sql = "update  punching set column3='OUT' where column2='O' and column5 >5.00 and column5<9.00  ";
			$result = DB_query($sql,$db);
			
  $sql0p="create temporary table tp02 select recordid,dt,time0 from punching  where time0 < 9 and column3='OUT'"     ;
   $result0p = DB_query($sql0p,$db,$ErrMsg);
 
 $sql0p="select recordid,dt,time0 from tp02" ;    
   $result0p = DB_query($sql0p,$db,$ErrMsg);
	while ($myrow0p = DB_fetch_row($result0p)) {
	$previousdt = date('d-m-Y',strtotime($myrow0p[1] . "-1 days"));
	 //echo"<br>Today".$myrow0p[1];
	 //echo"<br> yday".$previousdt."<br>";
     $sql00k="update punching set column4='".$previousdt."' where recordid=".$myrow0p[0] ;     
     $result00k = DB_query($sql00k,$db,$ErrMsg); 
  $sql="update punching set column5=".($myrow0p[2]+24)." where recordid=".$myrow0p[0] ;      
    $result = DB_query($sql,$db,$ErrMsg);
//echo $sql;	      
	}


			
			$sql = "update  punching set column2='A' where column2='1'   ";
			$result = DB_query($sql,$db);
			$sql = "update  punching set column2='B' where column2='2'   ";
			$result = DB_query($sql,$db);
			$sql = "update  punching set column2='C' where column2='3'   ";
			$result = DB_query($sql,$db);
			
	if($mcn ==0){

	

		$sql = "CREATE TEMPORARY TABLE tpunching select * from punching where trim(column4)='".$Sdate."' and trim(column2)='".$mshift."' and trim(column3)='IN'";
			$result = DB_query($sql,$db);
		$sql = "update tpunching set time0=24.00 where time0=0";
			$result = DB_query($sql,$db);
			
if($mshift=='A'){
 $rshift=1;
}	
if($mshift=='B'){
 $rshift=2;
}	
if($mshift=='C'){
 $rshift=3;
}
	
              $sql = "CREATE TEMPORARY TABLE tpunching1  SELECT employeeid as'column0','  ' as'column1','".$mshift."' as'Column2','IN' as'column3',dt as'column4',dt FROM clmsshiftroster".$mmonth1."  where dt = '".$Sdate."'  and trim(shift)='".$rshift."'";
			  $result = DB_query($sql,$db);
              $sql = "update  tpunching1 a,tpunching b set a.column1='p' where a.column0=b.column0";
			  $result = DB_query($sql,$db);
       	$sql = "update  tpunching1 set column3='IN' ";
			  $result = DB_query($sql,$db);

 $sql = "delete from  tpunching1 where column1='p' ";
			  $result = DB_query($sql,$db);
          $sql = "insert into tpunching(column0,column1,column2,column3,column4,dt) select column0,column1,column2,column3,column4,dt from  tpunching1 ";
			  $result = DB_query($sql,$db);
			  
/*
  $sql = "select column0,column2,column3,column4,dt from  tpunching ";
			  $result = DB_query($sql,$db);
  
	 while ($myrow = DB_fetch_row($result))  
    {
	echo '<br>'.$myrow[0].'--'.$myrow[1].'--'.$myrow[2].'--'.$myrow[3].'--'.$myrow[4];
	//.'--'.$myrow[5];
	}
  */
  
    		  $sql = "CREATE TEMPORARY TABLE TempT2 as (SELECT * ,00  as'slno','   ' as'sshift' FROM manpower0420 limit 0)";
			$result = DB_query($sql,$db);
//			$sql = "create temporary table Temp3 select  distinct a.column0 as'employeeid',b.lastname as'name',b.section,b.grade from punching a, clmsemployeemaster b where a.column0=b.employeeid and a.dt between '".$Sdate."' and '".$Edate."' and trim(a.column2)='".$mshift."' and trim(a.column3)='IN'"; 
			$sql = "create temporary table Temp3 select  distinct a.column0 as'employeeid',b.lastname as'name',b.section,b.grade,a.time0 as'timein','                             'as'sect' from punching a, clmsemployeemaster b where a.column0=b.employeeid and trim(a.column4) between '".$Sdate."' and '".$Edate."' and trim(a.column2)='".$mshift."' and trim(a.column3)='IN'"; 
			$sql = "create temporary table Temp3 select  distinct a.column0 as'employeeid',b.lastname as'name',b.section,b.grade,a.time0 as'timein','                             'as'sect' from tpunching a, clmsemployeemaster b where a.column0=b.employeeid and trim(a.column4) between '".$Sdate."' and '".$Edate."' and trim(a.column2)='".$mshift."' and trim(a.column3)='IN'"; 
			$result = DB_query($sql,$db); 
	//echo $sql ;


			$sql = "update Temp3 a,tsec b  set a.sect=b.section where a.section=b.section";  
			$result = DB_query($sql,$db);

			$sql = "insert into TempT2(slno,employeeid,employeename,type,hrs,section,grade,timein) select 0,employeeid,name,'Duty',8,section,grade,timein from Temp3 where trim(sect) <>''"; 
		$result = DB_query($sql,$db);
 

$hrs12shiftstartedon='08-05-2021';
$hrs12shiftstartedon=date("Y-m-d",strtotime($hrs12shiftstartedon));

$hrs12shiftendedon='21-06-2021'; 
$hrs12shiftendedon=date("Y-m-d",strtotime($hrs12shiftendedon));
$thismonthdate=date("Y-m-d",strtotime($Sdate));			


if($thismonthdate>=$hrs12shiftstartedon and $thismonthdate<=$hrs12shiftendedon ){
			$sql = "update TempT2  set hrs=12";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2  set hrs=8 where employeeid='145'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2  set hrs=8 where employeeid='172'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2  set hrs=8 where employeeid='112'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2  set hrs=8 where employeeid='109'";   
			$result = DB_query($sql,$db);

}	
			$sql = "update TempT2 a,clmsshiftroster".$mmonth1."  b  set a.sshift=b.shift where a.employeeid=b.employeeid and b.dt='".$Sdate."'";  
//echo $sql;
			$result = DB_query($sql,$db);

			$sql = "update TempT2 set type='ABSENT' where timein=0";  
     		$result = DB_query($sql,$db);
//This is to avoid allready updated data duplication
            if($mdoneby<>""){
			$sql = "update TempT2 a, tareacheck b set a.category='D' where a.employeeid=b.employeeid";  
  
  $result = DB_query($sql,$db);
 			$sql = "delete from TempT2  where category='D'"; 
   		$result = DB_query($sql,$db);
			}
			   
//********			
		
			
			$sql = "insert into TempT2(employeeid,type) values('','Duty')";  
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);

			$sql = "update TempT2 a,allowancemaster  b  set a.actgrade='1A'  where a.section=trim(b.column1) and trim(b.column2)=3 and (trim(a.grade)='G32' or trim(a.grade)='G4')";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 a,allowancemaster  b  set a.actgrade='1A'  where a.section=trim(b.column1) and trim(b.column2)=2 and (trim(a.grade)='G3' or trim(a.grade)='G4')";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 a,allowancemaster  b  set a.actgrade='1B'  where a.section=trim(b.column1) and trim(b.column2)=1 and (trim(a.grade)='G2' or trim(a.grade)='G74')";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 a,allowancemaster  b  set a.actgrade='1A'  where a.section=trim(b.column1) and trim(b.column2)=1 and (trim(a.grade)='G3' or trim(a.grade)='G4')";  
			$result = DB_query($sql,$db);


			$sql = "update TempT2 a,allowancemaster  b  set a.actgrade='1C'  where a.section=trim(b.column1) and trim(b.column2)='SG' and (trim(a.grade)='G1' or trim(a.grade)='G74')";  
			$result = DB_query($sql,$db);

			$sql = "update TempT2 a,allowancemaster  b  set a.actgrade='1A'  where a.section=trim(b.column1) and trim(b.column2)='SG' and (trim(a.grade)='G3' or trim(a.grade)='G4')";  
			$result = DB_query($sql,$db);

			$sql = "update TempT2 a,allowancemaster  b  set a.actgrade='1B'  where a.section=trim(b.column1) and trim(b.column2)='SG' and (trim(a.grade)='G2' or trim(a.grade)='G74')";  
			$result = DB_query($sql,$db);
			
			
			$sql = "update TempT2 a,allowancemaster  b  set a.manning='M2'  where a.section=trim(b.column1) and trim(b.column3)='YES' and a.employeeid >100";  
			$result = DB_query($sql,$db);
//echo $sql;
			$sql = "update TempT2 a,allowancemaster  b  set a.manning='M2'  where a.employeeid>=60 and employeeid<100";   
			$result = DB_query($sql,$db);
			$sql = "update TempT2 a,allowancemaster  b  set a.spice='ASH'  where a.section=trim(b.column1) and trim(b.column5)='YES'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 a,allowancemaster  b  set a.spice='ETP'  where a.section=trim(b.column1) and trim(b.column7)='YES'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 a,allowancemaster  b  set a.spice='T14'  where a.section=trim(b.column1) and trim(b.column8)='YES'";  
			$result = DB_query($sql,$db);
 
			$sql = "update TempT2 set actgrade='' where type='ABSENT' ";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set manning=''  where type='ABSENT'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set spice=''  where type='ABSENT'  ";  
			$result = DB_query($sql,$db);

			
			$sql = "update TempT2 set actgrade='' where grade='WTR' ";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set manning=''  where grade='WTR'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set spice=''  where grade='WTR'  ";  
			$result = DB_query($sql,$db);
			 
	 }else{
		
			$sql0a="select count(*) from 	clmsmanpowerap".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and trim(shift)='".$mshift."' and section='".$msection."'";
			$result0a = DB_query($sql0a,$db); 
//echo $sql0;

			while ($myrow0a = DB_fetch_row($result0a))  
			{
				$mcna=$myrow0a[0];
			}	
         	if($mcna==0){
				$sql = "CREATE TEMPORARY TABLE TempT2 as (SELECT * ,00 as'slno' FROM clmsmanpower".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and trim(shift)='".$mshift."' and section='".$msection."' and trim(yarea)='Submitted')";
			}else{
				$sql = "CREATE TEMPORARY TABLE TempT2 as (SELECT * ,00 as'slno' FROM clmsmanpowerap".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and trim(shift)='".$mshift."' and section='".$msection."')";
            } 			
			$result = DB_query($sql,$db);
			if($mcna==0){
				$sql = "update TempT2  set yarea=''";  
				$result = DB_query($sql,$db);
			}
			$sql = "update TempT2 a,clmsshiftroster".$mmonth1."  b  set a.sshift=b.shift where a.employeeid=b.employeeid and b.dt='".$Sdate."'";  
			$result = DB_query($sql,$db);
			
			$sql = "insert into TempT2(employeeid,type) values('','Duty')";  
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(employeeid,type) values('','Duty')"; 
			$result = DB_query($sql,$db);

	/*
		$sql = "insert into TempT2(type) values('OT')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type) values('OT')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type) values('ABSENT')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type) values('ABSENT')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type) values('ABSENT')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type) values('ABSENT')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type) values('ABSENT')";
			$result = DB_query($sql,$db);
  */
		
//echo $sql ;
		 
	 }	
if($mshift=='A'){
 
 $intime=7.40;
} 
if($mshift=='B'){
 $intime=15.30;
} 
if($mshift=='C'){
 $intime=23.40;
} 
$kkdept='ALL';
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

			$sql = 'create temporary table tmp SELECT employeeid,lastname,section FROM clmsemployeemaster where orgunit="Kancor" and active=0 and trim(dept)="'.$kkdept.'"';

			if($kkdept=='ALL'){ 
				$sql = 'create temporary table tmp SELECT employeeid,lastname,section FROM clmsemployeemaster where orgunit="Kancor" and active=0';
			}
			
//echo $sql;	  
			$result = DB_query($sql, $db);
			$sql = 'insert into tmp(section) values("Helper")';
//echo $sql;	  
			$result = DB_query($sql, $db);
			
//echo $sql;	 

/*
		$sql = "select recordid,slno,type,position,employeeid,employeename,hrs from TempT1";
        $sql = "select recordid,slno,position,employeeid,employeename,hrs,category as'Act Code',type as'Alw Code' from TempT2";
        $sql = "select recordid,slno,position,employeeid,employeename,hrs from TempT2";  
echo $sql;
		$DbgMsg = _('The SQL that was used to insert the employee but failed was');
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
echo $sql;

			$msln=1 ;
		 while ($myrow03 = DB_fetch_row($result03))   
			{
				$mrid=$myrow03[0];
				$sql1 = "update  TempT2 set slno=".$msln." where  recordid=".$mrid." and slno=0";
				$result1 = DB_query($sql1,$db);
				$msln=$msln+1;
			} 
				$sql1 = "update  TempT2 set slno=ordno";
				//$result1 = DB_query($sql1,$db);
*/
//echo $sql1; 
		
//$sql = "select Slno,type,position,employeeid,employeename,hrs from TempT1";
$sql="select count(employeeid) from TempT2 where employeeid>0 and substr(type,1,4)='Duty'"  ;
//echo $sql;
 $result031 = DB_query($sql, $db);
 //echo $sql ;
   $myrow031 = DB_fetch_row($result031);
	$mpresent=$myrow031[0];
	$sql="select count(employeeid) from TempT2 where employeeid>0 and substr(type,1,6)='ABSENT'";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $myrow03 = DB_fetch_row($result03);
	$mabsent=$myrow03[0];
$sql="select count(employeeid) from TempT2 where employeeid>0 and substr(type,1,2)='OT'";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $myrow03 = DB_fetch_row($result03);
	$mextrawork=$myrow03[0];
//echo $sql;
	 /*
			$sql = "insert into TempT2(type,employeeid) values('OT','OT1')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type,employeeid) values('OT','OT2')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type,employeeid) values('ABSENT','ABS1')";
			$result = DB_query($sql,$db);
			$sql = "insert into TempT2(type,employeeid) values('ABSENT','ABS2')";
			$result = DB_query($sql,$db);
*/	
$sql="create temporary table t3 select *, (@row_number:=@row_number + 1) AS 'num' from TempT2,  (SELECT @row_number:=0) AS t"; 
//$sql="select recordid,slno from TempT2 ";
$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update t3 set slno=num"; 
$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);


/*
$sql="select employeeid,slno,recordid, (@row_number:=@row_number + 1) AS 'num' from TempT2,  (SELECT @row_number:=0) AS t"; 
//$sql="select recordid,slno from TempT2 ";
$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 
 $ksln=1; 

 while ($myrowp = DB_fetch_row($result))
 {
$sql2="update TempT2 set slno=".$ksln." where employeeid='".$myrowp[0]."'";
 $sql2="update TempT2 set slno=".$ksln." where $ksln='".$myrowp[3]."'";
//$sql2="update TempT2 set slno=".$ksln." where recordid=".$myrowp[0]."";
 $result2 = DB_query($sql2, $db, $ErrMsg, $DbgMsg);
 $ksln=$ksln+1;
echo "<br>Row no".$myrowp[3];
 }  
*/	 
	//echo $sql;
//$sql = "select recordid,slno,position,employeeid,employeename,hrs,category,type from TempT2";
$sql = "select employeeid,Employeename,Type,HRS AS'Hours',Section as 'Working Section',Grade as'Grade',Actgrade,Spice,Manning,Timein,sshift from TempT2"; 
$sql = "select employeeid,Employeename,sshift as'Sc.Shift',Type,HRS AS'Hours',Section as 'Working Section',Grade as'Grade',Actgrade,Spice,Manning,Timein,slno,yarea from TempT2"; 
$sql = "select employeeid,Employeename,sshift as'Sc.Shift',Type,HRS AS'Hours',Section as 'Working Section',Grade as'Grade',Actgrade,Spice,Manning,Timein,slno,yarea from t3"; 
//echo $sql;
 

    $DbgMsg = _('The SQL that was used to insert the employee but failed was'); 
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $column_count = mysql_num_fields($result03)  ;
	
	echo "<FORM METHOD='post' id='f1' ACTION='manpowerentryapproval1.php'"; 
//	echo "<FORM METHOD='post' id='f1' ACTION='notyetkancor.php'";

   // echo '<br><br><A HREF="exporttoxlspayregister.php?">Export to xls</A>';
    echo '<br> <div style="padding-bottom: 50px;">';
$mheading="Contract Labour Engagement Approval -".$msection;
include('intendcal.php');

if($mshift=='A'){
  $mintend=$myrowr[0];	
}
if($mshift=='B'){
  $mintend=$myrowr[1];	
}
if($mshift=='C'){
  $mintend=$myrowr[2];	
}

 //  echo'<H3>'.$mheading.'</h3>';
  //  echo 'Prode Code'. $KraType  .'  Name '. $mprodname;
   	echo "<br><center><div class='headtable' style='border-radius:10px;box-shadow:5px 8px #122518; text-decoration: underline;font-weight:bold; color:#092429; background-color:White;width:50%'>" ;
     echo '<span>Date : </span>'.$Sdate.'<span> Shift : </span>' .$mshift.'<span>  </span>'.$mwc ; 
//	 echo'<br><span>Intend : </span>'.$mintend.'  <span>Present : </span>'.$mpresent.'<span> Absent : </span>'.$mabsent.'<span> Over Time : </span>'.$mextrawork;
	 echo'<br><span>Intend : </span>'.$mintend.'  <span>Present : </span>'.$mpresent;
	 echo "</div></center><br><br>";
    
   echo'<left><table id="tbl1" Border="1" width ="25%"  cellspacing="0" cellpaddin="0">'; 
$column_count=$column_count-2 ; 
    print("<TR>");
	echo "<th colspan='".($column_count)."'>";
    echo'<p>'.$mheading.'</p></th></tr><tr>';

    for($column_num = 0; $column_num < $column_count; $column_num++) 
    {
        $field_name = mysql_field_name($result03, $column_num);
		if($field_name =='employeeid'){
           print("<TH  width=''10%>Employee ID</TH>\n") ;
		}else{
			
			if(($column_num>=2 and $column_num<=3) or ($column_num>=6 and $column_num<=10) ){
       print("<TH hidden width=''10%>$field_name</TH>\n");
				
			}else{
	       print("<TH width=''10%>$field_name</TH>\n");
			
			}
         }
	}
//	echo '<TH>Select New Code</TH>';
    print("</TR>");     
    while ($myrow03 = DB_fetch_row($result03))  
    {
		      $mcount=  $myrow03[11];  
  //    echo "Mcount".$mcount.$myrow03[1];	  

		$myrow03[90]=trim($myrow03[9])+(strlen($myrow03[1])/100);
	
    if(strlen($myrow03[90])==3){
		$myrow03[90]=($myrow03[9]+.01); 
	}
   	if(strlen(trim($myrow03[1]))==0){
			$myrow03[90]='';
		}
//	   echo $myrow1[1];  
        //echo '<tr>';
		
	   if($mshift=='A' and ($myrow03[2]<>'1'  )){
		  if(trim($myrow03[2])<>''){ 
           print("<TR bgcolor='red'>");
		  }
       } else{
        print("<TR>") ;
       }		
/*     	
		print("<TD><center>$myrow03[0]</center></TD>\n");
          //  echo "<TD><input  type='text1' name='fromtime$mcount' value='" . $myrow03[1] . "'>";
			echo "<TD><SELECT  style='text-align:center; id='prd' NAME='prodname$mcount'>"; 
	//		$sql = "SELECT distinct prodcode, prodname FROM catlmfgspecmaster where prodcode !=''";
			if ($myrow03[1] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[1].'">'.$myrow03[1];
			} 

			$sql = 'SELECT distinct column2,column3,column14 FROM catlmfgequivalentmaster where column14 !="" order by column3';
			$result = DB_query($sql, $db);
		 
						  echo "<OPTION   VALUE='  '>" ;

			while ($myrow = DB_fetch_array($result)) {

	//			echo "<OPTION VALUE=" . $myrow['prodcode'] . ">" . $myrow['prodname'];
				  echo '<OPTION VALUE="' . $myrow['column3'] . '">' . $myrow[1]."  ". $myrow['column2'];

			} //end while loop

			 echo "</SELECT></TD>";
      */
	  
 //          echo "<TD>" . $myrow03[0] . "</TD>";
  //         echo "<TD><input  type='text1'  name='slno$mcount'  value='" . $myrow03[1] . "' readonly>";
 //          echo "<TD><input  type='text1' name='posi$mcount'  value='" . $myrow03[2] . "' readonly>";
           echo "<TD><center><input style='width:50%'  readonly type='text1' name='empid$mcount'  value='" . $myrow03[0] . "'></center></td>"  ;
  		   echo "<TD><SELECT  style='text-align:center; id='prdd' name='empn$mcount'>"; 
			if ($myrow03[1] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[0].'">'.$myrow03[0].$myrow03[1];
			} 

		//	$sql = 'SELECT employeeid,lastname FROM clmsemployeemaster where orgunit="CATL" and active=0';
		     $sql = 'SELECT employeeid,lastname FROM tmp where employeeid>10 order by 1000000-employeeid desc';
			$result = DB_query($sql, $db);
		 
						  echo "<OPTION   VALUE='  '>" ;

			while ($myrow = DB_fetch_array($result)) {

		//		  echo '<OPTION VALUE="'.substr($myrow[0],0,4). $myrow[1].'">' .substr($myrow[0],0,4)."  ". $myrow[1];
				  echo '<OPTION VALUE="'.$myrow[0].'">' . $myrow[1];  

			} //end while loop

			 echo "</SELECT></TD>";


		   //         echo "<TD><input  type='text1' name='empn$mcount'  value='" . $myrow03[4] . "'>";
  //         echo "<TD><input  type='text1' name='type$mcount'  value=" . $myrow03[2] . ">";  
 //   echo "<TD><input readonly type='text1' name='sshift$mcount'  value=" . $myrow03[10] . ">";
    echo "<TD hidden align='center'><input style='width:30%' readonly type='text' name='sshift$mcount' value=" . $myrow03[2] . ">";

  echo  "<TD hidden><SELECT id ='type' NAME='type$mcount' >";  
  			if ($myrow03[3] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[3].'">'.$myrow03[3];
			} 

	echo  "	<OPTION VALUE='Duty'>Duty"; 
	echo  "	<OPTION VALUE='DutyA'>Aditional Duty"; 
	echo  "<OPTION VALUE='OT'>OT";
	echo  "<OPTION VALUE='ABSENT'>ABSENT";
//	echo  "<OPTION VALUE='LEAVE'>LEAVE";  
		   
	echo  "</TD></SELECT>  ";	  

           echo "<TD><center><input style='width:60%' type='text1' name='hrs$mcount'  value=" . $myrow03[4] . ">" ; 
 
   		   echo "</center><TD><SELECT  style='text-align:center; id='sec' name='sec$mcount'>"; 
			if ($myrow03[5] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[5].'">'.$myrow03[5];
			} 
 
		//	$sql = 'SELECT employeeid,lastname FROM clmsemployeemaster where orgunit="CATL" and active=0';
		    // $sql = 'SELECT distinct section  FROM usersection where employeeid="CRK1020"';
		     $sql = 'SELECT distinct area  FROM clmssectionmaster';
			$result = DB_query($sql, $db);
		 
						  echo "<OPTION   VALUE='  '>" ;

			while ($myrow = DB_fetch_array($result)) {

				  echo '<OPTION VALUE="'. $myrow[0].'">' . $myrow[0];

			} //end while loop

			 echo "</SELECT></TD>";
 
//echo  "<TD>".$myrow03[5]."</TD>"; 

           echo "<TD hidden><center><input readonly style='width:80%' type='text1' name='grade$mcount'  value=" . $myrow03[6] . "></center></td>";
/*
 echo  "<TD><SELECT id ='gradet' NAME='grade$mcount' >"; 
			if ($myrow03[5] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[5].'">'.$myrow03[5];
			} 
	echo  "	<OPTION VALUE=''>"; 
	echo  "<OPTION VALUE='SWCM'>SWCM";
	echo  "<OPTION VALUE='WCM'>WCM";  
	echo  "<OPTION VALUE='SG'>SG";  
	echo  "<OPTION VALUE='CM'>CM";  
	echo  "<OPTION VALUE='G1'>G1";  
	echo  "<OPTION VALUE='G2'>G2";  
	echo  "<OPTION VALUE='G3'>G3";  
	echo  "<OPTION VALUE='WTR'>WTR";  
		   
	echo  "</TD></SELECT>  ";	  


 echo  "<TD><SELECT id ='act' NAME='act$mcount' >"; 
			if ($myrow03[7] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[7].'">'.$myrow03[7];
			} 
	echo  "	<OPTION VALUE=''>"; 
	echo  "<OPTION VALUE='1A'>1A-G2";
	echo  "<OPTION VALUE='1B'>1B-G1";  
	echo  "<OPTION VALUE='1C'>1C-SG";  
	echo  "<OPTION VALUE='1D'>1D-CM";  
		   
	echo  "</TD></SELECT>  ";	  
 */
 echo "<TD hidden><center><input style='width:50%'  id='act' type='text1' name='act$mcount' readonly  value='" . $myrow03[7] . "'></center></td>"  ;

  echo  "<TD hidden><center><SELECT id ='spic' NAME='spic$mcount' >"; 
			if ($myrow03[8] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[8].'">'.$myrow03[8];
			} 
	echo  "	<OPTION VALUE=''>"; 
	echo  "<OPTION VALUE='ASH'>ASH";
	echo  "<OPTION VALUE='CGBAGS'>CG No of Bags";  
	echo  "<OPTION VALUE='DISTRUBANCE'>DISTRUBANCE";  
	echo  "<OPTION VALUE='MUSTARD'>MUSTARD";  
	echo  "<OPTION VALUE='T14'>T14";  
	echo  "<OPTION VALUE='ETP'>ETP"; 
	echo  "<OPTION VALUE='DISCHARGE'>DISCHARGE";  
	echo  "<OPTION VALUE='CHILLY'>CHILLY";  
	echo  "<OPTION VALUE='CHILLY+ASH'>CHILLY+ASH";  
	echo  "<OPTION VALUE='CHILLY+DISTRUBANCE'>CHILLY+DISTRUBANCE";  
	echo  "<OPTION VALUE='CHILLY+MUSTARD'>CHILLY+MUSTARD";  
	echo  "<OPTION VALUE='CHILLY+T14'>CHILLY+T14";  
	echo  "<OPTION VALUE='CHILLY+ETP'>CHILLY+ETP";  
	echo  "<OPTION VALUE='CHILLY+DISCHARGE'>CHILLY+DISCHARGE";  
		   
	echo  "</TD></SELECT>  ";	  
/*
	echo  "<TD><SELECT id ='man' NAME='man$mcount' >"; 
			if ($myrow03[9] <> ''){
				echo '<OPTION SELECTED VALUE="'.$myrow03[9].'">'.$myrow03[9];
			} 
	echo  "	<OPTION VALUE=''>     "; 
	echo  "<OPTION VALUE='M2'>M2";
		   
	echo  "</TD></SELECT>  ";
*/	
echo "<TD hidden><center><input style='width:50%'  id='man' type='text1' name='man$mcount' readonly value='" . $myrow03[9] . "'></center></td>"  ;
	
    echo "<TD hidden><center><input readonly style='width:70%'  type='text1' name='timein$mcount'  value=" . $myrow03[10] . "></center></td>";
 
	//	echo "<TD>" . $myrow03[9] . "</TD>";
  // echo "<TD>" . $myrow03[11] . "</TD>";
//	    echo "<TD>" . $mcount . "</TD>";


	//	echo "<TD><input  type='text1' name='cat$mcount'  value=" . $myrow03[6] . ">";
     //      echo "<TD><input  type='text1' name='typ$mcount'  value=" . $myrow03[7] . ">";
     //      echo "<TD><input  type='text1' name='typ$mcount'  value=" . $myrow03[8] . ">";
           echo "<TD hidden><input  type='text1' name='empid$mcount'  value='" . $myrow03[0] . "'>"  ;

        echo'</tr>';  
		
			echo "<INPUT TYPE='hidden' NAME='ans' VALUE='Yes'>";
			echo "<INPUT TYPE='hidden' NAME='Sdate' VALUE='".$Sdate."'>";
			echo "<INPUT TYPE='hidden' NAME='Edate' VALUE='".$Edate."'>";
			echo "<INPUT TYPE='hidden' NAME='msln' VALUE='".$msln."'>";
			echo "<INPUT TYPE='hidden' NAME='mwc' VALUE='".$mwc."'>";
			echo "<INPUT TYPE='hidden' NAME='mshift' VALUE='".$mshift."'>";
			echo "<INPUT TYPE='hidden' NAME='totcount' VALUE='".$mcount."'>";
  	if(trim($myrow03[12])<>''){
      $msubmitted='Y';
	  $msubmittedap='Y';
	}
	}	
	 echo'</table>';
    echo '</div>';
$cd=date('d-m-Y',strtotime("-1 days"));
if($Sdate<$cd){
	if(substr($Sdate,0,2)<>'01'){
	  $msubmitted='Y';
	}
}
//echo 'Area code'. $marea;
    if($marea==''){
	  $msubmitted='';	
	}
//	if($msubmitted==''){
	if($msubmittedap==''){
	echo "<p><center><INPUT TYPE='submit' NAME='submit2' VALUE='" . _('Save and Preview') . "'>"; 
 	echo "<p><center><INPUT TYPE='submit' NAME='submit3' VALUE='" . _('Submit') . "'>"; 
    }else{
 	 //echo "<p><center><INPUT readonly TYPE='submittt' NAME='submit4' VALUE='" . _('Submitted') . "'>"; 
    }	
	echo '</FORM>';
	echo "<br><br><br>"; 

}	
?>

				