<?php

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('User-Section Updation');
//include('includes/headerkancor.inc');
include('includes/headerclms.inc'); 

if($_POST['empno']==''){
 $_POST['empno']=$_SESSION['UserID'];
}
//$_POST['empno']='clms1445';
$sql="select realname from www_users where trim(userid)='". trim($_POST['empno'])."'";
$result01 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow01 = DB_fetch_row($result01);
$realname=$myrow01[0];
$sql="create temporary table temp01 select * from clmsusersection where trim(employeeid)='". trim($_POST['empno'])."'";

 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="create temporary table temp02 select * from clmsusersection where trim(employeeid)='". trim($_POST['empno'])."'";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

 $sql="select * from temp02";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
  while ($myrow03 = DB_fetch_row($result03))  
    {
   // echo $myrow03[1];  
		
	 }

$sql="select section from clmssectionmaster";
 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
 $mcnt=0;
// $msec='';
 while ($myrow03 = DB_fetch_row($result03))  
    {
 //  echo'<br>';
   $m[$mcnt]='no';
  // $m[]='m'.$myrow03[0];
   $msec[]=$myrow03[0];
 //  echo $m[$mcnt].$myrow03[0]; 
	
	$sql04="select count(*) from temp02 where trim(section)='".trim($myrow03[0])."'";
//echo $sql04; 
	$result04 = DB_query($sql04, $db, $ErrMsg, $DbgMsg);
		$myrow04 = DB_fetch_row($result04);
//echo "COUNT=".$myrow04[0];
		if($myrow04[0]>0){
				$m[$mcnt]='y';
//	echo "MMMMM---".$m[$mcnt];			
 				//$m[]='y';
              // $ms[$mcnt]= $myrow03[0];				
			}
             //   $ms["$mcnt"]= $myrow03[0];				
				$ms='ms$mcount';
 //	echo "MMMMM---".$m[$mcnt];			
   
	$mcnt++;	
	 }
	
	
echo "<FORM METHOD='post' id='tk1' ACTION='" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";
//echo $_POST['empno']; 
echo'<td hidden><input type="text" name="empno" value="'.$_POST['empno'].'"</td>';  
echo'<td hidden><input type="text" name="realname" value="'.$realname.'"</td>';    

?>
 <html>  
<head>   
<title> PHP Form<</title>    
</head>   
<body bgcolor="pink">
<table> 
<tr><td><h3>Select Sections to be Alloted to <?php echo $realname.' User ID '.$_POST['empno'];?> </h3></td></tr>
 
<?php 
echo'<tr>';
for($i=0;$i<5;$i++){
	
?>

	
<?php 
/*
$mm1='1td><1input type="1checkbox "';
$mm1=$mm1.' name="chk1'.$i.'"'  ;
 echo"YYYY". $mm1;
 $mm2='checked=checked'; 
 $mm3=' value="test"';
echo"YYYY".$mm2.$mm3;
echo '<br>OOO'."chk1$i".$msec[$i]."MI=".$m[$i];;
	echo '<td><input type="checkbox"';
	echo ' "name="chk1'.$i.'"'; 
    if($m[$i]=='no'){  
		//echo 'checked=checked'; echo ' value="$msec[$i]"';
		echo ' checked=checked';
		echo ' value="test"';
		}
   // else{  echo 'value="$msec[$i]"';
   else{  echo ' value="test1"';  
	}
	echo ">$i-".$msec[$i]."<br /></td>";
 
 */
}

for($k=0;$k<7;$k++){
	if ($k % 2 == 0) {
		echo'</tr><tr>' ;
	}
	//echo 'M='.$m0;
echo'<td><input type="checkbox" name="chkl'.$k.'"';   if($m[$k]=="y"){echo 'checked=checked'; echo " value='".$msec[$k]."'";}
 else{  echo " value='".$msec[$k]."'";} echo" >$k-".$msec[$k]."<br /></td>";
}
?> 
 </table>   
<br>  
<input type="submit" name="Submit" value="Submit">  
</form>  
</body>  
</html> 
<?php  
//$checkbox1 = $_POST['chkl'] ; 
    
if (isset($_POST["Submit"])=="Submit")  
{ 
//$checkbox1 = $_POST['chkl'] ;    
$sql="delete from temp01";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);


//for ($i=0; $i<sizeof ($checkbox1);$i++) {  
for ($i=0; $i<7;$i++) {  
//echo $i;
//echo '<br>KKKK'.$i.$_POST['chkl'.$i];
 
//echo '<br>pppp'.$_POST['chkl25']; 
//echo '<br>QQQQ'.$_POST['chkl0']; 

//$query="INSERT INTO temp01 (section) VALUES ('".$checkboxl[$i]. "')";  
//if(trim($_POST['chkl'.$i])<>''){
if(isset($_POST['chkl'.$i])){
$query="INSERT INTO temp01 (section,employeeid,name,area) VALUES ('".$_POST['chkl'.$i]. "','".$_POST['empno']."','".$_POST['realname']."','".$_POST['empno']."')";  
//mysql_query($query) or die(mysql_error()); 
	$result04 = DB_query($query, $db, $ErrMsg, $DbgMsg);

} 
}  
$sql="delete from clmsusersection where trim(employeeid)='". trim($_POST['empno'])."'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="insert into  clmsusersection select * from temp01";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 

//echo 'FFFF'.$_POST['chkl0'];
echo "Records  Updated";  
 echo '<br><td><td><a href="usersectionupdation0.php">' . _('Back to User Section Setting  Form') . '</a><BR>';


 if($_SESSION['UserID']=='clmsadmin'){
echo' <td><td><left><INPUT type="image" name="submit"  style="width:60px;height:60px;" src="../rcs/includes/back_button.png" VALUE="Back" onClick="history.go(-2);return true;"><p style="font-size:40;">&nbsp;&nbsp;Back to Data Entry form</p></td>';

     $mheading="User-Workcenter Updation";
     $sql="select Employeeid,Name,Section,Area from clmsusersection  order by employeeid  ";
  // $sql="select * from temp01 ";

   $DbgMsg = _('The SQL that was used to insert the employee but failed was');
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $column_count = mysql_num_fields($result03);
   // echo '<br><br><A HREF="exporttoxlspayregister.php?">Export to xls</A>';
 //   echo '<div>';

  // echo'<H3>'.$mheading.'</h3>';
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
     //if($myrow03[20]==0){
	//	$myrow03[20]=' ';  
		
	 //}

	 //echo $myrow1[0];  
        //echo '<tr>';
        print("<TR>"); 
        for($column_num = 0; $column_num < $column_count; $column_num++) 
        {
            //print("<TD>$myrow03[$column_num]</TD>\n"); 
            if ($myrow03[$column_num]<0)
            {
//                 print ("<TD>&nbsp;&nbsp;</TD>\n");
               print("<TD><center>$myrow03[$column_num]</center></TD>\n");

            }
            else
            { 
                $myrow03[111]='';
               if ($myrow03[111]=='Kalamassery'){
		   print("<TD><b><center><font colorrr='dreddd'>$myrow03[$column_num]</font></b></center></TD>\n");
               }else { 
		   print("<TD><center>$myrow03[$column_num]</center></TD>\n");
               } 
            }
        }
 		echo '<TD><A HREF="'. $rootpath . '/notyetkancor.php?' . SID . '&EmployeeID=' . $myrow03[0] . '">' . _('Edit/Delete') . '</A></TD>';
       echo'</tr>';  
    }
    echo'</table>';
    echo '</div>';


}
}  
?>  
