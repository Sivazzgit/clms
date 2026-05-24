<?php
/* vars for export */
// database variables
$hostname = "localhost";
$user = "kancor";
$password = "kancor123";
$database = "kancor";
// Database connecten voor alle services
mysql_connect($hostname, $user, $password)
or die('Could not connect: ' . mysql_error());
					
mysql_select_db($database)
or die ('Could not select database ' . mysql_error());
// create var to be filled with export data
/*
echo '<br>rrrrrrrrrrrr';
$sql='select * from dailybackuptables';
$result=mysql_query($sql);
while($myrow = mysql_fetch_array($result)) {

echo '<br>'. $myrow[1];
$db_record = trim($myrow[1]); 

include('dailybackupcsv1.php');
}
*/
//$mmonth='0321';
$cd=date('d-m-Y',strtotime("-1 days"));
$Sdate=$cd;
$mmonth=substr($Sdate,3,2).substr($Sdate,8,2);
echo 'Month='.$mmonth;

$db_record ='manpower'.$mmonth; 
$mfn='kancormanpower';
include('dailybackupcsv1kancor.php');

?>
