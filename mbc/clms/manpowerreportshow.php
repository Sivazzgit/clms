<?php
$PageSecurity = 10;  
include('includes/sess.inc');
include('includes/headerclms.inc');
$sql = "select dt as'Date',Workcenter as'User',Shift,Employeeid,Employeename,HRS,Section as 'Working Section',category as'Vendor'
 from clmsmanpower".$_GET['mmonth']." where trim(dt)='".trim($_GET['stdate'])."' and trim(shift)='".trim($_GET['shift'])."' 
and trim(section)='".trim($_GET['section'])."'";

		
    $DbgMsg = _('The SQL that was used to insert the employee but failed was');
    $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
    $column_count = mysql_num_fields($result03);

//	echo "<FORM METHOD='post' id='f1' ACTION='mfgproductiondataupdation1.php'";
$mheading="Contract Labour Data Updated Date :".$_GET['stdate']." Shift : ".$_GET['shift']." Section : ".$_GET['section'];
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
   
	
		
echo'</div';
 
?>