<?php
$PageSecurity = 10;
include('includes/sess.inc');
//$title = _('Staff Pay Register Any Month');
include('includes/headerkancor.inc');
//include('PeriodSetting.php');
//include('GetPayrollPeriod.php');
	
?>

<body>
<center><table>  
	<form name="import"  method="post" enctype="multipart/form-data">

 <?php
 $empnoflg='';
 if($empnoflg==""){
   echo' <TR><TD><align=right><b>Employee No</b></TD><TD><INPUT TYPE="text" x-webkit-speech NAME="empno" ></TD></TR>'; 
 }

	 ?>
    <br></tr><TR><TD></TD><TD align="center"><input type="submit" name="submit" action="payslipany1.php" value="Submit" /></td></tr>
    </form>
	</table></center>
	
	<?php
	if(isset($_POST["submit"]))
	{
		$mno = $_POST['empno'];
		
 //      echo "The Selected Month is ".$pmonth;

		$sql = 'select sepdate,sepreason,lastname from clmsemployeemaster  where employeeid="'.$mno.'"';
	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);
	$msepdt=$myrow[0];
	$msepreason=$myrow[1]; 
	$mname=$myrow[2]; 
	
//	echo $msepdt;
	?>

<body>
<center><table>  
	<form name="import1"  method="post" action="separationupdation.php" enctype="multipart/form-data">

 <?php
 echo'<h4>'.$mname.'</h4>';
 echo' <TR><TD><align=right><b>Separation Date</b></TD><TD><INPUT TYPE="text" x-webkit-speech NAME="sepdt"value="'.$msepdt.'" ></TD>';
 echo '<TD><align=right><b>format (dd/mm/yyyy)</b></TD></TR>'; 
 echo' <TR><TD><align=right><b>Separation Reason</b></TD><TD><INPUT TYPE="text" x-webkit-speech NAME="sepreason"value="'.$msepreason.'" ></TD></TR>'; 
 echo' <TR><TD><INPUT TYPE="hidden"n x-webkit-speech NAME="mno"value="'.$mno.'" ></TD></TR>'; 
 	 ?>
    <br></tr><TR><TD></TD><TD align="center"><input type="submit" name="submit1" action="separationupdation.php"  value="Update" /></td></tr>
    </form>
	</table></center>
	
	<?php 
	if(isset($_POST["submit1"]))
	{
      // echo "I am Going to update";
	}
	
	}  
	?>	