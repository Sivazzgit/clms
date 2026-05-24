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
  <title>KCM HRMS</title>
  
  
  <link rel='stylesheet prefetch' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>

      <style>
      /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
      @import url('http://fonts.googleapis.com/css?family=Open+Sans:300,400,700');
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
    width: 80%;
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
	<img style="height:80px;width:240px;" src="./images/ramlogo.jpg">
	</td>	
	<td border=""><h2 style="margin-left:15%;color: #003d86;text-shadow: 0px 3px 3px rgba(91, 139, 101, 0.5);" >Human Resource Management System</h2></td>
	
	<td  border=""><img style="height:35px;width:35px;" src="./images/inside.png"><span style="color: #2fafef;
    font-family: -webkit-pictograph;
    font-style: italic;
    font-weight: 400;
    font-size: 25px;">nside</td>
</tr>	
</table>
<table style="width:100%;">
<tr style="background-color:#7d636030;box-shadow: 1px 2px 4px rgba(0, 0, 0, .5);border-radius:10px;display:table-cell;border:2px solid #cdd34b;height:20px;padding:0px; margin:0px;"><hr style="padding:0px; margin:0px;">
<td style="height:20px;margin-bottom:0px" > <img class="selectnavigation" onclick="clickedmenu()" title="Display the menu" src="./images/menu.png" style='width:30px;height:30px;text-align:right; font-size:20px;cursor:pointer'><span style="margin-top:-1;color:white;">Menu</span></td>
<td style="height:20px;margin-bottom:0px;width:90%;display:table-cell;margin-top:0%;" align ="left">
	<?php 
	$name =
	$_SESSION['lastname'];
	echo '<figure style="margin-top:0%;margin-bottom:-1;width:50px height:20px;text-align:right;">
	<img src="./uploads/'.$EmployeeID.'.jpg" onclick="return profilupdate();" onerror={this.src="./uploads/Default.png"}; style="cursor:pointer;border-bottom:solid 0px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);border-radius:5px" height="30" width="30" align="center" alt="my-image">
	<figurecaption><span style="font-weight:bold;color: #F1EBE7;">Test User </span></figurecaption>
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
        <a href="kcmmain.php"><i class="fa fa-dashboard"></i><span>Home</span></a>
      </li>
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-cogs"></i><span>Hiring</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
		  <li>	<a href="mpower.php">Manpower Requirement</a>
				 </li> <li>		<a href="headcountview.php">Head Count</a> 
				</li> <li>		<a href="manpowerviewcost.php">Employee Cost Budget</a> 
				</li> <li>		<a href="orgchart.php">Org Chart</a> 
				</li> <li>		<a href="jdview.php">Job Description</a>
				</li> <li>		<a href="appointmentorderview.php">Offer/Appointment Letter </a>
				</li> <li>		<a href="inductionformatview.php">Induction </a>
				</li> <li>		<a href="prlSelectEmployee.php">Org. Re Asignments</a>
				</li> <li>		<a href="prlSelectEmployee.php">Separation/Final Settlement</a>      
				</li> <li>		<a href="hrauditformat.php">HR Audit</a>      
        </ul>
      </li>
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-table"></i><span>Training</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
				<li>		<a href="RcsTrainingBudgetSetting.php">Training Budget</a>
				</li>
					<li>	<a href="RcsTrainingTopicMaster.php">Training Topic Master</a>
</li>					
					<li><a href="RcsTrainingmandaysplan.php">Training Plan</a>
	
</li>	<li><a href="RcsTrainingCalenderSetting.php">Training Calendar</a>
	</li>					<li><a href="RcsTrainingFacultyMaster.php">Training Faculty Master</a>
				</li>		<li><a href="RcsTrainingSylabusMaster.php">Training Sylabus Master</a>
				</li>		<li><a href="RcsTrainingNeedIdentificationMaster.php">Training Need Identification</a>
				</li>		<li><a href="RcsTrainingDataEntry.php">Training Nomination</a>
				</li>		<li><a href="notyet.php">Test/Exam</a>
					</li>	<li><a href="RcsTrainingDataEntry.php">Training Data Updation</a>
					</li>	<li><a href="RcsPostTrainingEvaluation.php">Post Training Eval.Data Updation</a>
						</li><li><a href="RcsPostTrainingEvaluation.php">Training Feed Back Recording</a>
						</li><li><a href="ReportCardAll.php">Training Card</a>
						</li><li><a href="ScoreList.php">Training Hours Details</a>
						</li><li><a href="notyet.php">Training Certificates</a>
						</li><li><a href="RcsTrainingBudgetSetting.php">Training Days Actual Vs Plan Monitoring</a>
						</li><li><a href="notyet.php">Training Budget Actual Vs Plan Monitoring</a>
						</li><li><a href="notyet.php">Graphic Presentation</a>
			</li>
			<!---------Add ur contents here --------->
        </ul>
      </li>
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa fa-tasks"></i><span>PMS</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
         <li><a href="goal.php">Goal Setting</a> 
			</li><li>		<a href="KraSetting.php">KRA(Objective) Setting</a>  
					</li><li><a href="KraEvaluation.php">Quarterly KRA Evaluations</a>
					</li><li><a href="notyet.php">Job Role Setting</a>
					</li><li><a href="notyet.php">Job Role Evaluation</a>
					</li><li><a href="budgetdemo.php">Budget Gap Analysis</a> 
					</li><li><a href="ReportCard.php">Report/Feed Back</a>
					</li><li><a href="CompetencySetting.php">Competency Setting</a>
					</li><li><a href="CompetencyEvaluation.php">Competency evaluation</a>
					</li><li><a href="prDialogue.php">Performance Review Dialogue</a>
					</li><li><a href="notyet.php">Annual Recommendations</a>
					</li><li><a href="catlratinganalysis.html">Potential Vs Performance Matrix</a>
        </ul>
      </li>
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-envelope"></i><span>Benefits</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
      <li>	<a href="ams.php">Attendance Management System</a>
	</li><li>			<a href="pay.php">Leave Management System</a>
	</li><li>			<a href="index.php">Payroll</a>
	</li><li>			<a href="Rcsoffcyclepayrollitems.php">Off Cycle Payroll</a>
	</li><li>			<a href="Rcswelfareitems.php">Welfare Actvities</a>
	</li><li>			<a href="notyet.php">Grieveance Handling</a>
		</li>			   
        
        </ul>
      </li>
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-bar-chart-o"></i><span>Statutory Complaince</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
        	<li><a href="EsiDeductionViewFull.php">ESI </a>
				</li><li>		<a href="pfreportfull.php">PF/Pension</a>
					</li><li><a href="gratuityreport.php">Gratuity</a>
					</li><li><a href="proftaxratecalaug.php">Prof.Tax</a>
					</li><li><a href="Bonusview17.php">Bonus</a>
					</li><li><a href="actsandrules.php">Acts & Rules</a>
					</li><li><a href="notyet.php">PPE Supply</a>
					</li><li><a href="notyet.php">Near miss Reporting</a>
					</li><li><a href="notyet.php">Accident Reporting</a>
					</li><li><a href="legaldataview.php">Compliance Data Base</a>
					</li><li><a href="https://docs.zoho.com/sheet/ropen.do?rid=15oj07480de4bf1f141a9b670586d3a6c4f2a">Compliance Check List</a>
					</li><li><a  href="notyet.php">Analysis</a>
					</li><li><a href="notyet.php">Skill Development</a>	
					</li>
        </ul>
      </li>
	  <!----
      <li class="sub-menu">
        <a href="javascript:void(0);"><i class="fa fa-map-marker"></i><span>Maps</span><i class="arrow fa fa-angle-right pull-right"></i></a>
        <ul>
          <li><a href="map-google.html">Google Map</a>
          </li>
          <li><a href="map-vector.html">Vector Map</a>
          </li>
        </ul>
      </li>
	  --->
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