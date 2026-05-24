<?php $PageSecurity = 1;



include('includes/sess.inc');

//include('includes/headerclms.inc');

if(substr($_SESSION['UserID'],0,4)<>'clms' ){

	exit("Not allowed. Unauthorised User");

}

//echo "UUUUUUUU".substr($_SESSION['UserID'],0,5);

//exit("Break-1");

$muserid = rtrim($_SESSION['UserID']);





	if(($muserid == "clmsadmin")

		|| ($muserid  == "admin") 

		|| ($muserid  == "CIEL")) 

		{

//			echo '<script type="text/javascript">location.href = "adminlogin.php";</script>';

//			exit();

		include('includes/headerclms.inc');

		}

		if(substr($_SESSION['UserID'],0,5)=='clms0' ){

			echo'<meta http-equiv="refresh" content="0; url=contmenu.php" />';

		}	

		if(substr($_SESSION['UserID'],0,5)=='clms1' ){

			echo'<meta http-equiv="refresh" content="0; url=manpowerentryapprovalstatus.php" />';

		}	

		

date_default_timezone_set('	Asia/Kolkata');

?>





<script>

function loadmenu(){

	document.getElementById('topmenu').style.display='block';}

window.onload = function(){

   setInterval(loadmenu, 1000);

};

</script>	

<div id="mainContent" style="margin-left:250px;height:80%;width:80%;overflow-y:auto;overflow-x:auto;">

<center><div class="inner_page_cont" style="border:1px solid white;width:50%;border-radius:20px;">

      			<div class="contentarea">

                	 <div class="contentmain">

                         <div class="left"><p style="color:white;text-align: justify;">Human Resources is an essential department in helping the growth of any company. HR should be the epicenter of the company where culture is cultivated and nourished. Happy employees will directly translate into good work being done.

						 An efficient <b>HRMS </b>can set the tone of the relationship between the company and its employees.

Building on skills, hiring the right people, building the internal structure and synergy of the company will help it grow and succeed. .<br>  <br>  You can reach us by any of the following facilities:</p>  <p>&nbsp;</p>  <p class="style2_link"><a href="mailto:support@ramconsultancy.in "><span style="color: rgb(255, 255, 0);">support@ramconsultancy.in</span></a><span style="color: rgb(255, 255, 0);"></span></p>  <p class="style2_link">&nbsp;</p>  </div> 

                            <div class="right">

                               <br><br>                 

                     </div>

                 </div></center>

            <div class="inner_page_cont-bottom"></div>

         </div>

</div>

</body>

</html>

