<?php
/* $Revision: 1.0 $ */
// include('includes/sess.inc');
$title = _('Contract Labour Engagement Recording at Gate');


//include('includes/headerclmsnew.inc'); 
   //$sql1 = "create temporary table tpplot select column0,dt,time0,00.00 as'inp',00.00 as'outp'  from clmspunchingupload limit 0";
   $sql1 = "create temporary table tpplot select column0,dt,time0,00.00 as'inp',00.00 as'outp'  from clmspunching limit 0";
    $result1 = DB_query($sql1,$db);
  // $sql = "select column0,dt,time0 from clmspunchingupload ";
   $sql = "select column0,dt,time0 from clmspunching ";
   
 $result = DB_query($sql,$db);
	while ($myrow = DB_fetch_row($result))  
	{
		 
    //echo "<br>". $myrow[0];		
    $sql0 = "select count(*) from tpplot where trim(column0)='".trim($myrow[0])."' and trim(dt)='".trim($myrow[1])."'";
	$result0 = DB_query($sql0,$db);
	$myrow0 = DB_fetch_row($result0);
	if($myrow0[0]==0){
	$sql01 = "insert into tpplot(column0,dt,inp,outp) values('".$myrow[0]."','".$myrow[1]."',00.00,00.00)";
 //echo $sql01;
 $result01 = DB_query($sql01,$db);
	}
  	$sql01 = "update  tpplot set inp=".$myrow[2]." where trim(column0)='".$myrow[0]."' and trim(dt)='".$myrow[1]."' and inp=0";
 //echo $sql01;
	$result01 = DB_query($sql01,$db);
	$result0 = DB_query($sql0,$db);
  	$sql01 = "update  tpplot set outp=".$myrow[2]." where trim(column0)='".$myrow[0]."' and trim(dt)='".$myrow[1]."' and inp<>$myrow[2]";
    $result01 = DB_query($sql01,$db);
	}		
	$sql02 = "select column0,dt,time0,inp,outp from tpplot order by column0,dt ";
	$result02 = DB_query($sql02,$db);
/*
	echo'<center><table id="tbl1" Border="1" width ="100%"  cellspacing="0" cellpaddin="0">'; 
	while ($myrow02 = DB_fetch_row($result02))  

	{
     echo"<tr><td>".$myrow02[0]."</td><td>".$myrow02[1]."</td><td>".$myrow02[2]."</td><td>".$myrow02[3]."</td><td>".$myrow02[4]."</td></tr>";

    }
	echo"</table>"; 
*/
	$sql03 = "update clmspunching set column2=0 ";
	$result03 = DB_query($sql03,$db);
	//$sql03 = "update clmspunchingupload a, tpplot b set a.column2=1 where a.column0=b.column0 and a.dt=b.dt and a.time0=b.inp ";
	$sql03 = "update clmspunching a, tpplot b set a.column2=1 where a.column0=b.column0 and a.dt=b.dt and a.time0=b.inp ";
	$result03 = DB_query($sql03,$db);
	//$sql03 = "update clmspunchingupload a, tpplot b set a.column2=2 where a.column0=b.column0 and a.dt=b.dt and a.time0=b.outp ";
	$sql03 = "update clmspunching a, tpplot b set a.column2=2 where a.column0=b.column0 and a.dt=b.dt and a.time0=b.outp ";
	$result03 = DB_query($sql03,$db);
	//echo $sql03
	$sql03 = "drop table tpplot";
	$result03 = DB_query($sql03,$db);
	
 	//exit("Break-8");	
 
?>