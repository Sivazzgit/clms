<?php

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Employee Master Data Upload');


?>







<body>
<center><table>  
	<form name="import" method="post" enctype="multipart/form-data">
    <tr><td>	<input type="file" name="file" /><br />

        <tr><td><input type="submit" name="submit" value="Submit"> and Wait for 5 minutes</td></tr>
    </form>
	</table></center>
	
<?php
$mmonth = $_POST['month']; 
//echo'rrrrr';
//$mmonth='0316';

echo '<left><a href="hiring.csv">' . _('Employee Master Data Upload Format...') . '</a><BR>';
echo '<left><a href="">' . _('Employee Master Data Upload Sheet XLS  Save as .csv(coma delimited....') . '</a><BR>';

echo'<P><left><INPUT Type="button" VALUE="Back" onClick="history.go(-1);return true;"><br>';
echo '<left><a href="">' . _('Please wait for 5 minutes as the process is going on)') . '</a><BR>';

	//include ("connection.php");
	
	if(isset($_POST["submit"]))
	{
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

		$sql="create temporary table tupload like dataupload";
	//	$sql="create temporary table tupload select column0,column1,column2,column3,column4,column5,column6,column7,column8,column9,column10,column11,column12,column13,column14,column15,column16,column17,column18,column19,column20,column21,column22  from catlmfgproductiondataupload";
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
/*
			$name17= substr($name1,0,10);
			$name18= substr($name1,11,2);
			$name19= substr($name1,14,2);
	        $name20=$name8.".".$name9;	
*/	
	//	echo $name0.$name1.$name2.$name3;
		//	echo'<br>';
			$sql="insert into tupload(column0,column1,column2,column3,column4,column5,column6,column7,column8,column9,column10,column11,column12,column13,column14,column15,column16,column17,column18,column19,column20,column21,column22) values('$name0','$name1','$name2','$name3','$name4','$name5','$name6','$name7','$name8','$name9','$name10','$name11','$name12','$name13','$name14','$name15','$name16','$name17','$name18','$name19','$name20','$name21','$name22')";
			$result = DB_query($sql,$db);
}

	


 $md="delete from  tupload where trim(column0)='EMP NO'  ";
 	$result = DB_query($md,$db);
  $md="update tupload set column19=concat(substr(column2,7,4),'-',substr(column2,4,2),'-',substr(column2,1,2))";
 	$result = DB_query($md,$db);
$md="update tupload set column20=concat(substr(column3,7,4),'-',substr(column3,4,2),'-',substr(column3,1,2))";
 	$result = DB_query($md,$db);
/*	
 $md="drop table westernemployeemaster1019 ";
 	$result = DB_query($md,$db);
 $md="create table westernemployeemaster1019 like westernemployeemaster1019old ";
$result = DB_query($md,$db);
*/	
 $md2="select * from tupload  ";
 		$result2 = DB_query($md2,$db);
		while ($myrow2 = DB_fetch_row($result2)) {
			$mempno=trim($myrow2[1]);
				$md3="select count(*) from clmsemployeemaster where trim(employeeid)='".$mempno."'";
				$result3 = DB_query($md3,$db);
				while ($myrow3 = DB_fetch_row($result3)) {
                  if($myrow3[0]==0){   				 
				    echo"Record Not found in clmsemployeemaster";
				    $md4="insert into clmsemployeemaster(employeeid) values('".$mempno."')";
					$result4 = DB_query($md4,$db);
					    echo"Record inserted in clmsemployeemaster employeeid=".$mempno;

				 
				   }else {
				  echo"Record Existes in clmsemployeemaster";
				  $md5 = "UPDATE clmsemployeemaster SET
					    oldemployeeid='" . DB_escape_string(trim($myrow2[2])) . "',
					    lastname='" . DB_escape_string(trim($myrow2[3])) . "',
                        dept ='". DB_escape_string(trim($myrow2[4])) . "',
                        birthdate ='". DB_escape_string(trim($myrow2[6])) . "',
                        hiredate ='". DB_escape_string(trim($myrow2[7])) . "',
                        address1 ='". DB_escape_string(trim($myrow2[8])) . "',
                        phone1 ='". DB_escape_string(trim($myrow2[9])) . "',
                        esino ='" . DB_escape_string(trim($myrow2[10])) . "',
                        pfno='" . DB_escape_string(trim($myrow2[11])) . "',
                        aadhar='" . DB_escape_string(trim($myrow2[12])) . "',
				      	orgunit='Kancor'

					
						WHERE employeeid = '".$mempno."'";
						$result5 = DB_query($md5,$db);
 echo $md5;
				  }
				}
		
		}
		
	
 
$md="select * from tupload  ";
 		$result = DB_query($md,$db);
$column_count = mysql_num_fields($result);
 
$md="select * from tupload  ";
 		$result = DB_query($md,$db);
echo'<div>';


echo'<H3>Employee Master Data Upload </h3>';
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



$md="select employeeid,lastname,gender,hiredate,birthdate,dept,position,costcenterid,grade,section,offday,phone1,email1,address1,zip,city,state,country from clmsemployeemaster where orgunit='Kancor' ";
 		$result = DB_query($md,$db);
$column_count = mysql_num_fields($result);
 
 		$result = DB_query($md,$db);
echo'<div>';


echo'<H3>Employee Master Data Updated </h3>';
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

			
	}
	
?>
    
 
  

