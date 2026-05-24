<?php

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('User-Section Updation');
//include('includes/headerkancor.inc');
include('includes/headerclms.inc'); 

if($_POST['empno']==''){
 $_POST['empno']=$_SESSION['UserID'];
}
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
$sql="select count(*) from temp02 where trim(section)='Boiler'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m0='y';	
}
//echo 'ZZZZZ-1'.$m0; 
$sql="select count(*) from temp02 where trim(section)='Effluent Treatment Plant'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m1='y';	
}
$sql="select count(*) from temp02 where trim(section)='Electrician'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m2='y';	
}
$sql="select count(*) from temp02 where trim(section)='EOD'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m3='y';	
}
$sql="select count(*) from temp02 where trim(section)='FGS 1 - Blending'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m4='y';	
}
$sql="select count(*) from temp02 where trim(section)='FGS 1 - Filling and Packing'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m5='y';	
}
$sql="select count(*) from temp02 where trim(section)='FGS 2'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m6='y';	
}
$sql="select count(*) from temp02 where trim(section)='Lab Helper'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m7='y';	
}
$sql="select count(*) from temp02 where trim(section)='Operator Trainee'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m8='y';	
}
$sql="select count(*) from temp02 where trim(section)='Pilot Plant 2'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m9='y';	
}
$sql="select count(*) from temp02 where trim(section)='Pilot Plant 6'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m10='y';	
}
$sql="select count(*) from temp02 where trim(section)='SEP 1 Extractor'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m11='y';	
}
$sql="select count(*) from temp02 where trim(section)='SEP 1 Pre Treatment'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m12='y';	
}

$sql="select count(*) from temp02 where trim(section)='SEP 1 Stripper'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if(trim($myrow03[0])>0){
  $m13='y';	
}
//echo 'ZZZZZ'.$m13; 
	 // echo $sql; 
$sql="select count(*) from temp02 where trim(section)='SEP 2 Extractor'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m14='y';	
}
$sql="select count(*) from temp02 where trim(section)='SEP 2 Pre Treatment'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m15='y';	
}
$sql="select count(*) from temp02 where trim(section)='SEP 2 Stripper'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m16='y';	
}
$sql="select count(*) from temp02 where trim(section)='Helper'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$myrow03 = DB_fetch_row($result03);
if($myrow03[0]>0){
  $m17='y';	
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
<tr><td><input type="checkbox" name="chkl0" <?php  if($m0=="y"){echo 'checked=checked'; echo " value='Boiler'";} else{  echo "value='Boiler'";} ?>>01-Boiler<br /> </td><td><input type="checkbox" name="chkl9" <?php  if($m9=="y"){echo 'checked=checked';echo " value='Pilot Plant 2'";} else{ echo " value='Pilot Plant 2'";}?>>10-Pilot Plant 2<br /></td></tr> 
<tr><td><input type="checkbox" name="chkl1" <?php  if($m1=="y"){echo 'checked=checked';echo " value='Effluent Treatment Plant'";} else{ echo "value='Effluent Treatment Plant'";}?>>02-Effluent Treatment Plant<br /></td><td><input type="checkbox" name="chkl10" <?php  if($m10=="y"){echo 'checked=checked';echo " value='Pilot Plant 6'";} else{ echo "value='Pilot Plant 6'";}?>>11-Pilot Plant 6<br /></td></tr>  
<tr><td><input type="checkbox" name="chkl2" <?php  if($m2=="y"){echo 'checked=checked';echo " value='Electrician'";} else{ echo "value='Electrician'";}?>>03-Electrician<br /> </td><td><input type="checkbox" name="chkl11" <?php  if($m11=="y"){echo 'checked=checked';echo " value='SEP 1 Extractor'";} else{ echo "value= 'SEP 1 Extractor'";}?>>12-SEP 1 Extractor<br /></td></tr> 
<tr><td><input type="checkbox" name="chkl3" <?php  if($m3=="y"){echo 'checked=checked';echo " value='EOD'";} else{ echo "value='EOD'";}?>>04-EOD<br /></td><td><input type="checkbox" name="chkl12" <?php  if($m12=="y"){echo 'checked=checked';echo " value='SEP 1 Pre Treatment'";} else{ echo "value='SEP 1 Pre Treatment'";}?>>13-SEP 1 Pre Treatment<br /></td></tr>  

<tr><td><input type="checkbox" name="chkl4" <?php  if($m4=="y"){echo 'checked=checked';echo " value='FGS 1 - Blending'";} else{ echo "value='FGS 1 - Blending'";}?>>05-FGS 1 - Blending<br /></td><td><input type="checkbox" name="chkl13" <?php  if($m13=="y"){echo 'checked=checked';echo " value='SEP 1 Stripper'";} else{ echo "value='SEP 1 Stripper'";}?> >14-SEP 1 Stripper<br /></td></tr>
 
<tr><td><input type="checkbox" name="chkl5" <?php  if($m5=="y"){echo 'checked=checked';echo " value='FGS 1 - Filling and Packing'";} else{ echo "value='FGS 1 - Filling and Packing'";}?>>06-FGS 1 - Filling and Packing<br /></td><td><input type="checkbox" name="chkl14" <?php  if($m14=="y"){echo 'checked=checked';echo " value='SEP 2 Extractor'";} else{ echo "value='SEP 2 Extractor'";}?>>15-SEP 2 Extractor<br /></td></tr>  
<tr><td><input type="checkbox" name="chkl6" <?php  if($m6=="y"){echo 'checked=checked';echo " value='FGS 2'";} else{ echo "value='FGS 2'";}?>>07-FGS 2<br /></td><td><input type="checkbox" name="chkl15" <?php  if($m15=="y"){echo 'checked=checked';echo " value='SEP 2 Pre Treatment'";} else{ echo "value='SEP 2 Pre Treatment'";}?>>16-SEP 2 Pre Treatment<br /></td></tr>  
<tr><td><input type="checkbox" name="chkl7" <?php  if($m7=="y"){echo 'checked=checked';echo " value='Lab Helper'";} else{ echo "value='Lab Helper'";}?>>08-Lab Helper<br /></td><td><input type="checkbox" name="chkl16" <?php  if($m16=="y"){echo 'checked=checked';echo " value='SEP 2 Stripper'";} else{ echo "value='SEP 2 Stripper'";}?>>17-SEP 2 Stripper<br /></td></tr>  
<tr><td><input type="checkbox" name="chkl8" <?php  if($m8=="y"){echo 'checked=checked';echo " value='Operator Trainee'";} else{ echo "value='Operator Trainee'";}?>>09-Operator Trainee<br /></td><td><input type="checkbox" name="chkl17" <?php  if($m17=="y"){echo 'checked=checked';echo " value='Helper'";} else{ echo "value=Helper";}?>>18-Helper<br /></td></tr>  

</table>   
<br>  
<input type="submit" name="Submit" value="Submit">  
</form>  
</body>  
</html> 

<?php  
$checkbox1 = $_POST['chkl'] ;   
if ($_POST["Submit" ]=="Submit")  
{ 
$sql="delete from temp01";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);

//for ($i=0; $i<sizeof ($checkbox1);$i++) {  
for ($i=0; $i<18;$i++) {  
//echo $i;
echo '<br>KKKK'.$_POST['chkl'.$i]; 
//$query="INSERT INTO temp01 (section) VALUES ('".$checkboxl[$i]. "')";  
if(trim($_POST['chkl'.$i])<>''){
$query="INSERT INTO temp01 (section,employeeid,name,area) VALUES ('".$_POST['chkl'.$i]. "','".$_POST['empno']."','".$_POST['realname']."','".$_POST['empno']."')";  
mysql_query($query) or die(mysql_error()); 
} 
}  
$sql="delete from clmsusersection where trim(employeeid)='". trim($_POST['empno'])."'";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
$sql="insert into  clmsusersection select * from temp01";
$result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg); 

//echo 'FFFF'.$_POST['chkl0'];
echo "Records  Updated";  
 echo '<br><td><td><a href="manpowerentry.php">' . _('Back to Data Entry Form') . '</a><BR>';


if($_SESSION['UserID']=='301025'){
echo' <td><td><left><INPUT type="image" name="submit"  style="width:60px;height:60px;" src="../rcs/includes/back_button.png" VALUE="Back" onClick="history.go(-2);return true;"><p style="font-size:40;">&nbsp;&nbsp;Back to Data Entry form</p></td>';

     $mheading="User-Workcenter Updation";
     $sql="select Employeeid,Name,Section,Area from clmsusersection  order by employeeid  ";
   $sql="select * from temp01 ";

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
     if($myrow03[20]==0){
		$myrow03[20]=' ';  
		
	 }

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
               if ($myrow03[111]=='Kalamassery'){
		   print("<TD><b><center><font colorrr='dreddd'>$myrow03[$column_num]</font></b></center></TD>\n");
               }else { 
		   print("<TD><center>$myrow03[$column_num]</center></TD>\n");
               } 
            }
        }
 		echo '<TD><A HREF="'. $rootpath . '/notyetkancor.php?' . SID . '&EmployeeID=' . $myrow[0] . '">' . _('Edit/Delete') . '</A></TD>';
       echo'</tr>';  
    }
    echo'</table>';
    echo '</div>';

	

 


}
}  
?>  