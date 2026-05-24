<?php



$PageSecurity = 10;  

include('includes/sess.inc');

$title = _('Contract Labour Engagement Recording at Gate');

if(substr($_SESSION['UserID'],0,4)<>'clms' ){

  exit("Not allowed. Unauthorised User");

}



include('includes/headerclmsnew.inc');
?>
