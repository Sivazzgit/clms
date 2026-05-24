<?php
$PageSecurity = 10;  
include('includes/sess.inc');
include('includes/headerclms.inc');
echo'</div>';
echo"<div style='display: block; margin-left:0%;margin-right:8%;width: 100%; background-color: 1#F9A825; overflow: auto; '>"; 
//echo 'grade1'.$_POST['grade1'];  
if (isset($_POST['submit2']) or isset($_POST['submit3']) ) { 
//echo "XXXXXXXX";
}
     $ans=$_POST['ans'];
  $Sdate=$_POST['Sdate'];
  $Edate=$_POST['Edate'];
//  $msln=$_POST['msln'];
  $mshift=$_POST['mshift'];
  $mwc=$_POST['mwc'];
  $mtotcount=$_POST['totcount'];
 //echo"RRRRR".$mtotcount; 
 //echo 'name11'.trim($_POST['empn1']);  

  if(trim($mwc)=='hh'){
echo' <td><td><left><INPUT type="image" name="submit"  style="width:30px;height:30px;" src="../rcs/includes/back_button.png" VALUE="Back" onClick="history.go(-2);return true;"><br><p style="font-size:20;">&nbsp;&nbsp;Back to Data Entry form</p></td>';
	echo'<h1>';  
  	prnMsg(_('All Plant Updation is not allowed.') . _('Please select any one plant'),'warn');
	echo'</h1>';
  }else{
//	exit('Break-2'); 
  
	  
//echo '<br>ttttttKK----'. $_POST['totcount'].'<br>';
$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);
//	$sql = "CREATE TEMPORARY TABLE if not exists TempT2 as (SELECT *,00 as'slno'  FROM catlmfgdelayd".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and trim(shift)='".$mshift."' and trim(workcenter)='".$mwc."' )";
	$sql = "CREATE TEMPORARY TABLE if not exists TempT2 as (SELECT *,00 as'slno'  FROM clmsmanpower".$mmonth1." order by recordid limit 0 )";
		$result = DB_query($sql,$db);
//echo $sql;
	$sql=="delete from TempT2 where slno=0";	
	    $result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$msln=1 ;
 while ($msln <=$mtotcount)  
    {
	//	$mrid=$myrow03[0];
		$sql1 = "insert into  TempT2(slno) values(".$msln.")";
		$result1 = DB_query($sql1,$db);
        $msln=$msln+1;
	//	echo $msln;
	} 
	//	echo 'Sl.No'.$msln;

	 for($mcount = 1; $mcount < $msln; $mcount++) 
	
        {
  //  echo '<br>'.$mcount.'Details'.$_POST['empn'.$mcount];
				
    if(trim($_POST['empn'.$mcount])<>''){     
	// $memno= $_POST['empid'.$mcount];
 	 $memno= $_POST['empn'.$mcount];
        $memno=trim($memno);		 
     	 $sql0 = "update  TempT2  set 
		shift = '".$mshift."', 
		workcenter = '".$mwc."',
		dt = '".$Edate."', 
		employeeid = '".$memno."',
		employeename = '".$_POST['empn'.$mcount]."',
		type = '".$_POST['type'.$mcount]."',
		hrs = ".$_POST['hrs'.$mcount].",
        section= '".$_POST['sec'.$mcount]."', 
		grade= '".$_POST['grade'.$mcount]."',
		actgrade= '".$_POST['act'.$mcount]."',
		Spice= '".$_POST['spic'.$mcount]."',
		Manning= '".$_POST['man'.$mcount]."',
		Timein= '".$_POST['timein'.$mcount]."',
		Timeout= '".$_POST['timeout'.$mcount]."',
		sshift= '".$_POST['sshift'.$mcount]."'
		
		
		
		where slno=".$mcount ;  
//	echo '<br>'. $sql0; 
	
/* 
	, 

		sshift= '".$_POST['sshift'.$mcount]."'
*/

		$result0 = DB_query($sql0,$db);
	}
		}
		
		$sql0="delete from TempT2 where trim(employeename)=''";
		$result0 = DB_query($sql0,$db); 
	
//	echo $sql0; 
	
//	     echo "<br>Emp code Posted".$memno ." Sl.no ".$mcntt." date-1 ".$Sdate." date-2 ".$Edate;
	 //	 echo "Angggg".$ans; 
	 
//	     echo "<br>Emp code Posted".$memno ." Sl.no ".$mcntt." date-1 ".$Sdate." date-2 ".$Edate;


/*
$sql="update TempT2 set employeeid=substr(trim(employeename),1,2) where substr(trim(employeeid),1,2)='OT' and substr(trim(employeename),1,1)>2 ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 set employeeid=substr(trim(employeename),1,3) where substr(trim(employeeid),1,2)='OT' and substr(trim(employeename),1,1)=1 ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 set employeeid=substr(trim(employeename),1,2) where substr(trim(employeeid),1,4)='Duty' and substr(trim(employeename),1,1)>2 ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 set employeeid=substr(trim(employeename),1,3) where substr(trim(employeeid),1,4)='Duty' and substr(trim(employeename),1,1)=1 ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 set employeeid=substr(trim(employeename),1,2) where substr(trim(employeeid),1,4)='ABSE' and substr(trim(employeename),1,1)>2 ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 set employeeid=substr(trim(employeename),1,3) where substr(trim(employeeid),1,4)='ABSE' and substr(trim(employeename),1,1)=1 ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
*/	
$sql="update TempT2 a, clmsemployeemaster b set a.employeename=b.lastname  where a.employeeid=b.employeeid";
   $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 a, clmsemployeemaster b set a.section=b.section where a.employeeid=b.employeeid and trim(a.section)='' ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 a, clmsemployeemaster b set a.category=b.dept where a.employeeid=b.employeeid ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

//$sql="update TempT2 a, clmsemployeemaster b set a.grade=b.grade where a.employeeid=b.employeeid and trim(a.grade)='' ";
$sql="update TempT2 a, clmsemployeemaster b set a.grade=b.grade where a.employeeid=b.employeeid ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 a, clmspunching b set a.timein=b.time0 where a.employeeid=b.column0 and trim(a.timein)='' and a.dt=b.column4 ";
   $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update TempT2 a, clmspunching b set a.timeout=b.time0 where a.employeeid=b.column0 and trim(a.timeout)='' and a.dt=b.column4 ";
   $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql = "update TempT2 a,clmsshiftroster".$mmonth1."  b  set a.sshift=b.shift where a.employeeid=b.employeeid and a.dt=b.dt";  
	$result = DB_query($sql,$db);
$sql = "update TempT2 set hrs=8 where hrs=0 and (trim(type)='Duty' or trim(type)='OT')";  
	$result = DB_query($sql,$db);

//*********************Allowances Config

			$sql = "update TempT2 set actgrade='' ";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set manning='' ";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set spice='' where spice='ASH'  ";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set spice='' where spice='ETP'  ";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set spice='' where spice='T14'  ";  
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

			$sql = "update TempT2 set actgrade='' where type='LEAVE' ";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set manning=''  where type='LEAVE'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set spice=''  where type='LEAVE'  ";  
			$result = DB_query($sql,$db);

			$sql = "update TempT2 set actgrade='' where grade='WTR' ";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set manning=''  where grade='WTR'";  
			$result = DB_query($sql,$db);
			$sql = "update TempT2 set spice=''  where grade='WTR'  ";   
			$result = DB_query($sql,$db);
			
			
			
//************Allowance Config  Ends	

		$sql0="select count(*) from 	clmsmanpower".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and shift='".$mshift."'";
//echo $sql0;
		$result0 = DB_query($sql0,$db); 
	while ($myrow0 = DB_fetch_row($result0))  
    {
	$mcn=$myrow0[0];
    }	
	//if($mcn <> ($msln-1)){
		//echo "<br>mcn".$mcn; 
	    //echo "<br>msln".$msln;
	//	echo "Not tallied";
		$sql0="delete from 	clmsmanpower".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and shift='".$mshift."' and workcenter='".$mwc."'";
	    $result0 = DB_query($sql0,$db); 
//		$sql0="insert into clmsmanpower".$mmonth1."(workcenter,shift,dt,employeeid,position,employeename,hrs,category,type,user) select workcenter,shift,dt,employeeid,position,employeename,hrs,category,type,'".$_SESSION['UserID']."'  from TempT2";
        $sql0="insert into clmsmanpower".$mmonth1."(dt,Workcenter,shift,Employeeid,Employeename,position,type,HRS,Section,Grade,Actgrade,Spice,Manning,Timein,Timeout,sshift,category) select dt,Workcenter,shift,Employeeid,Employeename,position,type,HRS,Section,Grade,Actgrade,Spice,Manning,Timein,Timeout,sshift,category from TempT2"; 
        $result0 = DB_query($sql0,$db);  
	 $currentDateTime = date('d-m-Y H:i:s');
     $mupdatedby=$_SESSION['UsersRealName'].$currentDateTime;
 // echo 'RRRRR'.$mupdatedby;	
		$sql0="update clmsmanpower".$mmonth1." set user='".$mupdatedby."' where  dt between '".$Sdate."' and '".$Edate."' and shift='".$mshift."' and workcenter='".$mwc."'";
	    $result0 = DB_query($sql0,$db); 
	
		
		if (isset($_POST['submit3'])){
		$sql0="update clmsmanpower".$mmonth1." set yarea='Submitted' where  dt between '".$Sdate."' and '".$Edate."' and shift='".$mshift."' and workcenter='".$mwc."'";
	    $result0 = DB_query($sql0,$db); 

		echo"Submitted";
}
	
	//	    }


$sql="create temporary table tdupli select employeeid,employeename,count(employeeid) as'cnt' from TempT2 group by employeeid ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

$sql="select count(*) from tdupli where cnt>1 ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $myrow03 = DB_fetch_row($result03);
	$mdupcnt=$myrow03[0];
if($mdupcnt>0){

echo "Found the following Duplicate entries ";
$sql="select employeeid,employeename from tdupli where cnt>1 ";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    while ($myrow03 = DB_fetch_row($result03))  
    {
     echo"<br>".$myrow03[0]."    ".$myrow03[1]; 

	}

}	


//Intend calculation
$kkdept='';
if($_SESSION['UserID']=='clms01'){
	$kkdept='UNIVERSAL ASSOCIATES';
}
if($_SESSION['UserID']=='clms02'){ 
	$kkdept='G4S SECURE SOLUTIONS INDIA PVT LTD';
}
if($_SESSION['UserID']=='clms03'){
	$kkdept='KINGDOM SECURITY';
}
include('intendcal.php'); 


//******

$mshiftint='b.'.trim($mshift).'shift';

$sql="create temporary table ts02 select section,000 as'Intend',000 as'Present'  from clmssectionmaster ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="insert into ts02(section) Values('') ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);


$sql="create temporary table ts01 select section,000 as'Intend',count(*) as'Present'  from TempT2  group by section  ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update ts01 a, tint00 b set a.Intend=".$mshiftint." where substr(a.section,1,10)=substr(b.section,1,10) ";
//echo $sql;
$sql="update ts02 a, tint00 b set a.Intend=b.Ashift where substr(a.section,1,10)=substr(b.section,1,10) ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update ts02 a, ts01 b set a.Present=b.Present where substr(a.section,1,20)=substr(b.section,1,20) ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="create temporary table ts03 select section, sum(Intend) as'Intend',sum(present) as'Present' from ts02 ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="update ts03 set section='Total'  ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="insert into ts02 select * from ts03 ";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

$sql="select section,Intend,Present from ts02 where Intend+Present >0  "; 
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $column_count = mysql_num_fields($result03);
echo' <td><td><left><INPUT type="image" name="submit"  style="width:30px;height:30px;" src="../rcs/includes/back_button.png" VALUE="Back" onClick="history.go(-2);return true;"><br><p style="font-size:20;">&nbsp;&nbsp;Back to Data Entry form</p></td>';
 
 $mheading="Section wise Summary of Intend and Engaged on ".$Sdate." in ".$mshift." Shift";
    echo'<center><table id="tbl1" Border="1" width ="50%"  cellspacing="0" cellpaddin="0">'; 

    print("<TR>");
	echo "<th colspan='".($column_count)."'>";
    echo'<p>'.$mheading.'</p></th></tr><tr>';

    for($column_num = 0; $column_num < $column_count; $column_num++) 
    {
        $field_name = mysql_field_name($result03, $column_num);
        print("<TH>$field_name</TH>\n");
    }
    print("</TR>");     
    while ($myrow03 = DB_fetch_row($result03))  
    {
      $mcntt=  $myrow03[1];       
	   //echo $myrow1[0];  
        //echo '<tr>';
        print("<TR>"); 
        for($column_num = 0; $column_num < $column_count; $column_num++) 
        {
            //print("<TD>$myrow03[$column_num]</TD>\n"); 
            if ($myrow03[$column_num]<=0)
            {
//                 print ("<TD>&nbsp;&nbsp;</TD>\n");
               print("<TD><center>$myrow03[$column_num]</center></TD>\n");

            }
            else
            { 
               if ($myrow03[1]==''){
		   print("<TD><b><center>$myrow03[$column_num]</b></center></TD>\n");
               }else { 
		   print("<TD><center>$myrow03[$column_num]</center></TD>\n");
               } 
            }
        }
	 	
		//  echo '<TD><A HREF="'. $rootpath . '/recorddelete.php?' . SID . '&LeaveID=' . $myrow03[0] . '&mmn='.$mmonth1.'">' . _('Delete Record') . '</A></TD>';
 	
 
        echo'</tr>';  
	}	
	 
    echo'</table>';
	
	
$mintend=$_POST['mintend'];
	
$sql="select count(employeeid) from TempT2 where employeeid>0 and substr(type,1,4)='Duty'";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $myrow03 = DB_fetch_row($result03);
	$mpresent=$myrow03[0];
$sql="select count(employeeid) from TempT2 where employeeid>0 and substr(type,1,4)='ABSE'";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $myrow03 = DB_fetch_row($result03);
	$mabsent=$myrow03[0];
$sql="select count(employeeid) from TempT2 where employeeid>0 and substr(type,1,2)='OT'";
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $myrow03 = DB_fetch_row($result03);
	$mextrawork=$myrow03[0];
   echo'<center><table id="tbl3" Border="1" width ="50%"  cellspacing="0" cellpaddin="0">'; 
  
	echo "<tr><th colspan='3'><b>Manpower summary on ".$Sdate." in ".$mshift." Shift</b></th></tr>";
    print("<TR>");
	echo "<th>Intend</th><th>Present</th></TR>";
    echo"<tr><td><center>".$mintend."</center></td><td><center>".$mpresent."</center></td></td></tr></table>"; 

//include('manpowerattendancecheck.php');
	
	 
$sql = "select * from TempT2 "; 
 //$sql = "select Recordid,dt as'Date',Shift, Workcenter,fromtime as'Delay From (Hrs:Min)',totime as'Delay Upto (Hrs:Min)',Duration as'Duration in Min',reasoncode as'Delay Reason',Employee,prodcode as'Compound',Contract,Others  from 	catlmfgdelayd".$mmonth1." where  dt between '".$Sdate."' and '".$Edate."' and shift='".$mshift."' and workcenter='".$mwc."'";
//$sql = "select slno,fromtime as'DelayFrom(Hrs:Min)',totime as'DelayUpto(Hrs:Min)',Duration as'Duration in Min',reasoncode as'Delay Reason',Employee,prodcode as'Compound',Contract,Others from TempT1";
$sql = "select dt as'Date',Workcenter as'Plant',Shift,Employeeid,Employeename,Type,HRS,Section as 'Working Section',Grade as'Working Grade',Actgrade,Spice,Manning,Timein,Timeout,sshift,slno from TempT2"; 
$sql = "select dt as'Date',Workcenter as'User',Shift,Employeeid,Employeename,Type,HRS,Section as 'Working Section',Timein,Timeout,category as'Vendor',slno from TempT2"; 
		
    $DbgMsg = _('The SQL that was used to insert the employee but failed was');
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $column_count = mysql_num_fields($result03);

//	echo "<FORM METHOD='post' id='f1' ACTION='mfgproductiondataupdation1.php'";
$mheading="Manpower Data Updated";
   // echo '<br><br><A HREF="exporttoxlspayregister.php?">Export to xls</A>';
    echo '<div>';
echo' <td><td><left><INPUT type="image" name="submit"  style="width:30px;height:30px;" src="../rcs/includes/back_button.png" VALUE="Back" onClick="history.go(-2);return true;"><br><p style="font-size:20;">&nbsp;&nbsp;Back to Data Entry form</p></td>';

 //  echo'<H3>'.$mheading.'</h3>';
 //   echo 'Prode Code'. $KraType  .'  Name '. $mprodname;
    echo'<center><table id="tbl1" Border="1" width ="50%"  cellspacing="0" cellpaddin="0">'; 

    print("<TR>");
	echo "<th colspan='".($column_count)."'>";
    echo'<p>'.$mheading.'</p></th></tr><tr>';

    for($column_num = 0; $column_num < $column_count; $column_num++) 
    {
        $field_name = mysql_field_name($result03, $column_num);
        print("<TH>$field_name</TH>\n");
    }
    print("</TR>");     
    while ($myrow03 = DB_fetch_row($result03))  
    {
      $mcntt=  $myrow03[1];       
	   //echo $myrow1[0];  
        //echo '<tr>';
        print("<TR>"); 
        for($column_num = 0; $column_num < $column_count; $column_num++) 
        {
            //print("<TD>$myrow03[$column_num]</TD>\n"); 
            if ($myrow03[$column_num]<=0)
            {
//                 print ("<TD>&nbsp;&nbsp;</TD>\n");
               print("<TD><center>$myrow03[$column_num]</center></TD>\n");

            }
            else
            { 
               if ($myrow03[1]==''){
		   print("<TD><b><center>$myrow03[$column_num]</b></center></TD>\n");
               }else { 
		   print("<TD><center>$myrow03[$column_num]</center></TD>\n");
               } 
            }
        }
	 	
		//  echo '<TD><A HREF="'. $rootpath . '/recorddelete.php?' . SID . '&LeaveID=' . $myrow03[0] . '&mmn='.$mmonth1.'">' . _('Delete Record') . '</A></TD>';
 	
 
        echo'</tr>';  
	}	
	 
    echo'</table>';
    echo '</div>';
   
	}
		
echo'</div';
 
?>