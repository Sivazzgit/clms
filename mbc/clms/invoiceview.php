<?php
$PageSecurity = 10;  


//include('includes/sess.inc');

$title = _('Contract Labour Engagement Approval Status');
if(substr($_SESSION['UserID'],0,4)<>'clms' ){
	exit("Not allowed. Unauthorised User");
}

//include('includes/headerclms.inc'); 

		echo '<TD CLASS="quick_menu_tabs" ALIGN="center"><h1><A ACCESSKEY="2" HREF="' .  $rootpath . '/Invoice Care_Aug 21.pdf' . SID . '"><U></U> ' . _('Invoice View') . '</A>&nbsp;&nbsp;</h1></TD>';


?>