<?php

$PageSecurity = 10;
include('includes/sess.inc');
$title = _('Punching Data Upload');

include('includes/headerclms.inc');
//include('payrollcontrol.php');

/*
	$hostname = "localhost";
	$username = "anahaw";
	$password = "anahaw";
	$database = "anahaw";


	$conn = mysql_connect("$hostname","$username","$password") or die(mysql_error());
	mysql_select_db("$database", $conn) or die(mysql_error());
*/
?>







<body>
<center><table>  
	<form name="import" method="post" enctype="multipart/form-data">
    <tr><td>	<input type="file" name="file" /><br />

        <tr><td><input type="submit" name="submit" value="Submit"></td></tr>
    </form>
	</table></center>
	
<?php
$mmonth = $_POST['month'];

//echo'rrrrr';
//$mmonth='0316';
echo '<left><a href="uploads/punchdataexpoerformat.csv">' . _('Take format ,paste new data from punching clocks save as .csv') . '</a><BR>';

echo'<P><left><INPUT Type="button" VALUE="Back" onClick="history.go(-1);return true;"><br>';

	//include ("connection.php");
	
	if(isset($_POST["submit"]))
	{
		
$flname=$_POST["file"];
echo "FLNAM".$flname;
	
$kmonth1=substr($mmonth,0,2).'-20'.substr($mmonth,2,2);
		    $sql="create temporary table temp1 like clmspunchingupload";
//echo $sql;
			$result = DB_query($sql,$db);

		//echo "rrrrrrrrrrrrr";
		$file = $_FILES['file']['tmp_name'];
		$filenam = $_FILES['file']['name'];
		echo 'File Name-1 '.$filenam;
		$handle = fopen($file, "r");
		$c = 0;
		//echo "My File name  is ".$file;
		    $column_count = 34;
    //        echo 'No of Columns = '.$column_count;
	//	    $sql="delete from clmspunchingupload";
	//		$result = DB_query($sql,$db);
		while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
         
		{
			//echo"CRK";
			//$kmonth1='03-2016';
			$name0 = $filesop[0];
			$name1 = $filesop[1];
			$name2 = $filesop[2];
			$name3 = $filesop[3];
			$name4 = $filesop[4];
			$name5 = $filesop[5];
			$name6 = $filesop[6];
			$name7= substr($name1,0,10);
			$name8= substr($name1,9,2);
			$name9= substr($name1,12,2);
		    if(strlen(trim($name1))<14)	
			{
				$name8= '0'.substr($name1,9,1);
    			$name9= substr($name1,11,2);
	
			}				 
 	        $name10=$name8.".".$name9;	
             
			$ydt= substr($name1,0,6).'20'.substr($name1,6,2);
            $ydt1=$ydt;
            $ydt=substr($ydt1,0,2).'-'.substr($ydt1,3,2).'-'.substr($ydt1,6,4);			
			//	echo 'date'.$ydt.' Leng th of date time'.strlen(trim($name1)) .$name8.$name9;
			//	echo $name0.$name1.$name2.$name3;
		//	echo'<br>';
			$sql="insert into temp1 (column0,column1,column2,column3,column4,column5,column6,dt,time0) values('$name0','$name1','$name2','$name3','$name4','$name5','$name6','$ydt','$name10')";
	//		$sql="insert into clmspunchingupload (column0,column1,column2,column3,column4,column5,column6,dt,time0) values('$name0','$name1','$name2','$name3','$name4','$name5','$name6','$ydt','$name10')";
//			$sql="insert into clmspunchingupload (column0,column1,column2,column3,column4,column5,column6,dt,time0) values('$name0','$name1','$name2','$name3','$name4','$name5','$name6','$name7','$name10')";
			$result = DB_query($sql,$db);
			//$sql="update  clmspunchingupload set dt=substr(column1,1,10)";
			//$result = DB_query($sql,$db);

	}
	$md="select * from clmspunchingupload";
    $md="create temporary table temp2 SELECT column0,column1,column2 FROM temp1 WHERE column0,column1,column2  NOT IN (SELECT * from clmspunchingupload where temp1.column0=clmspunchingupload.column0 and 
	temp1.column1=clmspunchingupload.column1  and temp1.column2=clmspunchingupload.column2)";
	$md="create temporary table temp2 Select column0,column1,column2,dt,time0 from(select column0,column1,column2,dt,time0 from temp1 union all select column0,column1,column2,dt,time0 from clmspunchingupload)as std GROUP BY column0,column1,column2
	 HAVING Count(*) = 1 order by column2";
//	$md="select * from temp1";
//    $md="update temp1 set dt=STR_TO_DATE(trim(dt),'%d-%m-%Y')";
//		$result = DB_query($md,$db);
       $md="update temp1 a,clmspunchingupload b set a.column6='Dup' where a.column0=b.column0 and a.column1=b.column1 and a.column2=b.column2";
		$result = DB_query($md,$db);
//	echo $md;
	$md="insert into clmspunchingupload(column0,column1,column2,column6,dt,time0) select column0,column1,column2,column6,dt,time0 from temp1 where column6='' ";
		$result = DB_query($md,$db);

//$mq2='echo '.$mq2.'"RRR";';
//echo $mq2;
    	$md="select * from temp1 where column6=''";
		$result = DB_query($md,$db);
		$myrow1 = DB_fetch_array($result);
$column_count = mysql_num_fields($result);
//echo 'Column Count ';
//echo $column_count;
echo'<div>';


echo'<H3>Punching Data</h3>';
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
    
 
  

