<?php

$PageSecurity = 10;  
include('includes/sess.inc');
$title = _('User-Section Updation');
//include('includes/headerkancor.inc');
include('includes/headerclms.inc'); 
	echo '<left><a href="approvalmatrix.php">' . _('Approval Matrix') . '</a><BR>';

echo "<FORM METHOD='post' id='tk1' action='usersectionupdation2.php'>";

?>
  

<html>
  <head>
    <!-- Load jQuery from Google's CDN -->
    <!-- Load jQuery UI CSS  -->
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="script.js"></script>
    
    <link rel="stylesheet" href="runnable.css" />
   <script type="text/javascript">
    function ddate{ 
      return "01/02/2016";
    }
 </script>

  </head>

<CENTER><TABLE border="1" style="background-color:1#F0E68C">
 <TR><TD><align=right><b>Employee No</b></TD><TD><INPUT TYPE='text' NAME='empno'value="" ></TD></TR> 
 </table>
</div>
<?php

echo "<p><CENTER><INPUT TYPE='Submit' NAME='submit0' VALUE='" . _('Submit ') . "'>";


 echo'</FORM>';
 
      $mheading="User-Workcenter Updation";
 
     $sql="create temporary table temp1 select Employeeid,Name,Section,'xxxxxxxxxxxx' as'apid','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' as'apname' from clmsusersection where substr(employeeid,1,5)='clms1' order by employeeid  ";
	 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
     $sql="update  temp1 a, clmsapprovalmatrix b set a.apid=trim(b.approver) where trim(a.employeeid)=trim(b.creater) "; 

	 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
     $md="ALTER TABLE temp1 CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";	 
 	 $result03 = DB_query($md, $db, $ErrMsg, $DbgMsg);
    $sql="update  temp1 a, www_users b set a.apname=b.realname where trim(a.apid)=trim(b.userid) ";
	 $result03 = DB_query($sql, $db, $ErrMsg, $DbgMsg);
// echo $sql;

 $sql="select Employeeid,Name as 'Section Incharge',Section,apname as'Plant incharge' from temp1 where substr(employeeid,1,5)='clms1' and substr(trim(apname),1,4)<>'xxxx' order by apname  ";
//echo $sql;
 
 //echo $sql; 
 //   $sql="select * from clmsusersection  order by employeeid  ";

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
  //   if($myrow03[20]==0){
//		$myrow03[20]=' ';  
		
//	 }

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
 		//echo '<TD><A HREF="'. $rootpath . '/notyetkancor.php?' . SID . '&EmployeeID=' . $myrow[0] . '">' . _('Edit/Delete') . '</A></TD>';
       echo'</tr>';  
    }
    echo'</table>';
    echo '</div>';


?>