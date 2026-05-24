clms<?php

$PageSecurity = 10;

include('pdataplotting.php'); 

  $sql0p="update clmspunching set column4=dt" ;   
   $result0p = DB_query($sql0p,$db,$ErrMsg);
   $sql0p="update clmspunching set column5=time0" ;   
   $result0p = DB_query($sql0p,$db,$ErrMsg);

//   $sql0p="select recordid,str_to_date(dt,'%Y-%m-%d')as'dt',time0 from punching  where time0 < 6 and column3='IN'" ;   
//   $sql0p="create temporary table tp01 select recordid,dt,time0 from punching  where time0 < 6 and column3='IN'" ;   
  $sql0p="create temporary table tp01 select recordid,dt,time0 from clmspunching  where time0 < 4 " ;   
   $result0p = DB_query($sql0p,$db,$ErrMsg);
 
 $sql0p="select recordid,dt,time0 from tp01" ;    
   $result0p = DB_query($sql0p,$db,$ErrMsg);
	while ($myrow0p = DB_fetch_row($result0p)) {
	$previousdt = date('d-m-Y',strtotime($myrow0p[1] . "-1 days"));
	 //echo"<br>Today".$myrow0p[1];
	 //echo"<br> yday".$previousdt."<br>";
     $sql00k="update clmspunching set column4='".$previousdt."' where recordid=".$myrow0p[0] ;     
     $result00k = DB_query($sql00k,$db,$ErrMsg); 
  $sql="update clmspunching set column5=".($myrow0p[2]+24)." where recordid=".$myrow0p[0] ;      
    $result = DB_query($sql,$db,$ErrMsg);
	 
//echo $sql;	      
	} 

/*	
  $sql0p="create temporary table tp02 select recordid,dt,time0 from clmspunching  where time0 < 9 and column3='OUT'"     ;
   $result0p = DB_query($sql0p,$db,$ErrMsg);
 
 $sql0p="select recordid,dt,time0 from tp02" ;    
   $result0p = DB_query($sql0p,$db,$ErrMsg);
	while ($myrow0p = DB_fetch_row($result0p)) {
	$previousdt = date('d-m-Y',strtotime($myrow0p[1] . "-1 days"));
	 //echo"<br>Today".$myrow0p[1];
	 //echo"<br> yday".$previousdt."<br>";
     $sql00k="update clmspunching set column4='".$previousdt."' where recordid=".$myrow0p[0] ;     
     $result00k = DB_query($sql00k,$db,$ErrMsg); 
  $sql="update clmspunching set column5=".($myrow0p[2]+24)." where recordid=".$myrow0p[0] ;      
    $result = DB_query($sql,$db,$ErrMsg);
	 
//echo $sql;	      
	} 	
*/	
?>