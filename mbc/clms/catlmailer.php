<?php
include '../../test/speed/PHPMailer-master/PHPMailerAutoload.php';
function catlmailer($toaddress,$toName,$toCC,$toBCC,$Sub,$Body,$html)
{
	//return;
	//SMTP needs accurate times, and the PHP time zone MUST be set
	//This should be done in your php.ini, but this is how to do it if you don't have access to that
	//date_default_timezone_set('Asia/Kolkata');

date_default_timezone_set('America/Phoenix');

	//require '../PHPMailerAutoload.php';
	//include 'http://myleaveap.in/pms/PHPMailer-master/PHPMailerAutoload.php';

ob_start();
	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->IsSMTP();

	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->Host = "localhost";
	//$mail->SMTPDebug = 2;
	//$mail->Host = 'relay-hosting.secureserver.net';
	//Set the SMTP port number - likely to be 25, 465 or 587
	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;
	$mail->SMTPSecure  = false;

	//Username to use for SMTP authentication
	$mail->Username = "kancorhr@myleaveap.in";
	//Password to use for SMTP authentication
	$mail->Password = "kancor123";

	//Set who the message is to be sent from
	$mail->From ='kancorhr@myleaveap.in';
	$mail->setFrom('kancorhr@myleaveap.in', 'KANCOR');
	//Set an alternative reply-to address
//	$mail->addReplyTo('hr@myleaveap.in  ', 'SPEED');
	
	// Add each email address
  foreach($toaddress as $email){ $mail->AddAddress(trim($email)); }
 // if($toCC!=''){ foreach($toCC as $email){ $mail->addCC(trim($email)); } }
 // if($toBCC!=''){ foreach($toBCC as $email){ $mail->addBCC(trim($email)); } }
	/*$mail->addAddress($toaddress, $toName);
	//Set the subject line
	//$mail->Subject = 'Job Role Approved'; 
	$mail->addCC($toCC, "");
	$mail->addBCC($toBcc, "");*/
	$mail->Subject = $Sub;
	$mail->isHTML(true); 
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	//$mail->msgHTML(file_get_contents('mynewpwd.php'), dirname(__FILE__));
	if($html=='')
	{
	if(trim($toName)!='')
	$mybody = "<p>Dear ".$toName.",</p><p>";
	else
	$mybody = "<p>Hi,</p><p>";
	$mybody =$mybody.$Body;
	
	$mybody = $mybody."</p><a href ='speed.myleaveap.in  ' ></a>
				<br>
				<p>Regards,</p>
				<p>HR Team</p>	 
				<p>------------------------------------------------------------------------------------------------------------------------</p>
				<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; THIS IS AN AUTO GENERATED MESSAGE. PLEASE DO NOT REPLY.</p>
				<p>------------------------------------------------------------------------------------------------------------------------</p>";
	}
	else
	{
		$mybody = $Body;
	}
	$mail->Body = $mybody;
	//Replace the plain text body with one created manually
	//$mail->AltBody = 'The KRA of '.$empname.' has been sent for Review. Please login myleaveap.in/rcs/mn3.php';
	//Attach an image file
	//$mail->addAttachment('images/phpmailer_mini.png');
	//echo "my email " . $_GET['UserNameEntryField'];
	//send the message, check for errors

	if (!$mail->Send()) 
	{
	//    echo "Mailer Error: " . $mail->ErrorInfo;
	   if($toaddress=='')
	   {
			echo "<Center><h3 style='color:Red'>Email address is not valid for ".$toName."</h3";
	   }
	   else
	   {
			echo "<Center><h3 style='color:Green'> Mail to ".$toaddress[0]." is being sent </h3";
	   }
		//echo $empemail."</h3>";
	} else
	{
		 echo "<Center><h3 style='color:1Green'>Email to ".$toaddress[0]." sent </h3";
	}

}

function catlmailerwithId($toId,$ccId,$bccId,$Sub,$Body,$html) 
{
	//return;
	//SMTP needs accurate times, and the PHP time zone MUST be set
	//This should be done in your php.ini, but this is how to do it if you don't have access to that
	date_default_timezone_set('Asia/Kolkata');
//return 0;  
#date_default_timezone_set('America/Phoenix');

	//require '../PHPMailerAutoload.php';
	//include 'http://myleaveap.in/pms/PHPMailer-master/PHPMailerAutoload.php';

$db = $GLOBALS['db'];



	$sql = "SELECT 
			lastname,email1 			
		FROM clmsemployeemaster
		where employeeid =".$toId."";
	$ErrMsg = _('The employee master could not be retrieved because');
	$result = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_row($result);
	//$toaddress =rtrim($myrow[1]);

	$email = [];
	array_push($email,$myrow[1]);
	$toName =rtrim($myrow[0]);
	if($ccId!="")
	{
		//echo $toName." dsadsadsads"; 

		$sql = "SELECT 
				lastname,email1 			
			FROM clmsemployeemaster
			where employeeid =".$ccId."";
		$ErrMsg = _('The employee master could not be retrieved because');
		$result = DB_query($sql,$db,$ErrMsg);
		$myrow = DB_fetch_row($result);
		$toCC =rtrim($myrow[1]);


		array_push($email,$myrow[1]);
	}


	if($bccId!="")
	{

		$sql = "SELECT 
				lastname,email1 			
			FROM clmsemployeemaster
			where employeeid =".$bccId."";
		$ErrMsg = _('The employee master could not be retrieved because');
		$result = DB_query($sql,$db,$ErrMsg);
		$myrow = DB_fetch_row($result);
		$toBCC =rtrim($myrow[1]);
	}
	//$toName =rtrim($myrow[1]);
			
	//array_push($email,"siv.appu@gmail.com");
	//array_push($email,"ramu1956@gmail.com");
	//array_push($email,"siv.appu@gmail.com");
	//$toCC = "ramu1956@gmail.com";
	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->IsSMTP();
	echo $email[0];


	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	//$mail->Host = "mail.myleaveap.in";

	$mail->Host = 'mail.myleaveap.in  ';
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = 587; 
	//Whether to use SMTP authentication
	$mail->SMTPAuth = true; 
	$mail->SMTPSecure  = false;

	//Username to use for SMTP authentication
	$mail->Username = "hr@myleaveap.in  ";
	//Password to use for SMTP authentication
	$mail->Password = "catl123";

	//Set who the message is to be sent from
	$mail->setFrom('hr@myleaveap.in  ', 'SPEED');
	//$mail->From ='hr@myleaveap.in  ';
	//Set an alternative reply-to address
	$mail->addReplyTo('hr@myleaveap.in  ', 'SPEED');
	
	// Add each email address
  foreach($email as $toaddress){ $mail->AddAddress(trim($toaddress)); }
 // if($toCC!=''){ foreach($toCC as $email){ $mail->addCC(trim($email)); } }
 // if($toBCC!=''){ foreach($toBCC as $email){ $mail->addBCC(trim($email)); } }
	/*$mail->addAddress($toaddress, $toName);
	//Set the subject line
	//$mail->Subject = 'Job Role Approved'; 
	$mail->addCC($toCC, "");
	$mail->addBCC($toBcc, "");*/

	$mail->Subject = $Sub; 
	$mail->isHTML(true); 
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	//$mail->msgHTML(file_get_contents('mynewpwd.php'), dirname(__FILE__));
	if($html=='')
	{
	if(trim($toName)!='')
	$mybody = "<p>Dear ".$toName.",</p><p>";
	else
	$mybody = "<p>Hi,</p><p>";
	$mybody =$mybody.$Body;
	
	$mybody = $mybody."</p><a href ='speed.myleaveap.in  ' ></a>
				<br>
				<p>Regards,</p>
				<p>HR Team</p>	 
				<p>------------------------------------------------------------------------------------------------------------------------</p>
				<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; THIS IS AN AUTO GENERATED MESSAGE. PLEASE DO NOT REPLY.</p>
				<p>------------------------------------------------------------------------------------------------------------------------</p>";
	}
	else
	{
		$mybody = $Body;
	}
	$mail->Body = $mybody;
	//Replace the plain text body with one created manually
	//$mail->AltBody = 'The KRA of '.$empname.' has been sent for Review. Please login myleaveap.in/rcs/mn3.php';
	//Attach an image file
	//$mail->addAttachment('images/phpmailer_mini.png');
	//echo "my email " . $_GET['UserNameEntryField'];
	//send the message, check for errors

	if (!$mail->Send()) 
	{
	//    echo "Mailer Error: " . $mail->ErrorInfo;
	   if($toaddress=='')
	   {
			echo "<Center><h3 style='color:Red'>Email address is not valid for ".$toName."</h3";
	   }
	   else
	   {
			echo "<Center><h3 style='color:Green'> Mail to ".$toaddress." is being sent </h3";
	   }
		//echo $empemail."</h3>";
	} else
	{
		 echo "<Center><h3 style='color:1Green'>Email to ".$toaddress." sent </h3";
	}

}


function catlmailerwithAttach($toaddress,$toName,$toCC,$toBCC,$Sub,$Body,$html,$attachment)
{
	//return;
	//SMTP needs accurate times, and the PHP time zone MUST be set
	//This should be done in your php.ini, but this is how to do it if you don't have access to that
	date_default_timezone_set('Asia/Kolkata');

//date_default_timezone_set('America/Phoenix');

	//require '../PHPMailerAutoload.php';
	//include 'http://myleaveap.in/pms/PHPMailer-master/PHPMailerAutoload.php';

ob_start();
	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->IsSMTP();

	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->Host = "localhost";
	//$mail->SMTPDebug = 2;
	//$mail->Host = 'relay-hosting.secureserver.net';
	//Set the SMTP port number - likely to be 25, 465 or 587
	//Whether to use SMTP authentication
	$mail->SMTPAuth = false;
	$mail->SMTPSecure  = false;

	//Username to use for SMTP authentication
	//$mail->Username = "hr@myleaveap.in  ";
	//Password to use for SMTP authentication
	//$mail->Password = "catl123";

	//Set who the message is to be sent from
	$mail->From ='hr@myleaveap.in  ';
	$mail->setFrom('hr@myleaveap.in  ', 'SPEED');
	//Set an alternative reply-to address
//	$mail->addReplyTo('hr@myleaveap.in  ', 'SPEED');
	
	// Add each email address
  foreach($toaddress as $email){ $mail->AddAddress(trim($email)); }
 // if($toCC!=''){ foreach($toCC as $email){ $mail->addCC(trim($email)); } }
 // if($toBCC!=''){ foreach($toBCC as $email){ $mail->addBCC(trim($email)); } }
	/*$mail->addAddress($toaddress, $toName);
	//Set the subject line
	//$mail->Subject = 'Job Role Approved'; 
	$mail->addCC($toCC, "");
	$mail->addBCC($toBcc, "");*/
	$mail->Subject = $Sub;
	$mail->isHTML(true); 
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	//$mail->msgHTML(file_get_contents('mynewpwd.php'), dirname(__FILE__));
	if($html=='')
	{
	if(trim($toName)!='')
	$mybody = "<p>Dear ".$toName.",</p><p>";
	else
	$mybody = "<p>Hi,</p><p>";
	$mybody =$mybody.$Body;
	
	$mybody = $mybody."</p><a href ='111speed.myleaveap.in  ' > CLMS</a>
				<br>
				<p>Regards,</p>
				<p>HR Team</p>	 
				<p>------------------------------------------------------------------------------------------------------------------------</p>
				<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; THIS IS AUTO GENERATED MESSAGE. PLEASE DO NOT REPLY.</p>
				<p>------------------------------------------------------------------------------------------------------------------------</p>";
	}
	else
	{
		$mybody = $Body;
	}
	$mail->Body = $mybody;
	//Replace the plain text body with one created manually
	//$mail->AltBody = 'The KRA of '.$empname.' has been sent for Review. Please login myleaveap.in/rcs/mn3.php';
	//Attach an image file
		//$mail->addAttachment($attachment);
		$mail->addAttachment($attachment, $name = $attachment,  $encoding = 'base64', $type = 'application/pdf');

	//echo "my email " . $_GET['UserNameEntryField'];
	//send the message, check for errors

	if (!$mail->Send()) 
	{
	//    echo "Mailer Error: " . $mail->ErrorInfo;
	   if($toaddress=='')
	   {
			echo "<Center><h3 style='color:Red'>Email address is not valid for ".$toName."</h3";
	   }
	   else
	   {
			echo "<Center><h3 style='color:Green'> Mail to ".$toaddress[0]." is being sent </h3";
	   }
		//echo $empemail."</h3>";
	} else
	{
		 echo "<Center><h3 style='color:1Green'>Email to ".$toaddress[0]." sent </h3";
	}

}		
?>