<?php
 include('includes/headerclms.inc');
echo "<script>
function loadmenu(){
	document.getElementById('topmenu').style.display='block';}
window.onload = function(){
   setInterval(loadmenu, 1000);
};
</script>";	
 echo'<br> <br> <br> <br> <br>';
echo "<center><img style='margin-left:50px; box-shadow:0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.76);height:250px;width:250px' src ='images/undercons.png'></img>";
?>
