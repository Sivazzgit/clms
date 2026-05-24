<?php

$PageSecurity = 10;

include('includes/sess.inc'); 

include('includes/header.inc');

?>

<html>

<body>



<link rel="stylesheet" type="text/css" href="./includes/css/tbstyle.css" media="all">	

<script src="jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="./includes/css/style.css" media="all">



<form action="filesupload.php" method="post" enctype="multipart/form-data">

<center><table><tr>

	<?php

	



	//$sql = "SELECT DISTINCT	pos                     

	//FROM catlemployeemaster WHERE TRIM(pos)!=''";

	//$ErrMsg = _('The employee master could not be retrieved because');

	//$result = DB_query($sql,$db,$ErrMsg);

	//while($myrow = DB_fetch_row($result)) 

	{

		

		$str = "<select name='type'>";	

		$str =$str."<option value='Challan'>PF and ESI Challan</option>";

	        $str =$str."<option value='Other'>Mustrol & Wages Register</option></select>";

		//$str= $str.$myrow[0].",";

	}

	echo $str;

	?>

	

<!--	</select></td> --!>

	 <td>Select the  File to upload:</td>

    <td><input type="file" accept="" name="fileToUpload" id="sign_in_button"></td>

	</tr><tr>

    <td colspan='4' style='text-align:center;'>

	<button id="sign_in_button" value= 'Upload file' name ="submit">

        <span class="button_text">Upload file</span></button>

	</td>

	

	</tr></table>

</form>



</body>

</html>

