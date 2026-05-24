<?php
/* $Revision: 1.0 $ */

//$PageSecurity = 15;

//$PageSecurity = 10;  
//include('includes/sess.inc');
//$title = _('Shift Wise Labour Intend');
//include('includes/headerclms.inc');
/* 
if($_SESSION['UserID']=='rmsuser'){
	include('mfgheader2.php');
}else{
	include('mfgheader.php');

}
*/
//$Sdate='10-09-2021';
//$kkdept='CARE & CONCERN';
if(!$kkdept){
 $kkdept='';   
}
$sqlr="select sum(ashift),sum(bshift),sum(cshift) from clmsintend where vendor='".$kkdept."' and STR_TO_DATE('".$Sdate."','%d-%m-%Y')  between str_to_date(stdate,'%d-%m-%Y') and str_to_date(endate,'%d-%m-%Y') group by vendor";
$sql6int="create temporary table tint00 select section,sum(ashift) as'Ashift',sum(bshift)  as'Bshift',sum(cshift) as'Cshift' from clmsintend where vendor='".$kkdept."' and STR_TO_DATE('".$Sdate."','%d-%m-%Y')  between str_to_date(stdate,'%d-%m-%Y') and str_to_date(endate,'%d-%m-%Y') group by vendor,section";

if($_SESSION['UserID']=='clmsadmin'){
$sqlr="select sum(ashift),sum(bshift),sum(cshift) from clmsintend where  STR_TO_DATE('".$Sdate."','%d-%m-%Y')  between str_to_date(stdate,'%d-%m-%Y') and str_to_date(endate,'%d-%m-%Y') ";
$sql6int="create temporary table tint00 select section,sum(ashift) as'Ashift',sum(bshift) as'Bshift',sum(cshift) as'Cshift' from clmsintend where vendor='".$kkdept."' and STR_TO_DATE('".$Sdate."','%d-%m-%Y')  between str_to_date(stdate,'%d-%m-%Y') and str_to_date(endate,'%d-%m-%Y') group by section";
}	

//echo $sqlr;
//echo $sql6int;
$resultr = DB_query($sqlr, $db); 
$myrowr = DB_fetch_array($resultr);
//echo "B Shift Nos=".$myrowr[1];

$result6int = DB_query($sql6int, $db); 

?>