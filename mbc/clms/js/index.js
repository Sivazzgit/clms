$("#leftside-navigation .sub-menu > a").click(function(e) {
  $("#leftside-navigation ul ul").slideUp(), $(this).next().is(":visible") || $(this).next().slideDown(),
  e.stopPropagation()
})

function clickedmenu()
{
	if(document.getElementById('topmenu').style.display!="none")
	{
		document.getElementById('topmenu').style.display ="none";
	}
	else
	{
		document.getElementById('topmenu').style.display ="block";
	}
}