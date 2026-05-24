<?php

/* $Revision: 1.17 $ */

// Display demo user name and password within login form if $allow_demo_mode is true

include ('includes/LanguageSetup.php');



$logtime = date('m/d/Y h:i:s a', time());

$time = 23 * 60; //30 minutes

$start_time = date('Y-m-d h:i:s', time() - $time);

$end_time = date('Y-m-d h:i:s', time() + $time);



if ($allow_demo_mode == True AND !isset($demo_text)) {

    $demo_text = _('login as user') .': <i>' . _('demo') . '</i><BR>' ._('with password') . ': <i>' . _('anahaw') . '</i>';

} elseif (!isset($demo_text)) {

    $demo_text = _('Please login here');

}

$KUserName='';

//echo"<br>Welcome My User Name " .$KUserName;

//echo"<br>Welcome My Password " .$Kpword;



?>



<HTML>

<HEAD>

    <TITLE><?php echo $_SESSION['CompanyRecord']['coyname'];?></TITLE>

    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo _('ISO-8859-1'); ?>" />

    <link rel="stylesheet" href="css/<?php echo $theme;?>/login.css" type="text/css" />

    <link rel="stylesheet" href="includes/css/style.css" type="text/css" />

</HEAD>



<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

    

    

    

    

    <div id="login">

    <FORM action="<?php echo $_SERVER['PHP_SELF'];?>" name="loginform" method="post">

    <TABLE width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" class="mainTable1">

        <TR>    

            <TD align="left" valign="top"></TD>

        </TR>



        <TR>

            <TD align="center" valign="top">



            <TABLE  class="login" style=" border:4px solid #004721;border-radius:20px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);width:70%;">

                <TR>

                    <TD colspan="1" rowspan="2"> 

                    

                    

                       

                       <img style="padding: 30px;" src="./includes/images/ramlogo.jpg" >

                                

                            </TD><TD><table><tr><td colspan="2"> 

                                         </td>

                                    <tr><td>

                                         

                                         <SPAN class="loginText" style="color:#004721;"><?php echo _('Username'); ?>:</SPAN>

                                         </td><td>

                                         <INPUT type="TEXT" name="UserNameEntryField" value ="<?php echo ltrim($KUserName); ?>"/><br />

                                         </td><td>

                                         </tr>

                                         <tr><td>

                                                <?php



                        if ($AllowCompanySelectionBox == true){

                            echo '<SELECT name="CompanyNameField">';

                            $DirHandle = dir('companies/');

                            while (false != ($CompanyEntry = $DirHandle->read())){

                                if (is_dir('companies/' . $CompanyEntry) AND $CompanyEntry != '..' AND $CompanyEntry != 'CVS' AND $CompanyEntry!='.'){

                                    echo "<OPTION  VALUE='$CompanyEntry'>$CompanyEntry";

                                }

                            }

                            echo '</SELECT>';

                        } else {

                                                //echo '<INPUT type="TEXT" name="CompanyNameField"  VALUE="' . $DefaultCompany . '">';

                                                //echo '<INPUT type="hidden" name="CompanyNameField"  VALUE="' . $DefaultCompany . '">';

                            echo '<INPUT type="hidden" name="CompanyNameField"  VALUE="' . $DefaultCompany . '">';





                        }

                    ?>

                                         <SPAN class="loginText" style="color:#004721;"><?php echo _('Password'); ?>:</SPAN><BR />

                                         </td><td>

                                         <INPUT type="PASSWORD" name="Password">

                                         <BR />

                                         </td><td>

                                         <br><br><br>

                                         </tr>

                                         <tr>



                                             <td colspan="2"><center>

                                                <br>

                                                  <button id="sign_in_button" value="><?php echo _('Login'); ?>" name="SubmitUser"> 

                                            <span class="button_text">LogIn</span></button></center>

                                             </td> 



                                         </tr> 

                                         </table>                                        

                                         <br>

                                        

                                        <br> 

                                    <INPUT type="hidden" name="logtime" value ="<?php echo ltrim($logtime); ?>"/><br />  

                                    <INPUT type="hidden" name="end_time" value ="<?php echo ltrim($end_time); ?>"/><br />  

     </td>

      

                        </TR>                     

                        

                             

                        </form>

                    </table>



                    </TD>

                </TR>



            </table>



            </TD>

        </TR>

    </table> 

    </div>

    <script language="JavaScript" type="text/javascript">

    //<![CDATA[

            <!--

            document.forms[0].CompanyField.select();

            document.forms[0].CompanyField.focus();

            //-->

    //]]>

    </script>

</body>

</html>

