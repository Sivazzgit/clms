<?php

if(!isset( $PageSecurity ))

{

 $PageSecurity = 1;

	include('includes/sess.inc');

	date_default_timezone_set('	Asia/Kolkata');

}



if(substr($_SESSION['UserID'],0,4)=='clms' ){

	

}else{

	exit("Not allowed. Unauthorised User");

	

}



echo "<table><tr><td style='padding:10px'>";

echo "Login Time ".$_SESSION['logtime']; echo "</td><td style='padding:10px'> Session Time Remaining</td><td style='padding:10px'><div id='sessiontime'> Will be required to login after".$_SESSION['end_time']."</div></td>"; 

echo "<td style='padding:10px'>Please save data when the count is less than 100</td></tr></table>";

echo "<script>var sessionTimeout=".$logindiff.";</script>";

echo'<script type="text/javascript" src="./includes/sessiontime.js?v40"></script>'; 





?>



<!DOCTYPE html>

<html >

<head>

  <meta charset="UTF-8">

  <title>CLMS</title>

  

  

  <link rel='stylesheet prefetch' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>



      <style>

      /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */

      @import url('https://fonts.googleapis.com/css?family=Open+Sans:300,400,700');

@import url('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css');

body {

  color: #e7e9ec;

 # background: #293949;

 background: #2e5e4f;

  font-family: 'Open Sans', sans-serif;

  padding: 0;

  margin: 0;

  text-rendering: optimizeLegibility;

  -webkit-font-smoothing: antialiased;

}



/* unvisited link */

a:link {

    color: #f4f4f4;

}



/* visited link */

a:visited {

    color: #a7d6f7;

}



/* mouse over link */

a:hover {

    color: #ded9b7;

}



/* selected link */

a:active {

    color: #a7d6f7;

}



.sidebar-toggle {

  margin-left: -240px;

}

.sidebar {

  width: 240px;

  height: 100%;

  background: #1a5541;

  position: absolute;

  -webkit-transition: all 0.3s ease-in-out;

  -moz-transition: all 0.3s ease-in-out;

  -o-transition: all 0.3s ease-in-out;

  -ms-transition: all 0.3s ease-in-out;

  transition: all 0.3s ease-in-out;

  z-index: 100;

}

.sidebar #leftside-navigation ul,

.sidebar #leftside-navigation ul ul {

  margin: -2px 0 0;

  padding: 0;

}

.sidebar #leftside-navigation ul li {

  list-style-type: none;

  border-bottom: 1px solid rgba(255, 255, 255, 0.05);

}

.sidebar #leftside-navigation ul li.active > a {

  color: #1abc9c;

}

.sidebar #leftside-navigation ul li.active ul {

  display: block;

}

.sidebar #leftside-navigation ul li a {

  color: #aeb2b7;

  text-decoration: none;

  display: block;

  padding: 18px 0 18px 25px;

  font-size: 12px;

  outline: 0;

  -webkit-transition: all 200ms ease-in;

  -moz-transition: all 200ms ease-in;

  -o-transition: all 200ms ease-in;

  -ms-transition: all 200ms ease-in;

  transition: all 200ms ease-in;

}

.sidebar #leftside-navigation ul li a:hover {

  color: #1abc9c;

}

.sidebar #leftside-navigation ul li a span {

  display: inline-block;

}

.sidebar #leftside-navigation ul li a i {

  width: 20px;

}

.sidebar #leftside-navigation ul li a i .fa-angle-left,

.sidebar #leftside-navigation ul li a i .fa-angle-right {

  padding-top: 3px;

}

.sidebar #leftside-navigation ul ul {

  display: none;

}

.sidebar #leftside-navigation ul ul li {

  background: #27453c;

  margin-bottom: 0;

  margin-left: 0;

  margin-right: 0;

  border-bottom: none;

}

.sidebar #leftside-navigation ul ul li a {

  font-size: 12px;

  padding-top: 13px;

  padding-bottom: 13px;

  color: #aeb2b7;

}



.mytd th{



background-color:#4c93a7;

#background-color:#1abc9c;

color:white;

    border: 1px solid #ddd;

    padding: 8px;

}

.mytd td{	

color:#3b5648; 

    border: 1px solid #ddd;

    padding: 8px;

}

.mytd a{	

color:#3b5648; 

}

.mytd {

    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;

    border-collapse: collapse;

    width: 100%;

	box-shadow:0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.76);

	margin-left:10%;

}



.mytd  tr:nth-child(even){background-color: #f2f2f2;}

.mytd  tr:nth-child(odd){background-color: #ffffff;}

.mytd tr:hover {background-color: #ddd;}



.prdtb td{

color:white;

}



td{

color:white;

}

.tableheader {

#background-color:#1abc9c;

background-color:#4c93a7;

color:white;

}

td.tableheader {

#background-color:#1abc9c;



background-color:#4c93a7;

color:white;

}

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>



</head>



 <script type="text/javascript" src="./script.js"></script>



<script src="./includes/menu-vertical.js?v=170802">



  $(document).ready(function(){

$(".top-menu").slideupmenu({

slideUpSpeed: 150,

slideDownSpeed: 200,

ease: "easeOutQuad",

stopQueue: true

}); 

}); 

</script>

 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<body>



<table class="headertb1" style="width:100%;border-spacing: 0px;border-collapse: separate;border-color: white;" border=""><tbody><tr style="background-color:white;"> 

	<td  border=""style="width:5%;padding:0px;">

<?php

if(substr($_SESSION['UserID'],0,5)<>'clms0' ){



	//echo'<img  src="./images/logokamcor.png">';

	//echo'<img width="350" height="100" src="./images/Mane1.jpg">'; 

	echo'<img width="350" height="100" src="./images/ramlogo.jpg">'; 

	echo'</td>';	

	echo'<td border=""><center><h2 style="margin-left:15%;color: #003d86;text-shadow: 0px 3px 3px rgba(91, 139, 101, 0.5);" >Muthiah Beverage and Confectionery (Pvt) Ltd
 <br>Contract Labour Management System</center></h2></td>';

	//echo'	<td  border=""><img  src="./images/logokamcor.png">';

//	echo'	<td  border=""><img width="350" height="100" src="./images/Mane1.jpg">';

	echo'	<td  border=""><img width="350" height="100" src="./images/ramlogo.jpg">';



}else{



	echo'<img  src="./images/ramlogo.jpg">';

	echo'</td>';	

	echo'<td border=""><center><h2 style="margin-left:15%;color: #003d86;text-shadow: 0px 3px 3px rgba(91, 139, 101, 0.5);" >Muthiah Beverage and Confectionery (Pvt) Ltd
 <br>Contract Labour Management System </center></h2></td>';

	

echo'	<td  border=""><img  src="./images/ramlogo.jpg">';



}	

?>	

 </td> 

</tr>	

</table>

<table style="width:100%;">

<tr style="background-color:#7d636030;box-shadow: 1px 2px 4px rgba(0, 0, 0, .5);border-radius:10px;display:table-cell;border:2px solid #cdd34b;height:20px;padding:0px; margin:0px;"><hr style="padding:0px; margin:0px;">

<?php

if($_SESSION['UserID']=='clmsadmin'){

echo'<td style="height:20px;margin-bottom:0px" > <img class="selectnavigation" onclick="clickedmenu()" title="Display the menu" src="./images/menu.png" style="width:30px;height:30px;text-align:right; font-size:20px;cursor:pointer"><span style="margin-top:-1;color:white;">Menu</span></td>';

}else{

echo'<td hidden style="height:20px;margin-bottom:0px" > <img class="selectnavigation" onclick="clickedmenu()" title="Display the menu" src="./images/menu.png" style="width:30px;height:30px;text-align:right; font-size:20px;cursor:pointer"><span style="margin-top:-1;color:white;">Menu</span></td>';



}  

?>

<td style="height:20px;margin-bottom:0px;width:90%;display:table-cell;margin-top:0%;" align ="left">

	<?php 

	$name =

	$_SESSION['UsersRealName'];

	$EmployeeID=$_SESSION['UserID'];

	echo '<figure style="margin-top:0%;margin-bottom:-1;width:50px height:20px;">

	<img src="./uploads/'.$EmployeeID.'.jpg" onclick="return profilupdate();" onerror={this.src="./uploads/Default.png"}; style="cursor:pointer;border-bottom:solid 0px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);border-radius:5px" height="30" width="30" align="left" alt="my-image">

	<figurecaption><span style="font-weight:bold;color: #F1EBE7;" title="'.$_SESSION['UsersRealName'].'">'.$_SESSION['UserID'].' </span></figurecaption>

	</figure>'; 

	?>

		</td>

<td><a href="PwordChange.php">Password Change </a></td> 

		

		<td  style="width:30%;margin-bottom:0px;height:20px;padding:0px;text-align:right;"><a href="logoutfull.php">Log out</a>

	 <img  title="Back to previous page" src="./images/back.png" style='margin-top:0%;width:30px;height:30px;text-align:right; font-size:20px;cursor:pointer' onclick=' onclickback();'></img>

    </td>

	</tr></tbody></table>

	<div id="topmenu" class="top-menu" style="display:block;">

   <aside class="sidebar">

  <div id="leftside-navigation" class="nano">

    <ul class="nano-content">

      <li>

        <a href="clms.php"><i class="fa fa-home"></i><span>Home</span></a>

      </li>
<!------
      <li>

        <a href="billingmonthly.php"><i class="fa fa-inr"></i><span>Monthly Billing Details</span></a>

      </li>

      <li>

        <a href="prlSelectEmployee.php"><i class="fa fa-edit"></i><span>Add/Update Employee Details</span></a>

      </li> 

------>


       <li class="sub-menu">
         <a href="javascript:void(0);" ><i class="fa fa-desktop"></i><span>Contractor</span><i class="arrow fa fa-angle-right pull-right"></i></a> 

          <ul>

            <li>

            <a href="notyetkancor.php"></i><span>Labour Engagement Data Entry</span></a> 

          </li>


            <li>

            <a href="intend.php"></i><span>Intend Master View</span></a> 

          </li>

            <li>

            <a href="intendcalmonthly.php"></i><span>Intend and Engagement Section wise</span></a> 

          </li>

             <li>

            <a href="intendcalmonthlydatewisesummary.php"></i><span>Intend and Engagement Date wise </span></a> 

          </li>

<!---------
         <li>

            <a href="mustrolattendance1.php"></i><span>Mustrol View</span></a>

          </li>

          <li>

            <a href="shiftrosterview.php"></i><span>Shift Roster View</span></a>

          </li>

          <li>

            <a href="punchingattendancereport.php?"></i><span>Punching View</span></a> 

          </li>
--------->
        </ul> 

      </li> 
<!-------
      <li class="sub-menu">

         <a href="javascript:void(0);" ><i class="fa fa-upload"></i><span>Data Upload</span><i class="arrow fa fa-angle-right pull-right"></i></a> 

          <ul>

            <li>

              <a href="uploadshiftroster.php"><span>Shift Roster Upload</span></a> 

            </li> 

             <li>

              <a href="PunchingDataUploadinpunch.php"><span>IN PUNCH</span></a> 

            </li> 

            <li>

              <a href="PunchingDataUploadoutpunch.php"><span>OUT PUNCH</span></a> 

            </li> 

          </ul>

        </li> 

        <li>

          <a href="manpowerentryapprovalstatus.php"><i class="fa fa-check"></i><span>Approval Status</span></a>

         </li> 

         <li>

        <a href="punchingattendancereport.php"><i class="fa fa-clock-o"></i><span>Attendance card</span></a>

      </li> 
    </ul>
------->

  </div>

</aside>

</div>

 <?php 

 echo'<center><h2>'. $title.'</h2>';

 ?>

  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js'></script>



    <script src="js/index.js"></script>