<?php

/* $Revision: 1.0 $ */



$PageSecurity = 15;



//$PageSecurity = 10;  

include('includes/sess.inc');

$title = _('Contract Labour Intend and Engagement');

include('includes/headerclms.inc');

/* 

if($_SESSION['UserID']=='rmsuser'){

	include('mfgheader2.php');

}else{

	include('mfgheader.php');



}

*/

 echo'   <script src="script.js"></script>';

	$Sdate=date("01-m-Y");

	$Todate=date("d-m-Y");



	echo "<FORM METHOD='post' ACTION='" . $_SERVER['PHP_SELF'] . "" . SID . "'>";

	echo "<INPUT TYPE='hidden' NAME='New' VALUE='Yes'>";

	echo '<CENTER><TABLE>';

        echo '<br>'; 

    echo ' <TR><TD align="right" width=200 height=20> <b>Date From : </TD><TD width=200 height=20><input type="text" name="datepicker" id="datepicker" value="'.$Sdate.'"></b></TD>

			   <TD align="right" width=200 height=20> <b>To : </TD><TD width=200 height=20><input type="text" name="datepicker1" id="datepicker1" value="'.$Todate.'"></b></TD>

	</TR> ';

	echo "</TR></TABLE><br><br><p><center><INPUT TYPE='Submit' NAME='submit1' VALUE='" . _('Show') . "'>";



	echo '</FORM><br>'; 

	

  if (isset($_POST['submit1'])) {

// echo"XXXXXXXX";

$Sdate=$_POST['datepicker'];

$Todate=$_POST['datepicker1'];

//$Edate=$_POST['datepicker1']; 

$Edate=$_POST['datepicker']; 

$mmonth1=substr($Sdate,3,2).substr($Sdate,8,2);
//echo $mmonth1;

  }
$mmonth1='0625';

//$Sdate='10-10-2021';

$kkdept='CARE & CONCERN';



$sql="create temporary table t01 select distinct dt from clmsshiftroster".$mmonth1;

//echo $sql;

$result = DB_query($sql, $db); 



//$sqlr="select sum(ashift),sum(bshift),sum(cshift) from clmsintend where vendor='".$kkdept."' and STR_TO_DATE('".$Sdate."','%d-%m-%Y')  between str_to_date(stdate,'%d-%m-%Y') and str_to_date(endate,'%d-%m-%Y') group by vendor";

 





$sqlr="create temporary table ti01 select distinct a.dt,b.section,b.vendor, sum(b.ashift) as'ashiftint',sum(b.bshift) as'bShiftint',sum(b.cshift) as 'cShiftint'

,sum(b.total) as'totalint',000 as'ashifteng',000 as'bshifteng',000 as'cshifteng',000 as'totaleng' from t01  a,clmsintend b where  STR_TO_DATE(trim(a.dt),'%d-%m-%Y')  between str_to_date(trim(b.stdate),'%d-%m-%Y') and str_to_date(trim(b.endate),'%d-%m-%Y') group by a.dt,b.section,b.vendor  ";

$resultr = DB_query($sqlr, $db); 

//$mvend='CARE & CONCERN';

$sql00 = 'SELECT distinct name from clmsvendormaster ';

$result00 = DB_query($sql00, $db);

while ($myrow00 = DB_fetch_row($result00))

{

$mvend=trim($myrow00[0]);



$sql="select dt from t01";

$result = DB_query($sql, $db); 

while ($myrow = DB_fetch_row($result))  

  {

    $mdt=trim($myrow[0]);



    $sql0="select section from clmssectionmaster";

	$result0 = DB_query($sql0, $db); 

	while ($myrow0 = DB_fetch_row($result0))  

	{

	$msec=trim($myrow0[0]);

    $sql1="select count(*) from ti01 where trim(dt)='".$mdt."' and trim(section) like '".$msec."%'  and trim(vendor) like '".$mvend."%'"; 

//echo $sql1;

	$result1 = DB_query($sql1, $db);  

    $myrow1 = DB_fetch_row($result1); 

	if($myrow1[0]==0){

		$sql2="insert into ti01(dt,section,vendor) values('".$mdt."','".$msec."','".$mvend."')"; 

//echo $myrow1[0]. $sql1;

		$result2 = DB_query($sql2, $db); 

	} 

	

   }

  } 

}



$sqlr="create temporary table ti02 select dt,section,category as'vendor',shift,000 as'ashifteng',000 as'bshifteng',000 as'cshifteng',000 as'totaleng' from clmsmanpower".$mmonth1;

$resultr = DB_query($sqlr, $db); 



$sqlr="update  ti02 set ashifteng=1 where trim(shift)='A'";

$resultr = DB_query($sqlr, $db); 



$sqlr="update  ti02 set bshifteng=1 where trim(shift)='B'";

$resultr = DB_query($sqlr, $db); 

$sqlr="update  ti02 set cshifteng=1 where trim(shift)='C'";

$resultr = DB_query($sqlr, $db); 



$sqlr="update  ti02 set totaleng=ashifteng+bshifteng+cshifteng";

$resultr = DB_query($sqlr, $db); 

$sqlr="create temporary table ti03 select dt,section,vendor,sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng)

 as'totaleng' from ti02 group by dt,section,vendor";

//echo $sqlr;

$resultr = DB_query($sqlr, $db); 

$sqlr="update  ti01 a, ti03 b  set a.totaleng=b.totaleng where trim(a.dt)=trim(b.dt) and trim(a.section)=trim(b.section) and trim(a.vendor)=trim(b.vendor)";

$resultr = DB_query($sqlr, $db);

//echo $sqlr;

 

$sqlr="update  ti01 a, ti03 b  set a.ashifteng=b.ashifteng where trim(a.dt)=trim(b.dt) and trim(a.section)=trim(b.section) and trim(a.vendor)=trim(b.vendor)";

$resultr = DB_query($sqlr, $db); 

$sqlr="update  ti01 a, ti03 b  set a.bshifteng=b.bshifteng where trim(a.dt)=trim(b.dt) and trim(a.section)=trim(b.section) and trim(a.vendor)=trim(b.vendor)";

$resultr = DB_query($sqlr, $db); 

$sqlr="update  ti01 a, ti03 b  set a.cshifteng=b.cshifteng where trim(a.dt)=trim(b.dt) and trim(a.section)=trim(b.section) and trim(a.vendor)=trim(b.vendor)";

$resultr = DB_query($sqlr, $db); 



//$sqlr="select * from ti01  where  STR_TO_DATE(trim(dt),'%d-%m-%Y')  between str_to_date(trim('".$Sdate."'),'%d-%m-%Y') and str_to_date(trim('".$Todate."'),'%d-%m-%Y') ";

//echo $sqlr;

//$resultr = DB_query($sqlr, $db);



 if($_SESSION['UserID']=='clms01'){

	$kkdept='CARE & CONCERN';

}

if($_SESSION['UserID']=='clms02'){ 

	$kkdept='VASS GROUP';

}

if($_SESSION['UserID']=='clms03'){

	$kkdept='KINGDOM SECURITY';

}

 

 

 $sqlr="create temporary table ti04 select Vendor,dt,section,sum(ashiftint) as'ashiftint',sum(bshiftint) as'bshiftint',sum(cshiftint) as 'cshiftint'

,sum(totalint) as'totalint',sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng)

 as'totaleng' from ti01  where  STR_TO_DATE(trim(dt),'%d-%m-%Y')  between str_to_date(trim('".$Sdate."'),'%d-%m-%Y') and 

 str_to_date(trim('".$Todate."'),'%d-%m-%Y' ) and trim(vendor)='".$kkdept."' group by vendor,dt,section WITH ROLLUP  "; 

 

 /*

  $sqlr="create temporary table ti04 select Vendor,dt,'ALL' as 'section',sum(ashiftint) as'ashiftint',sum(bshiftint) as'bshiftint',sum(cshiftint) as 'cshiftint'

,sum(totalint) as'totalint',sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng)

 as'totaleng' from ti01  where  STR_TO_DATE(trim(dt),'%d-%m-%Y')  between str_to_date(trim('".$Sdate."'),'%d-%m-%Y') and 

 str_to_date(trim('".$Todate."'),'%d-%m-%Y' ) and trim(vendor)='".$kkdept."' group by vendor,dt WITH ROLLUP  "; 



  $sqlr="create temporary table ti04 select Vendor,'ALL' as 'dt',section,sum(ashiftint) as'ashiftint',sum(bshiftint) as'bshiftint',sum(cshiftint) as 'cshiftint'

,sum(totalint) as'totalint',sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng)

 as'totaleng' from ti01  where  STR_TO_DATE(trim(dt),'%d-%m-%Y')  between str_to_date(trim('".$Sdate."'),'%d-%m-%Y') and 

 str_to_date(trim('".$Todate."'),'%d-%m-%Y' ) and trim(vendor)='".$kkdept."' group by vendor,section WITH ROLLUP  "; 

 */

 if(substr($_SESSION['UserID'],0,5)=='clms0' ){

	echo '<center><a href="gatemanpowerentry.php">' . _('Back to Attendance Entry Form') . '</a><BR>';

 }

 

 if($_SESSION['UserID']=='clmsadmin'){  



  $sqlr="create temporary table ti04 select Vendor,dt,section,sum(ashiftint) as'ashiftint',sum(bshiftint) as'bshiftint',sum(cshiftint) as 'cshiftint'

,sum(totalint) as'totalint',sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng)

 as'totaleng' from ti01  where  STR_TO_DATE(trim(dt),'%d-%m-%Y')  between str_to_date(trim('".$Sdate."'),'%d-%m-%Y') and 

 str_to_date(trim('".$Todate."'),'%d-%m-%Y' ) group by vendor,dt,section WITH ROLLUP  "; 



 //Date wise



 $sqlr="create temporary table ti04 select ''as 'Vendor',''as 'section',dt,sum(ashiftint) as'ashiftint',sum(bshiftint) as'bshiftint',sum(cshiftint) as 'cshiftint'

,sum(totalint) as'totalint',sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng)

 as'totaleng' from ti01  where  STR_TO_DATE(trim(dt),'%d-%m-%Y')  between str_to_date(trim('".$Sdate."'),'%d-%m-%Y') and 

 str_to_date(trim('".$Todate."'),'%d-%m-%Y' ) group by dt WITH ROLLUP  "; 



  //Section wise wise

/*

 $sqlr="create temporary table ti04 select ''as 'Vendor',section,' ' as'dt',sum(ashiftint) as'ashiftint',sum(bshiftint) as'bshiftint',sum(cshiftint) as 'cshiftint'

,sum(totalint) as'totalint',sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng)

 as'totaleng' from ti01  where  STR_TO_DATE(trim(dt),'%d-%m-%Y')  between str_to_date(trim('".$Sdate."'),'%d-%m-%Y') and 

 str_to_date(trim('".$Todate."'),'%d-%m-%Y' ) group by section WITH ROLLUP  "; 

*/

 }

 

 if(substr($_SESSION['UserID'],0,5)=='clms1' ){

//$md="select a.* from  clmsintend a,clmsusersection b where trim(a.section)=trim(b.section) and trim(b.employeeid)='".$_SESSION['UserID']."' order by recordid desc";

  $sqlr="create temporary table ti04 select Vendor,dt,a.section,sum(ashiftint) as'ashiftint',sum(bshiftint) as'bshiftint',sum(cshiftint) as 'cshiftint'

,sum(totalint) as'totalint',sum(ashifteng) as'ashifteng',sum(bshifteng) as'bshifteng',sum(cshifteng) as'cshifteng',sum(totaleng)

 as'totaleng' from ti01 a, clmsusersection b  where trim(a.section)=trim(b.section) and trim(b.employeeid)='".$_SESSION['UserID']."'  and   STR_TO_DATE(trim(dt),'%d-%m-%Y')  between str_to_date(trim('".$Sdate."'),'%d-%m-%Y') and 

 str_to_date(trim('".$Todate."'),'%d-%m-%Y' ) group by vendor,dt,a.section WITH ROLLUP  "; 

	

}

 

//echo $sqlr; 

 $resultr = DB_query($sqlr, $db); 

$sqlr="update  ti04 set section='Date wise Total' where trim(section)='' and trim(dt)<>'' and trim(vendor)<>''";

$resultr = DB_query($sqlr, $db); 

$sqlr="update  ti04 set section='Vendor wise Total' where trim(section)='' and trim(dt)='' and trim(vendor)<>''";

$resultr = DB_query($sqlr, $db); 

$sqlr="update  ti04 set section='Grand Total' where trim(section)='' and trim(dt)='' and trim(vendor)=''";

$resultr = DB_query($sqlr, $db); 





$sqlr="select * from ti04 where totalint+totaleng>0 ";







$resultr = DB_query($sqlr, $db); 



//echo $sqlr;

 $column_count = mysql_num_fields($resultr);

$mheading='Contract Labour Intend and Engagement';

//$myrowr = DB_fetch_array($resultr);

//echo "B Shift Nos=".$myrowr[1];

    echo'<center><table id="tbl1" Border="1" width ="50%"  cellspacing="0" cellpaddin="0">';

	    print("<TR>");

	echo "<th colspan='".($column_count)."'>";

    echo'<p>'.$mheading.'</p></th></tr><tr>';

	    print("<TR>");

	echo "<th colspan='3'><p>Details</p></th><th colspan='4'><p>Intend</p></th><th colspan='4'><p>Engaged</p></th></tr><tr>";



    for($column_num = 0; $column_num < $column_count; $column_num++) 

    {

        $field_name = mysql_field_name($resultr, $column_num);

        print("<TH>$field_name</TH>\n");

    }

    print("</TR>");     



   while ($myrow03 = DB_fetch_row($resultr))  

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

   

	

?>