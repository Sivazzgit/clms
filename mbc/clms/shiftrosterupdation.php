<?php

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('Shift Roster Updation');
include('includes/headerclms.inc');
 
//exit("Shift Roster updated upto 31-10-2021");  

$mmonth = '1022'; 
$mlmonth='0922';
//str_to_date(doj,'%d.%m.%Y')
//$sql="create temporary table tsrost0 select *,STR_TO_DATE(dt,'%d-%m-%Y') as'dt1' from clmsshiftroster".$mlmonth." where dt>='08-02-2022'";
//$sql="create temporary table tsrost0 select *,STR_TO_DATE(dt,'%d-%m-%Y') as'dt1' from clmsshiftroster".$mlmonth." where dt>='11-08-2022'";
$sql="create temporary table tsrost0 select *,STR_TO_DATE(dt,'%d-%m-%Y') as'dt1' from clmsshiftroster".$mlmonth." where dt>='10-09-2022'";
//$sql="create temporary table tsrost0 select *,STR_TO_DATE(dt,'%d-%m-%Y') as'dt1' from clmsshiftroster".$mlmonth." where dt>='11-01-2022'";  
//echo $sql;
		$result = DB_query($sql,$db);
//$sql="create temporary table tsrost select *,ADDDATE(dt1, INTERVAL 2 month) as'dt2' from tsrost0" ;
//$sql="create temporary table tsrost select *,ADDDATE('2020-09-27', INTERVAL 3 day) as'dt2' from tsrost0" ;
$sql="create temporary table tsrost1 select *,ADDDATE(dt1, INTERVAL 21 day) as'dt2' from tsrost0" ;
$result = DB_query($sql,$db);
$sql="create temporary table tsrost select *,date_format(dt2,'%d-%m-%Y') as'dt3' from tsrost1 order by employeeid" ;
$result = DB_query($sql,$db);

$sql="create temporary table tsrost5 select *,ADDDATE(dt1, INTERVAL 42 day) as'dt2' from tsrost0" ;
$result = DB_query($sql,$db);

$sql="create temporary table tsrost6 select *,date_format(dt2,'%d-%m-%Y') as'dt3' from tsrost5 order by employeeid"; 
$result = DB_query($sql,$db);



$sql="insert into tsrost select * from tsrost6" ;
$result = DB_query($sql,$db);
$sql="update tsrost set dt=dt3" ;
$result = DB_query($sql,$db);

/*
//****
//Off Roration changed in 25-12-20, One date is taken back
//This is to be opened onle when next change affects

$sql="create temporary table toffchange01 select *,ADDDATE(dt2, INTERVAL -1 day) as'dt00' from tsrost" ;
$result = DB_query($sql,$db);

$sql="create temporary table toffchange select *,date_format(dt00,'%d-%m-%Y') as'dt001' from toffchange01 order by employeeid"; 
$result = DB_query($sql,$db);


//echo $sql; 
$sql="update toffchange set dt=dt001" ;
$result = DB_query($sql,$db);



$sql="drop table tsrost" ;
$result = DB_query($sql,$db);
$sql="create temporary table tsrost select * from toffchange" ;
$result = DB_query($sql,$db);
$sql="update tsrost set offday='Sunday' where shiftgroup='A'" ;
$result = DB_query($sql,$db);
$sql="update tsrost set offday='Monday' where shiftgroup='B'" ;
$result = DB_query($sql,$db);
$sql="update tsrost set offday='Tuesday' where shiftgroup='C'" ;
$result = DB_query($sql,$db);
$sql="update tsrost set offday='Wednesday' where shiftgroup='D'" ;
$result = DB_query($sql,$db);
$sql="update tsrost set offday='Thursday' where shiftgroup='E'" ;
$result = DB_query($sql,$db);
$sql="update tsrost set offday='Friday' where shiftgroup='F'" ;
$result = DB_query($sql,$db);
$sql="update tsrost set offday='Saturday' where shiftgroup='G'" ;
$result = DB_query($sql,$db);

//Group Change of Employee in 145 from G to A
$sql="create temporary table tt1 select * from tsrost where employeeid='113'" ;
$result = DB_query($sql,$db);
$sql="update tt1 set employeeid='145'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.shift=b.shift where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='145'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.shiftgroup=b.shiftgroup where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='145'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.offday=b.offday where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='145'" ;
$result = DB_query($sql,$db);

$sql="drop table tt1" ;
$result = DB_query($sql,$db);

//Group Change of Employee in 172 from G to A
$sql="create temporary table tt1 select * from tsrost where employeeid='113'" ;
$result = DB_query($sql,$db);
$sql="update tt1 set employeeid='172'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.shift=b.shift where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='172'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.shiftgroup=b.shiftgroup where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='172'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.offday=b.offday where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='172'" ;
$result = DB_query($sql,$db);

//Group Change of Employee in 168 from G to A
$sql="drop table tt1" ;
$result = DB_query($sql,$db);

$sql="create temporary table tt1 select * from tsrost where employeeid='113'" ;
$result = DB_query($sql,$db);
$sql="update tt1 set employeeid='168'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.shift=b.shift where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='168'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.shiftgroup=b.shiftgroup where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='168'" ;
$result = DB_query($sql,$db);
$sql="update tsrost a, tt1 b set a.offday=b.offday where a.employeeid=b.employeeid and a.dt=b.dt and a.employeeid='168'" ;
$result = DB_query($sql,$db);





$sql="update tsrost set shift='1' where employeeid='168' and shift<>'O'" ;
$result = DB_query($sql,$db);
$sql="update tsrost set shift='1' where employeeid='109' and shift<>'O'" ;
$result = DB_query($sql,$db);
$sql="update tsrost set shift='1' where employeeid='112' and shift<>'O'" ;
$result = DB_query($sql,$db);



//****
*/

$sql="delete from tsrost where substr(dt,4,2)<>". substr($mmonth,0,2) ;
$result = DB_query($sql,$db);
//echo $sql;
//echo'Mmonth'.$mmonth;
//echo'oooo'.substr(trim($mmonth),0,2); 
 

    $sql="CREATE TABLE if not exists clmsshiftroster0820 like  clmsshiftroster1221";
	$result0 = DB_query($sql,$db,$ErrMsg);
		
    $sql="CREATE TABLE if not exists clmsshiftroster".$mmonth." like  clmsshiftroster1221";
	$result0 = DB_query($sql,$db,$ErrMsg);

    $sql="delete from clmsshiftroster".$mmonth;
	$result0 = DB_query($sql,$db,$ErrMsg);
    $sql="insert into clmsshiftroster".$mmonth."(employeeid,grade,section,shiftgroup,normal,offday,shift,dt) select employeeid,grade,section,shiftgroup,normal,offday,shift,dt from tsrost" ;

	$result0 = DB_query($sql,$db,$ErrMsg);
	
//	echo $sql;

//$tomorrow = date('d-m-Y',strtotime($kdate . "+30 days"));
//$medt1=date('01-m-Y',strtotime($medt)); 

//$md="update tsrost set dt1= date('d-m-Y',strtotime(trim(dt)))";
//$result = DB_query($md,$db);
 

$md="select * from clmsshiftroster".$mmonth." order by employeeid,dt";
//$md="select * from tsrost order by employeeid";
//$md="select * from tsrost6 order by employeeid";
//$md="select * from toffchange order by employeeid,dt";
 		$result = DB_query($md,$db);
$column_count = mysqli_num_fields($result);
 
$md="select * from clmsshiftroster".$mmonth." order by employeeid,dt";
//$md="select * from tsrost order by employeeid,dt";
//$md="select * from toffchange order by employeeid,dt";
 		$result = DB_query($md,$db);

echo $md;		
echo'<div>';


echo'<H3>Employee Shift Roster Updated </h3>';
echo'<center><table Border="1" width ="25%"  cellspacing="0" cellpaddin="0">';

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

			
	
	
?>
    
 
  

