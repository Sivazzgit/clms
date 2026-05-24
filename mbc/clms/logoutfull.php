<?php
/* $Revision: 1.13 $ */
$PageSecurity =1;

include('includes/sess.inc');

?>
<html>
<head>
    <title><?php echo $_SESSION['CompanyRecord']['coyname'];?> - <?php echo _('Log Off'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=us-ascii" />
    <link rel="stylesheet" href="css/<?php echo $theme;?>/login.css" type="text/css" />
</head>

<script type="text/javascript">location.href = 'clms.php';</script>
<?php
	// Cleanup
	session_start();
	session_unset();
	session_destroy();
?>
</body>
</html>


