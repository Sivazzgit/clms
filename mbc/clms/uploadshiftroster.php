<?php

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Shift Roster Updation');
include('includes/headerclms.inc');
echo '<h4><left><a href="Shift Roster format.csv">' . _('Shift Roster Upload Format.csv..') . '</a></left></h4><BR>'; 
$_POST['month']='';

?>







<body>
<center><table>  
	<form name="import" method="post" enctype="multipart/form-data">
    <tr><td>	<input type="file" name="file" /><br />

        <tr><td><input type="submit" name="submit" value="Submit"> </td></tr>
    </form>
	</table></center>
	
<?php
	if(isset($_POST["submit"])){

$mmonth = $_POST['month']; 
//echo'rrrrr';
//$mmonth='0520';

//echo '<left><a href="">' . _('Employee Master Data Upload Sheet XLS  Save as .csv(coma delimited....') . '</a><BR>';

//echo'<P><left><INPUT Type="button" VALUE="Back" onClick="history.go(-1);return true;"><br>';
//echo '<left><a href="">' . _('Please wait for 5 minutes as the process is going on)') . '</a><BR>';

	//include ("connection.php");
	
//	if(isset($_POST["submit"]))
//	{
$kmonth1=substr($mmonth,0,2).'-20'.substr($mmonth,2,2);

		//echo "rrrrrrrrrrrrr";
		$file = $_FILES['file']['tmp_name'];
		echo 'File Name '.$file;

		$handle = fopen($file, "r");
		$c = 0;
		//echo "My File name  is ".$file;
		    $column_count = 34;
    //        echo 'No of Columns = '.$column_count;
//		  $sql="create temporary table tprodupload as (select *, '     ' as'shift', '              ' as 'column17','             ' as 'column18','               ' as 'column19','             ' as 'column20' ,'             ' as 'column21','            ' as 'column22' from catlmfgdelayupload limit 1)";

//		$sql="create temporary table tupload like dataupload";
		$sql="create temporary table tupload0 select column0,column1,column2,column3,column4,column5,column6,column7,column8,column9,column10,column11,column12,column13,column14,column15,column16,column17,column18,column19,column20,column21,column22
		,column22 as'column23',column22 as'column24',column22 as'column25',column22 as'column26',column22 as'column27',column22 as'column28',column22 as'column29'
		,column22 as'column30',column22 as'column31',column22 as'column32',column22 as'column33',column22 as'column34',column22 as'column35',column22 as'column36'
		,column22 as'column37',column22 as'column38'  from dataupload";
		$result = DB_query($sql,$db);
		while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
         
		{
			//$kmonth1='03-2016';
			$name0 = $filesop[0]; 
			$name1 = $filesop[1];
			$name2 = $filesop[2];
			$name3 = $filesop[3];
			$name4 = $filesop[4];
			$name5 = $filesop[5];
			$name6 = $filesop[6];
			$name7 = $filesop[7];
			$name8 = $filesop[8];
			$name9 = $filesop[9];
			$name10 = $filesop[10];
			$name11 = $filesop[11];
			$name12= $filesop[12];
			$name13= $filesop[13];
			$name14= $filesop[14];
			$name15= $filesop[15];
			$name16 = $filesop[16];
			$name17 = $filesop[17];
			$name18 = $filesop[18];
			$name19 = $filesop[19];
			$name20 = $filesop[20];
			$name21 = $filesop[21];
			$name22 = $filesop[22];
			$name23 = $filesop[23];
			$name24 = $filesop[24];
			$name25 = $filesop[25];
			$name26 = $filesop[26];
			$name27 = $filesop[27];
			$name28 = $filesop[28];
			$name29 = $filesop[29];
			$name30 = $filesop[30];
			$name31 = $filesop[31];
			$name32 = $filesop[32];
			$name33 = $filesop[33];
			$name34 = $filesop[34];
			$name35 = $filesop[35];
			$name36 = $filesop[36];
			$name37 = $filesop[37];
			$name38 = $filesop[38]; 
/*
			$name17= substr($name1,0,10);
			$name18= substr($name1,11,2);
			$name19= substr($name1,14,2);
	        $name20=$name8.".".$name9;	
*/	
	//	echo $name0.$name1.$name2.$name3;
		//	echo'<br>';
			$sql="insert into tupload0(column0,column1,column2,column3,column4,column5,column6,column7,column8,column9,column10,column11,column12,column13,column14,column15
			,column16,column17,column18,column19,column20,column21,column22,column23,column24,column25,column26,column27,column28,column29,column30,column31,column32,column33
			,column34,column35,column36,column37,column38
			) values('$name0','$name1','$name2','$name3','$name4','$name5','$name6','$name7','$name8','$name9','$name10','$name11','$name12','$name13','$name14','$name15'
			,'$name16','$name17','$name18','$name19','$name20','$name21','$name22','$name23','$name24','$name25','$name26','$name27','$name28','$name29','$name30'
			,'$name31','$name32','$name33','$name34','$name35','$name36','$name37','$name38')";
			$result = DB_query($sql,$db);
}

	
 $md="select column1,column3 from tupload0 where trim(column0)='Period From'  ";
 $result = DB_query($md,$db);
 $myrow = DB_fetch_row($result);
 $msdate=trim($myrow[0]);
 $medate=trim($myrow[1]);
 $smonth=substr($msdate,3,2).substr($msdate,8,2);
 $emonth=substr($medate,3,2).substr($medate,8,2);
 $mmyy=substr($msdate,2,8);
 $msd=substr($msdate,0,2);
 $med=substr($medate,0,2);
 $mdiff=$med-$msd;
echo "Date From".$msdate;

 $kk=0;
 $sql2='select column0,column1,column2,column3,column4,column5,column6,column7';
// for($kk=$msd;$kk<$med;$kk++){ 
 for($kk=8;$kk<(8+$mdiff);$kk++){ 
 //echo $kk;	 
	//$sql2=$sql2.',column'.($kk+7); 
	$sql2=$sql2.',column'.($kk); 
 }	 
 $mfrdt=date('d-m-Y',strtotime($msdate));
 
 //$mfstdt=date('01-m-Y',strtotime($medt)); //$fstdt=$medt1;
 $mfstdt=date('01-m-Y',strtotime("-0 days"));
 
 if($smonth ==''){
	Exit("<br>Please Fill Up the Date From Column"); 
 }
 if($emonth ==''){
	Exit("<br>Please Fill Up the Date To Column"); 
 }
 if($smonth <> $emonth){
	Exit("<br>Please upload only one month data at a time"); 
 }
 if($mfrdt<$mfstdt){
	//Exit("<br>Previous Month Data Upload?..Please contact Admin"); 
 }
 
$mmonth=$smonth; 
 echo '<br> Start Date='.$msdate.'----'.$smonth.'--- Start dt'.$msd .'MMYY='.$mmyy;
 echo '<br> End Date='.$medate.'----'.$emonth.'--- End dt'.$med.'---Diff'.$mdiff ;
 
 echo '<br> From Date='.$mfrdt; 
 echo '<br> First date of Current month='.$mfstdt;
 $sql3=$sql2.' from tupload0';
 //echo '<br> SQL ='.$sql3; 

 $md="delete from  tupload0 where trim(column0)='Employee No'  ";
 	$result = DB_query($md,$db);
    $sql="CREATE TABLE if not exists clmsshiftroster".$mmonth." like  shiftroster0520";
	//$result0 = DB_query($sql,$db,$ErrMsg);


$md=$sql3;
$result01 = DB_query($md,$db);
while ($myrow01 = DB_fetch_row($result01)) {
$mno=trim($myrow01[0]);	
$mgrade=trim($myrow01[2]);
$msection=trim($myrow01[3]);
$mshiftgroup=trim($myrow01[4]);
$mnormal=trim($myrow01[5]);
$moffday=trim($myrow01[6]);
$msdd=$msd*1;
 echo"BBBBBBB".$msdd; 
	
$mdi=0;	
for($mdt=$msdd;$mdt<=$med;$mdt++){ 

	$mdi=$mdi+1;
        $kdt=$mdt;
        if($mdt<10){ 
			$kdt='0'.$mdt;
		}	
	$mdtt=$kdt.$mmyy;	
     $mcc=$mdi+6; 
     $msccd=$myrow01[$mcc];	
	 $msccd=trim($msccd);
 //    $mcomp= $_POST['compcode'.$mcntt];

	$sql0="select count(*) from clmsshiftroster".$mmonth." where employeeid='".$mno."' and dt='".$mdtt."'";	
//echo $sql0;
	 
 $result0 = DB_query($sql0,$db);
 $myrow0 = DB_fetch_row($result0);
//echo"<br>Myrow0=".$myrow0[0];
    if($myrow0[0]==0 and trim($mno)<>''){
	  $sql0p="insert into clmsshiftroster".$mmonth."(employeeid,dt) values('".$mno."','".$mdtt."')";	
   //echo $sql0p;
   $result0p = DB_query($sql0p,$db);
	 
	 }
    if(trim($mno) <>''){
	 $sql0pp="update clmsshiftroster".$mmonth." set  shift='".$msccd."' where 
	  employeeid='".$mno."' and dt='".$mdtt."'";
	 // echo '<br>'.$sql0pp;  	
      $result0pp = DB_query($sql0pp,$db);
	} 
	
	$sql0h="update clmsemployeemaster set section='".$msection."' where employeeid='".$mno."'";	
//echo $sql0;
	 
 //$result0h = DB_query($sql0h,$db);
 
	}	

		//echo $kdt.$mmyy;

 




}	
	
 //$md="delete from shiftroster".$mmonth;
 //	$result = DB_query($md,$db);
 //$md="insert into  shiftroster".$mmonth."(employeeid,Employeename,dt,Type,HRS,Section,Actgrade,Spice,Manning,shift,grade) select column12,column3,column4,column2,column13,column14,column8,column9,column10,column11,column15 from tupload0"; 
 //	$result = DB_query($md,$db);
 $md="update clmsshiftroster".$mmonth." set shift='1' where shift='A'";
 	$result = DB_query($md,$db);
 $md="update clmsshiftroster".$mmonth." set shift='2' where shift='B'";
 	$result = DB_query($md,$db);
 $md="update clmsshiftroster".$mmonth." set shift='3' where shift='C'";
 	$result = DB_query($md,$db);
 $md="update clmsshiftroster".$mmonth." set shift='4' where shift='G'"; 
 	$result = DB_query($md,$db);


	
/*	
	$md="create table westernemployeemaster1019 like westernemployeemaster1019old ";
$result = DB_query($md,$db);
	
 $md2="select * from tupload  ";
 		$result2 = DB_query($md2,$db);
		while ($myrow2 = DB_fetch_row($result2)) {
			$mempno=trim($myrow2[1]);
				$md3="select count(*) from manpower".$mmonth. where trim(employeeid)='".$mempno."'";
				$result3 = DB_query($md3,$db);
				while ($myrow3 = DB_fetch_row($result3)) {
                  if($myrow3[0]==0){   				 
				    echo"Record Not found in manpower".$mmonth;
				    $md4="insert into manpower".$mmonth."(employeeid) values('".$mempno."')";
					$result4 = DB_query($md4,$db);
					    echo"Record inserted in prlemployeemaster employeeid=".$mempno;

				 
				   }else {
				  echo"Record Existes in prlemployeemaster";
				  $md5 = "UPDATE prlemployeemaster SET
					    lastname='" . DB_escape_string(trim($myrow2[2])) . "',
                        grade ='". DB_escape_string(trim($myrow2[3])) . "',
                        offday ='" . DB_escape_string(trim($myrow2[4])) . "',
                        section='" . DB_escape_string(trim($myrow2[5])) . "',
				      	orgunit='Kancor'

					
						WHERE employeeid = '".$mempno."'";
						$result5 = DB_query($md5,$db);
 echo $md5;
				  }
				}
		
		}
		
*/	
 $md=$sql3;
//$md="select * from tupload0  ";
 		$result = DB_query($md,$db);
$column_count = mysql_num_fields($result);
 
//$md="select * from tupload0  "; 
 		$result = DB_query($md,$db);
echo'<div>';


echo'<H3>Employee Shift Roster Upload </h3>';
echo'<center><table Border="1" width ="25%"  cellspacing="0" cellpaddin="0">';

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
print("<TD>".date('d-m-Y',strtotime($myrow1[0]))."</TD>\n");
echo'</tr>';
		}		
//$recd='$myrow1[0]';
//echo $mmr;
//echo '<td>'.$myrow1[0].'</td><td>'.$myrow1[1].'</td><td>'.$myrow1[2].'</td><td>'.$myrow1[3].'</td><td>'.$myrow1[4].'</td>';
//echo'</tr>';
 //echo $mq2;

echo'</table></center>';
echo'</div>';


/*
//$md="select employeeid,lastname,gender,hiredate,birthdate,dept,position,costcenterid,grade,section,offday,phone1,email1,address1,zip,city,state,country from prlemployeemaster where orgunit='Kancor' ";

$md="select * from shiftoster".$mmonth;
 		$result = DB_query($md,$db);
$column_count = mysql_num_fields($result);
 
 		$result = DB_query($md,$db);
echo'<div>';


echo'<H3>Employee Shift Roster Updated </h3>';
echo'<center><table Border="1" width ="25%"  cellspacing="0" cellpaddin="0">';

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
//print("<TD>".date('d-m-Y',strtotime($myrow1[0]))."</TD>\n");
echo'</tr>';
		}		
//$recd='$myrow1[0]';
//echo $mmr;
//echo '<td>'.$myrow1[0].'</td><td>'.$myrow1[1].'</td><td>'.$myrow1[2].'</td><td>'.$myrow1[3].'</td><td>'.$myrow1[4].'</td>';
//echo'</tr>';
 //echo $mq2;

echo'</table></center>';
echo'</div>';

*/			
	}
	
?>
    
 
  

