<?php

$PageSecurity = 10;

include('includes/sess.inc');

$title = _('Contract Labour Intend : Add New Record');



include('includes/headerclms.inc'); 

 echo'   <script src="script.js"></script>';

 	if (isset($_POST['submit'])) {

		

            $sql0 = 'SELECT count(*) from clmsiengaged where trim(vendor)="'.$_POST['vendor'].'" and trim(stdate)="'.$_POST['datepicker'].'"';

            $sql0 = 'SELECT count(*) from clmsengaged where trim(vendor) like "'.$_POST['mvendor'].'%" and trim(section) like "'.$_POST['msection'].'%"and trim(stdate)="'.$_POST['mstdate'].'"';

 			$result0 = DB_query($sql0, $db);

			$myrow0 = DB_fetch_array($result0);

//echo $sql0;

		if($myrow0[0]==0){

			if($_POST["ashift"]==''){

				$_POST["ashift"]='0';

			}

			if($_POST["bshift"]==''){

				$_POST["bshift"]='0';

			}

			if($_POST["cshift"]==''){

				$_POST["cshift"]='0';

			}

		echo "<h3>New Record Updated</h3>";

	//	echo '<h3><br><br><center><a href="engaged.php">' . _('Back to Engagement Page') . '</a><BR></h3>';

        $mmdt=date("d/m/Y :h:i:sa"); 

		$mcreatedby=$_SESSION['UserID'].' at '.$mmdt;
		$_POST["vendor"]=$_SESSION['UserID'];
	

            $sql00 = 'insert into clmsengaged(vendor,section,stdate,endate,ashift,bshift,cshift,urgent,createdby) values("'.$_POST['vendor'].'","'.$_POST['section'].'"

			,"'.$_POST['datepicker'].'",

			"'.$_POST['datepicker1'].'",'.$_POST["ashift"].','.$_POST["bshift"].','.$_POST["cshift"].',"'.$_POST['urgent'].'","'.$mcreatedby.'")';

//echo $sql00;		  

			$result00 = DB_query($sql00, $db);

		}else{

		$mmdt=date("d/m/Y :h:i:sa"); 

		$mchangedby=$_SESSION['UserID'].' at '.$mmdt;

		if(substr($_SESSION['UserID'],0,5)=='clms1' ){

	   

           $sql00 = 'update clmsengaged set endate="'.$_POST['datepicker1'].'" 

		                                    ,ashift='.$_POST["ashift"].'

										    ,bshift='.$_POST["bshift"].'

											,cshift='.$_POST["cshift"].'

											,urgent="'.trim($_POST["urgent"]).'" 

											,vendor="'.trim($_POST["vendor"]).'" 

											,changedby="'.$mchangedby.'" 

											where trim(vendor) like "'.$_POST['mvendor'].'%" and trim(section) like "'.$_POST['msection'].'%" and trim(stdate)="'.$_POST['mstdate'].'"' ;



		}else{

           $sql00 = 'update clmsengaged set endate="'.$_POST['datepicker1'].'" 

		                                    ,ashift='.$_POST["ashift"].'

										    ,bshift='.$_POST["bshift"].'

											,cshift='.$_POST["cshift"].'

											,urgent="'.trim($_POST["urgent"]).'" 

											,vendor="'.trim($_POST["vendor"]).'" 

											,vendorselectedby="'.$mchangedby.'" 

											where trim(vendor) like "'.$_POST['mvendor'].'%" and trim(section) like "'.$_POST['msection'].'%" and trim(stdate)="'.$_POST['mstdate'].'"' ;



		}

		//echo $sql00;		  

			$result00 = DB_query($sql00, $db);

		echo "<h3>Details Updated</h3>";

 		

		

	   }

    }

		echo '<h3><br><br><center><a href="engaged.php">' . _('Back to engaged Page') . '</a><BR></h3>';

	

?>

