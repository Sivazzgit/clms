<?php
if(!isset( $PageSecurity ))
{
 $PageSecurity = 1;
	include('includes/sess.inc');
	date_default_timezone_set('	Asia/Kolkata');
}
?>

<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Kancor</title>
  
  
  <link rel='stylesheet prefetch' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>

      <style> 
      /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
      @import url('http://fonts.googleapis.com/css?family=Open+Sans:300,400,700');
@import url('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css');
body {
  color: #e7e9ec;;
 # background: #293949;
 #background: #2e5e4f; 
 #background: #FE5825;
 #background:  #7B86AA;
 background:  #0d5b2f;
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
.datadiv {
  background-color: #292a2a;
  padding-left:50px;
  border: 3px solid white;
  border-radius: 10px;
  box-shadow: 0 8px 20px -6px black; 
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
	<img src="./includes/images/logokamcor.png">  
	</td>	
	<td border=""><center><h2 style="margin-left:15%;color: #003d86;text-shadow: 0px 3px 3px rgba(91, 139, 101, 0.5);" >Kancor Ingredients Ltd-Angamaly <br>Manpower Management System</h2></center></td>
	
	<td  border="" style="width:5%;padding:0px;">	<img src="./includes/images/logokamcor.png">  
</td>
</tr>	
</table>
<table style="width:100%;">
<tr style="background-color:#7d636030;box-shadow: 1px 2px 4px rgba(0, 0, 0, .5);border-radius:10px;display:table-cell;border:2px solid #cdd34b;height:20px;padding:0px; margin:0px;"><hr style="padding:0px; margin:0px;">
<td style="height:20px;margin-bottom:0px"  > <img class="selectnavigation" onclickkk="clickedmenu()" title="Display the menu" src="./images/menu.png" style='width:30px;height:30px;text-align:right; font-size:20px;cursor:pointer'><span style="margin-top:-1;color:white;">Menu</span></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td colspan="3"> <a href="PwordChange.php">Password Change </a></td>
<td style="height:20px;margin-bottom:0px;width:90%;display:table-cell;margin-top:0%;" align ="left">
	<?php 
	$name =
	$_SESSION['lastname'];
	echo '<figure style="margin-top:0%;margin-bottom:-1;width:50px height:20px;text-align:right;">
	<img src="./images/'.$_SESSION["UserID"].'.jpg" onclick="return profilupdate();" onerror={this.src="./uploads/Default.png"}; style="cursor:pointer;border-bottom:solid 0px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);border-radius:5px" height="30" width="30" align="center" alt="my-image">
	<figurecaption><span style="font-weight:bold;color: #F1EBE7;">User  :'.$_SESSION["UsersRealName"].' </span></figurecaption> 
	</figure>'; 
	?>
		</td>
		<td  style="width:30%;margin-bottom:0px;height:20px;padding:0px;text-align:right;"><a href="logoutfull.php">Log out</a>
	 <img  title="Back to previous page" src="./images/back.png" style='margin-top:0%;width:30px;height:30px;text-align:right; font-size:20px;cursor:pointer' onclick=' onclickback();'></img>
    </td>
	</tr></tbody></table>
	<div id="topmenu" class="top-menu" style="display:none;">
   <aside class="sidebar">
  <div id="leftside-navigation" class="nano">
    <ul class="nano-content">
      <li>
        <a href="kancormain.php"><i class="fa fa-dashboard"></i><span>HOME</span></a>
      </li>
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-cogs"></i><span>BIOMETRIC</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
				 <li>		<a href="prlSelectEmployee.php">Employee Registration</a> 
				</li>
				<li>		<a href="UpdateMonthlyPunching.php">Punching Data Upload</a> 
				</li>
				<li>		<a href="punchingonline.php">INPunching Online</a> 
				</li>
				<li>		<a href="punchingonlineout.php">OUTPunching Online)</a> 
				</li>
				<li>		<a href="punchingreportfromclock.php">Clock Punching Report</a> 
				</li>
				<li>		<a href="punchingommission.php">Punching Regularisation</a>  
				</li>
				<li>		<a href="musterolreportanymonth.php">Attendance Monitoring</a> 
				</li>
        </ul>
      </li>
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-table"></i><span>MANNING</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
				<li>		<a href="notyetkancor.php">Workcenter Master Updation</a>
				</li>
					<li>	<a href="manpowerentry.php">Deployment at work Center</a>
				</li>					
			</li>
			<!---------Add ur contents here --------->
        </ul>
      </li>
 
     <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-table"></i><span>ALLOWANCES</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
				<li>		<a href="notyetkancor.php">Monthly Allowances</a>
				</li>
					<li>	<a href="notyetkancor.php">Annual Allowances</a>
				</li>					
					<li>	<a href="notyetkancor.php">SAP Integration</a>
				</li>					
			</li>
			<!---------Add ur contents here --------->
        </ul>
      </li>

 <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa fa-tasks"></i><span>LEAVE MANAGEMENT</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
  			<li>		<a href="lent3.php">Leave Application</a>  
					</li><li><a href="notyetkancor.php">Leave Crediting</a>
						</li><li><a href="notyetkancor.php">Leave Encashment</a>
					</li><li><a href="notyetkancor.php">Leave Balance View</a>
				</li> 
        </ul>
      </li>
 

 <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-envelope"></i><span>SAFETY & TRAINING</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
      <li>	<a href="notyetkancor.php">PPE Supply</a>
	</li><li>			<a href="notyetkancor.php">Near miss Reporting</a>
	</li><li>			<a href="notyetkancor.php">Incidents Reporting</a>
	</li><li>			<a href="notyetkancor.php">Analysis</a>
	</li><li>			<a href="notyetkancor.php">Skill Development</a>
        
        </ul>
      </li>
 <!-------
 <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-bar-chart-o"></i><span>Finshed Goods</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
        	<li><a href="EsiDeductionViewFull.php">Finshed Goods Storage </a>
				</li><li>		<a href="notyetft.php">Packing</a>
					</li><li><a href="notyetft.php">Despatch</a>
					</li><li><a href="notyetft.php">Gate Pass</a>
					</li><li><a href="notyetft.php.php">Transport</a>
        </ul>
      </li>
	
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-map-marker"></i><span>Manpower</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
          <li><a href="notyetft.php">Employee Master</a>
          </li>
          <li><a href="notyetft.php">Statutory Requirements</a>
          </li>
        </ul>
      </li>
	------>
	   <!----
      <li class="sub-menu">
        <a href="typography.html"><i class="fa fa-text-height"></i><span>Typography</span></a>
      </li>
	  -->
	   <!----
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-file"></i><span>Pages</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
          <li><a href="pages-blank.html">Blank Page</a>
          </li>
          <li><a href="pages-login.html">Login</a>
          </li>
          <li><a href="pages-sign-up.html">Sign Up</a>
          </li>
          <li><a href="pages-calendar.html">Calendar</a>
          </li>
          <li><a href="pages-timeline.html">Timeline</a>
          </li>
          <li><a href="pages-404.html">404</a>
          </li>
          <li><a href="pages-500.html">500</a>
          </li>
        </ul>
      </li>
	  -->
    </ul>
  </div>
</aside>
</div>
 <?php 
 echo'<center><h2>'. $title.'</h2>';
 ?>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js'></script>

    <script src="js/index.js"></script>