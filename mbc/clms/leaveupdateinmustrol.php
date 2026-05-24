<?php
/* $Revision: 1.0 $ */

//include('punchingreportupdation.php');
//$PageSecurity = 10;
//include('includes/sess.inc');
//$title = _('Processing');

//include('includes/headerkancor.inc') ;
//$EmployeeID1='102';
$Sdate=$_POST['datepicker'];
$Edate=$_POST['datepicker']; 
$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);
$mm=substr($Sdate,3,2);
$my=substr($Sdate,8,2);

$Sdate=date('Y-m-01',strtotime($Sdate));
$Edate=date('Y-m-d',strtotime($Edate));
//echo $Sdate;
//echo $Edate;
 
//$Sdate='2021-07-01';
//$Edate='2021-07-31';
//$mmonth1='0721';
//$my='21';
 	$sql="create temporary table tempcl3 select employeeid,dt,shift,'xxx' as'ws',00 as'dayswkd',00 as 'offdays',00 as'abs' from shiftroster".$mmonth1." order by employeeid,dt";   
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 	$sql="create temporary table tempcl2 select distinct employeeid from leave".$my." where STR_TO_DATE(st_date,'%d-%m-%Y')  between' ".$Sdate."' and '".$Edate."'";   

 	$sql="create temporary table tempcl2 select distinct employeeid from leave".$my." where 
	(STR_TO_DATE(st_date,'%d-%m-%Y')  between' ".$Sdate."' and '".$Edate."') or  (STR_TO_DATE(st_date,'%d-%m-%Y') < ' ".$Sdate."' and (STR_TO_DATE(en_date,'%d-%m-%Y') >='".$Sdate."'))";   
	
//echo $sql; 
	$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
	$sql0k="select employeeid  from tempcl2 ";
	$result0k = DB_query($sql0k, $db, $ErrMsg, $DbgMsg);
	while ($myrow0k = DB_fetch_row($result0k)) {
        $EmployeeID1=trim($myrow0k[0]); 
		    

			//$sql = "SELECT * FROM leave".$my." where employeeid ='".$EmployeeID1."'  and STR_TO_DATE(st_date,'%d-%m-%Y')  between' ".$Sdate."' and '".$Edate."'"; 
			$sql = "SELECT * FROM leave".$my." where employeeid ='".$EmployeeID1."'  and 	(STR_TO_DATE(st_date,'%d-%m-%Y')  between' ".$Sdate."' and '".$Edate."') or  (STR_TO_DATE(st_date,'%d-%m-%Y') < ' ".$Sdate."' and (STR_TO_DATE(en_date,'%d-%m-%Y') >='".$Sdate."'))";   
 

//echo $sql;
 //	$ErrMsg = _('The employee') . ' ' . $_POST['LastName'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the employee but failed was');
			$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
			while ($myrow = DB_fetch_row($result)) {
         $mshift=$myrow[3];
            $kshift=$mshift;  
			//$EmployeeID=$myrow[1];
			$mch=$mm.'-20'.$my;
			$mch1=substr(trim($myrow[4]),3,7);
			$mSdate=$myrow[4];
			if($mch<>$mch1){
				$mSdate='01-'.$mch;
				}
			$mEdate=$myrow[5];
			//echo"Start date";
			//echo '<br>'.$mch;
			//echo '<br>'.$mch1;
			//echo $EmployeeID;

			$Edate2=date('Y-m-d',strtotime($mEdate));
			$Sdate3=date('Y-m-d',strtotime($mSdate));
			$Edate5=date('d-m-Y',strtotime($mEdate));
			$Sdate5=date('d-m-Y',strtotime($mSdate));
			//echo $Sdate3;
			//echo$Edate2;
			//echo $Edate;
			$tomorrow=$Sdate3;
			$md="select employeeid";
			$mkk=0;
			$mq2='';
			$moffday=0;
			while($tomorrow<=$Edate2){
			$mdate=date('d-m-Y',strtotime($tomorrow));
			$mq2=$mq2.'$myrow1['.$mkk.'].';
			//echo '<br>';
			//echo $mdate;
			//echo $tomorrow;
			//echo $myrow[1];
			$sql0="select count(employeeid) as'offday' from shiftchange where employeeid='".$myrow[1]."' and dt='".$mdate."' and sshift = 'O'";
 			$result0 = DB_query($sql0, $db);
			$myrow0 = DB_fetch_array($result0);
			$offday = $myrow0{'offday'};
			//echo $mkk;
			//echo 'moff day'.$offday. 'm shift '.$mshift;
			//echo $sql0;
			$mshift=$myrow[3];
			if($offday> 0){
                           $moffday=$moffday+1;
        	if($myrow[3]=='EL' and $offday>0){
			$mshift='OF';
				
			}
			if($myrow[3]=='EL' and $offday<1){
			$mshift='EL';
				
			}

            }

			$md=$md.',D'.substr($tomorrow,8,2);;
			//echo "tomorrow".$tomorrow;
			//echo "D".substr($tomorrow,8,2);

			$Sdate1=$tomorrow;
			$Sdate2=str_replace('-', '/', $Sdate1);
			$tomorrow = date('Y-m-d',strtotime($Sdate2 . "+1 days"));
            $sql = "SELECT count(employeeid) as'mcount' FROM tempcl3 where employeeid='".$myrow[1]."' and dt ='".$mdate."'";

//echo $sql;			//$ErrMsg = _('The employee') . ' ' . $_POST['LastName'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the employee but failed was');
			$result1 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
			$myrow1 = DB_fetch_row($result1);

	if ( $myrow1[0] > 0 ) {
            $sql = "UPDATE tempcl3 SET ws='" . DB_escape_string($mshift) . "'
			WHERE employeeid='".$myrow[1]."' and dt ='".$mdate."' and ws !='OFF'";
			//$ErrMsg = _('The employee') . ' ' . $_POST['LastName'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the employee but failed was');
//	echo $sql;
	$result3 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
			//                                        echo"Record  Found";
                       } else {
					   $sql = "INSERT INTO tempcl3 (
                        employeeid, 
						dt,ws )
				VALUES (
					'" . DB_escape_string($myrow[1]) ."','" . DB_escape_string($mdate) ."','" . DB_escape_string($mshift) ."'

					)";

			//$ErrMsg = _('The employee') . ' ' . $_POST['LastName'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the employee but failed was');

			$result3 = DB_query($sql, $db, $ErrMsg, $DbgMsg);


			//             echo"Record Not Found";
                           }
			$mkk=$mkk+1;
			}

			} //END WHILE LIST LOOP
		}// End Employee id Selection
			//echo "Leave Updated"; 
							echo "Leave Updated" ;
/*
			$sql="select employeeid,dt,ws from tempcl3 ";
			$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
			while ($myrow = DB_fetch_row($result)) {
            echo"<br>".$myrow[0]."--".$myrow[1]."--".$myrow[2]; 
			}    

*/




?>