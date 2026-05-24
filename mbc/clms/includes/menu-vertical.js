var mcVM_options={menuId:"menu-v",alignWithMainMenu:false};
init_v_menu(mcVM_options);function init_v_menu(a){if(window.addEventListener)window.addEventListener("load",function(){start_v_menu(a)},false);else window.attachEvent&&window.attachEvent("onload",function(){start_v_menu(a)})}function start_v_menu(i){var e=document.getElementById(i.menuId),j=e.offsetHeight,b=e.getElementsByTagName("ul"),g=/msie|MSIE 6/.test(navigator.userAgent);if(g)for(var h=e.getElementsByTagName("li"),a=0,l=h.length;a<l;a++){h[a].onmouseover=function(){this.className="onhover"};h[a].onmouseout=function(){this.className=""}}for(var k=function(a,b){if(a.id==i.menuId)return b;else{b+=a.offsetTop;return k(a.parentNode.parentNode,b)}},a=0;a<b.length;a++){var c=b[a].parentNode;c.getElementsByTagName("a")[0].className+=" arrow";b[a].style.left=c.offsetWidth+"px";b[a].style.float="none"; b[a].style.zIndex="999999999";b[a].style.position="absolute" ;b[a].style.top=c.offsetTop+"px";if(i.alignWithMainMenu){var d=k(c.parentNode,0);if(b[a].offsetTop+b[a].offsetHeight+d>j){var f;if(b[a].offsetHeight>j)f=-d;else f=j-b[a].offsetHeight-d;b[a].style.top=f+"px"}}c.onmouseover=function(){if(g)this.className="onhover";var a=this.getElementsByTagName("ul")[0];if(a){a.style.zIndex="9999999";a.style.visibility="visible";a.style.display="block"}};c.onmouseout=function(){if(g)this.className="";this.getElementsByTagName("ul")[0].style.display="none"}}for(var a=b.length-1;a>-1;a--)b[a].style.display="none"}
var prervurl = null;
var newurl = null;
function reviver2(nm, val) {
    if ( nm === 'edition' ) {
        return undefined; // to omit from results
    } else if ( nm === 'pub_date' ) {
        return new Date(val); // restore date object
    } else if ( typeof val === 'string' ) {
        // restore ' (undo JSON_HEX_APOS)
        val = val.replace(/\u0027/g, "'");
        return val.replace(/\s+/g, ' '); // remove extra spaces
    } else {
        return alert(val); // return unchanged
    }
}
var testData;
function loaddata(a)
{

	// return true or false, depending on whether you want to allow the `href` property to follow through or not

		if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				else if(a == "orgnichart.php")
				{
					document.getElementById("mainContent").innerHTML = "";
					var jsonobj = xmlhttp.response;
					jsonobj = jsonobj.replace(/( \r\n|\n|\r)/gm,"");
					jsonobj	= JSON.stringify(jsonobj);
				//var arr = new Array();
				//arr= JSON.parse(jsonobj);
					var jsonobj1= JSON.parse(jsonobj,reviver2); 
					//jsonobj=  jQuery.parseJSON(jsonobj.details); 
					onAddGuardian(jsonobj1);
					document.getElementById("mainContent").style.overflow="hidden";
				}
				else if(a.includes("DwmMstr.php?SelectedStatusID="))
				{
				
					document.getElementById("mainContent").innerHTML = xmlhttp.responseText;
					document.getElementById("mainContent").style.overflow="auto";
					changedwmdate(null,null);
				}
				else if(a.includes( "org.php"))
				{
					document.getElementById("mainContent").innerHTML ="<link href='https://www.jqueryscript.net/css/jquerysctipttop.css' rel='stylesheet' type='text/css'><link href='jquery.orgchart.css' media='all' rel='stylesheet' type='text/css' />"
+ "<style type='text/css'>#orgChart{width: auto;height: auto;}"+
"#orgChartContainer{width: 100%;height: 500px;overflow: auto;background: #FFFFFF;}</style>"+
					
					"<div id='orgChartContainer'><div id='orgChart'></div></div><div id='consoleOutput'></div>";
					   /* var testData = [
						{id: 1, name:  'My Organization', parent: 0},
						{id: 2, name: 'CEO Office', parent: 1},
						{id: 3, name: 'Division 1', parent: 1},						
						{id: 5, name: 'Sub Division', parent: 3},
						{id: 4, name: 'Division 2', parent: 1}
						];*/
						var jsonobj = xmlhttp.response;
					jsonobj = jsonobj.replace(/( \r\n|\n|\r)/gm,"");	

					jsonobj = jsonobj.substring(1,jsonobj.length)						
					var testData= JSON.parse(jsonobj); 
					setTimeout(function(){$.fn.showorg(testData.testData);}, 1000);
					//onAddGuardian(jsonobj1);
						
				}
				else if( a.includes("KraEval1.php")||a.includes("KraRev1.php"))
				{
					document.getElementById("kraevaledit").innerHTML = xmlhttp.responseText;
					document.getElementById("kraevaledit").style.display = "block";
				}
				else if( a.includes("KraRev0.php"))
				{
					document.getElementById("myteamevent").innerHTML = xmlhttp.responseText;
					document.getElementById("myteamevent").style.display = "block";
				}
				else if( a.includes("KraR2Rev0.php"))
				{
					document.getElementById("rev2event").innerHTML = xmlhttp.responseText;
					document.getElementById("rev2event").style.display = "block";
				}
				else if( a.includes("KraAss0.php"))
				{
					document.getElementById("shodevent").innerHTML = xmlhttp.responseText;
					document.getElementById("shodevent").style.display = "block";
				}
				else
				{
					document.getElementById("mainContent").innerHTML = xmlhttp.responseText;
					document.getElementById("mainContent").style.overflow="auto";
					if(a.includes("mpcpupdate")&& a.includes("recordid"))
					{
						if(selectedid==null)
							selectedid="cb0";
						document.getElementById("edit").click();
					}
					mytbonload();
				}
				var ctx = document.getElementById("myChart")
				if(ctx != null)
				{
					getchart(a); 
				}
			}
		};	
			
		if(a.includes("KraRev0.php")||
		a.includes("KraR2Rev0.php")||
		a.includes("KraAss0.php"))
		{
			id ="myteamevent";
			if(a.includes("KraR2Rev0.php"))
				id = "rev2event";
			if(a.includes("KraAss0.php"))
			id = "shodevent";
			if(document.getElementById(id).style.display == "block")
			{
				document.getElementById(id).style.display = "none";
				return;
			}
			else
			{
					document.getElementById(id).style.display = "block";
			}
		}
		xmlhttp.open("GET",a,true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		if((a.includes("KraEval1.php")==false)&&
		(a.includes("KraRev0.php")==false)&&
		(a.includes("KraRev1.php")==false)&&
		a.includes("KraR2Rev0.php")==false&&
		a.includes("KraAss0.php")==false&&
		a.includes("KraAss1.php")==false&&
		a.includes("KraR2Rev1.php")==false)
		{
			document.getElementById("mainContent").innerHTML = "<center><img style='position:fixed; top:50%;width:50px;height:50px;'src='./includes/images/loading.gif'></center>";
			if(newurl != null)
			{
				prervurl = newurl;
			}		
			newurl=a;
		}
        xmlhttp.send();	
	
		
		
		return false;
};


function getURLencodedForm(form)
{
  var urlEncode = function(data, rfc3986)
  {
    if (typeof rfc3986 === 'undefined') {
      rfc3986 = true;
    }

    // Encode value
    data = encodeURIComponent(data);
    data = data.replace(/%20/g, '+');

    // RFC 3986 compatibility
    if (rfc3986)
    {
      data = data.replace(/[!'()*]/g, function(c) {
        return '%' + c.charCodeAt(0).toString(16);
      });
    }

    return data;
  };

  if (typeof form === 'string') {
    form = document.getElementById(form);
  }

  var url = [];
  for (var i=0; i < form.elements.length; ++i)
  {
  	if(form.elements[i].type =="radio")
  	{
  		if(form.elements[i].checked ==false)
  			continue;
  	}
  	if(form.elements[i].type =="checkbox")
  	{
  		if(form.elements[i].checked ==false)
  			continue;
  	}
    if (form.elements[i].name != '')
    {
      url.push(urlEncode(form.elements[i].name) + '=' + urlEncode(form.elements[i].value));
    }
  }

  return url.join('&');
}
function submitdata(a,url)
{
		
	//	alert(value);
	// return true or false, depending on whether you want to allow the `href` property to follow through or not
		if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				if(url.includes("KraEval1.php")==true)
				{
					//document.getElementById("kraevaledit").style.display = "none";
					if(xmlhttp.responseText.includes("SUCCESS"))
					{
						var value = "<p style='color:Green';>Record has been updated successfully</p>";
						document.getElementById("kraevaledit").innerHTML = value;
						setTimeout(loaddata(newurl),2000);
					}
				}
				else if(url.includes("KraRev1.php")==true ||url.includes("KraR2Rev1.php")==true )
				{
					//document.getElementById("kraevaledit").style.display = "none";
					if(xmlhttp.responseText.includes("SUCCESS"))
					{
						var value = "<p style='color:Green';>Record has been updated successfully</p>";
						document.getElementById("kraevaledit").innerHTML = value;
						setTimeout(loaddata(newurl),2000);
					}
				}
				else if(url.includes("KraAss1.php")==true ||url.includes("KraAss0.php")==true )
				{
					//document.getElementById("kraevaledit").style.display = "none";
					if(xmlhttp.responseText.includes("SUCCESS"))
					{
						//var value = "<p style='color:Green';>Record has been updated successfully</p>";
						//document.getElementById("kraevaledit").innerHTML = value;
						setTimeout(loaddata(newurl),2000);
					}
				}
				else
				{
					document.getElementById("mainContent").innerHTML = xmlhttp.responseText;
					if(newurl.includes("mpcpupdate.php") == true &&
						newurl.includes("recordid=") == true)
						{
							
						}
				else	if((newurl.includes("DwmMstr.php")==false) &&
						newurl.includes("mpcpset.php") == false)
					setTimeout(loaddata(newurl),2000);
				}
				//alert( xmlhttp.responseText);
			}
		};	
		
		
		//var form = document.querySelector("#"+a);
		//var data = new FormData(form);
		var value = getURLencodedForm(a);
		xmlhttp.open("POST",url,true);
		
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send(value);	
		if((url.includes("KraEval1.php")==false)&&
		(url.includes("KraRev1.php")==false)
		)
		document.getElementById("mainContent").innerHTML = "<center><img style='position:fixed; top:50%;width:50px;height:50px;' src='./includes/images/loading.gif'></center>";
		return false;
		
};

function clicknewEntry()
{
	
	if(document.getElementById("adddwm").style.display == "none")
	{
		document.getElementById("adddwm").style.display = "block";
		document.getElementById("dwmtb").style.display = "none";
		document.getElementById("addnewdwm").src = "./includes/images/hide.png";
		if(document.getElementById("dwmachieve"))
		document.getElementById("dwmachieve").style.display = "none";
	}
	else
	{
		document.getElementById("adddwm").style.display = "none";
		document.getElementById("dwmtb").style.display = "block";
		document.getElementById("addnewdwm").src = "./includes/images/add.png";
		if(document.getElementById("dwmachieve"))
		document.getElementById("dwmachieve").style.display = "table";
	}
};

function resetMpcpForm()
{
	
}

function onclickNewKra(id,url)
{
		var table = document.getElementById('kratb');
		var rows = table.rows.length;
		var i = rows-2;
		var lastNo= 1;
		if(i>=6 && document.getElementById("btntxt").innerText !="Update")
		{
			alert("Maximum 6 KRAs can be set fot an individual");
			return false;
		}
		
		if(document.getElementById("kratitle").value == "")
		{
			document.getElementById("kratitle").focus();
			alert("Fields should not be empty");
			return false;
		}
		if(document.getElementById("Targetq1").value == "")
		{
			document.getElementById("Targetq1").focus();
			alert("Fields should not be empty");
			return false;
		}
		
		if(document.getElementById("Targetq2").value == "")
		{
			document.getElementById("Targetq2").focus();
			alert("Fields should not be empty");
			return false;
		}
		
		if(document.getElementById("Targetq3").value == "")
		{
			document.getElementById("Targetq3").focus();
			alert("Fields should not be empty");
			return false;
		}
		if(document.getElementById("Targetq4").value == "")
		{
			document.getElementById("Targetq4").focus();
			alert("Fields should not be empty");
			return false;
		}
		
		if(document.getElementById("Weightageq1").value == "")
		{
			document.getElementById("Weightageq1").focus();
			alert("Fields should not be empty");
			return false;
		}
		if(document.getElementById("Weightageq2").value == "")
		{
			document.getElementById("Weightageq2").focus();
			alert("Fields should not be empty");
			return false;
		}
		if(document.getElementById("Weightageq3").value == "")
		{
			document.getElementById("Weightageq3").focus();
			alert("Fields should not be empty");
			return false;
		}
		if(document.getElementById("Weightageq4").value == "")
		{
			document.getElementById("Weightageq4").focus();
			alert("Fields should not be empty");
			return false;
		}
		
	return submitdata(id,url);
}
function onchnageweightage()
{
	if(document.getElementById("addkra").style.display == "none")
	{
		document.getElementById("addkra").style.display = "block";
		document.getElementById("addkrabut").src = "./includes/images/hide.png";
		
	}
	else
	{
		document.getElementById("addkra").style.display = "none";
		document.getElementById("addkrabut").src = "./includes/images/add.png"
	}
	document.getElementById("new").value =  'Yes';
}

function removeOptions(id)
{
	var length = id.options.length;
	for (i = 0; i < length; i++) {
		id.remove(id.options[0]);
	}
}

function getKraWeightage(weightage1,weightage2,weightage3,weightage4)
{
	
	// return true or false, depending on whether you want to allow the `href` property to follow through or not
		if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				var res =  xmlhttp.responseText.split(",");
				
				removeOptions(document.getElementById("Weightageq1"));
				
				removeOptions(document.getElementById("Weightageq2"));
				
				removeOptions(document.getElementById("Weightageq3"));
				
				removeOptions(document.getElementById("Weightageq4"));
					
				{
					var val1= res[0].split("\r\n");
						
					if(weightage1 != null)
					{
						var  Maxweight = 100-parseInt(val1[1]);						
						Maxweight = Maxweight-parseInt(weightage1);	
						Maxweight = 100 - Maxweight;
						if(Maxweight>60)
							Maxweight =60;
					}
					else
					{
						var  Maxweight = parseInt(val1[1]);
					}
					var ret = setweightage(document.getElementById("Weightageq1"),Maxweight,parseInt(weightage1));
					if(ret == false)
					{
						alert("Weightage for Q1 has already reached 100%!!");
					}
					if(weightage2 != null)
					{
						Maxweight = 100-parseInt(res[1]);						
						Maxweight = Maxweight-parseInt(weightage2);		
						Maxweight = 100 - Maxweight;	
						if(Maxweight>60)
							Maxweight =60;
					}
					else
					{
						  Maxweight = parseInt(res[1]);
					}
				
					ret = setweightage(document.getElementById("Weightageq2"),Maxweight,parseInt(weightage2));
					if(ret == false)
					{
						alert("Weightage for Q2 has already reached 100%!!");
					}
					if(weightage3 != null)
					{
						Maxweight = 100-parseInt(res[2]);						
						Maxweight = Maxweight-parseInt(weightage3);		
						Maxweight = 100 - Maxweight;
						if(Maxweight>60)
							Maxweight =60;
					}
					else
					{
						  Maxweight = parseInt(res[2]);
					}
					ret = setweightage(document.getElementById("Weightageq3"),Maxweight,parseInt(weightage3));
					if(ret == false)
					{
						alert("Weightage for Q3 has already reached 100%!!");
					}
					if(weightage4 != null)
					{
						Maxweight = 100-parseInt(res[3]);						
						Maxweight = Maxweight-parseInt(weightage4);	
						Maxweight = 100 - Maxweight;	
						if(Maxweight>60)
							Maxweight =60;						
					}
					else
					{
						  Maxweight = parseInt(res[3]);
					}
					
					ret = setweightage(document.getElementById("Weightageq4"),Maxweight,parseInt(weightage4));				
					if(ret == false)
					{
						alert("Weightage for Q4 has already reached 100%!!");
					}
					//document.getElementById("Weightageq1").options.selectedIndex =-1;							
					//document.getElementById("Weightageq2").options.selectedIndex =-1;
					//document.getElementById("Weightageq3").options.selectedIndex =-1;
					//document.getElementById("Weightageq4").options.selectedIndex =-1;
					val = 0;
				}
				//alert( xmlhttp.responseText);
			}
		};	
		
		
		//var form = document.querySelector("#"+a);
		//var data = new FormData(form);
		var value = "KraWeightage.php";
		xmlhttp.open("GET",value,true);		
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");		
        xmlhttp.send();	
		return false;
}
function changewt(id)
{
	var opt =id.options;
	if(opt == null || opt.length == 0)
	{
	//	alert("Weightage for the quarter has already reached 100%");
	//	id.focusout();
	}
}
function clicknewKraEntry()
{
	if(document.getElementById("addkra").style.display == "none")
	{
		document.getElementById("addkra").style.display = "block";
		document.getElementById("addkrabut").src = "./includes/images/hide.png";
		
	}
	else
	{
		document.getElementById("addkra").style.display = "none";
		document.getElementById("addkrabut").src = "./includes/images/add.png"
		return;
	}
	document.getElementById("new").value =  'Yes';
	document.getElementById("btntxt").innerText ="Add KRA";	
	
	getKraWeightage(null,null,null,null);
	
}
function clicknewMpcpEntry()
{
	
	if(document.getElementById("addmpcp").style.display == "none")
	{
		document.getElementById("addmpcp").style.display = "block";
		document.getElementById("mpcp").style.display = "none";
		document.getElementById("addnewmpcp").src = "./includes/images/hide.png";
		var table = document.getElementById('mpcptb');
		var rows = table.rows.length;
		var i = rows-1;
		var lastNo= 1;
		for(;i>=0;i++)
		{
			if(table.rows[i].cells[1].textContent!="")
			{
				var res =  table.rows[i].cells[1].textContent.split('M');
				lastNo = parseInt(res[1])+1;
				break;
			}
		}
		
		var no = document.getElementsByName("nos");
		no[0].value = "M"+lastNo;
		no[0].readOnly  = true;
	}
	else
	{
		document.getElementById("addmpcp").style.display = "none";
		document.getElementById("mpcp").style.display = "block";
		document.getElementById("addnewmpcp").src = "./includes/images/add.png"
	}
};

function clickeditEntry(id,url)
{
	if(selectedid == null)		
	{
		alert("Please select an entry to edit!!");
		return;
	}
	url+= "?SelectedStatusID="+document.getElementById(selectedid).value;	
	loaddata(url);
	
	selectedid=null;
	
}

function getvaluefromhtml(node)
{
	if(node.innerText != "")
		return node.innerText.trim();
	var n1 = node.outerHTML.indexOf("value=\"");
	var n2 = node.outerHTML.indexOf("\">");	
	var str =node.outerHTML.substring(n1+7,n2);
	if(str.includes("<td>"))
		str ="";
	return str.trim();
}

function onchangekralink(obj)
{
	if(obj.checked==false)
	alert("This action will delete the KRA Entry and data once submitted");
}
function updatequarter(id)
{
 var qrter = document.getElementById(id);
 var sum = document.getElementById("uomformula").selectedIndex;
 var mon1=null;
  var mon2=null;
   var mon3=null;
 if(id == "tq3")
 {
 	mon1 = parseFloat(document.getElementById("t10").value);
 	mon2 = parseFloat(document.getElementById("t11").value);
 	mon3 = parseFloat(document.getElementById("t12").value);
 }
 else if(id == "tq2")
 {
 	mon1 = parseFloat(document.getElementById("t7").value);
 	mon2 = parseFloat(document.getElementById("t8").value);
 	mon3 = parseFloat(document.getElementById("t9").value);
 }
 else if(id == "tq1")
 {
 	mon1 = parseFloat(document.getElementById("t4").value);
 	mon2 = parseFloat(document.getElementById("t5").value);
 	mon3 = parseFloat(document.getElementById("t6").value);
 }
 else if(id == "tq4")
 {
 	mon1 = parseFloat(document.getElementById("t1").value);
 	mon2 = parseFloat(document.getElementById("t2").value);
 	mon3 = parseFloat(document.getElementById("t3").value);
 }
 actualsum = mon1+mon2+mon3;
 if(sum >0)
 {
 	actualsum = actualsum/3;
 }
 qrter.value = actualsum;
 
}
function onchangeuomformula()
{
	updatequarter("tq1");
	updatequarter("tq2");
	updatequarter("tq3");
	updatequarter("tq4");
	
}
function calculcateachieve(period)
{
	
 document.getElementById("achievement").value = 
 parseFloat(document.getElementById("actual"+period).value)/parseFloat(document.getElementById("t"+period).value)*100;
 if(parseFloat(document.getElementById("t"+period).value)==0)
	  document.getElementById("achievement").value = 0;
  document.getElementById("achievement").value  =   parseFloat(document.getElementById("achievement").value).toFixed(2);
 }
function clickMPCPUpdateEntry(id,url,period)
{
	if(selectedid == null)		
	{
		alert("Please select an entry to edit!!");
		return;
	}
	
	
	if(document.getElementById("addmpcp").style.display == "none")
	{
		document.getElementById("addmpcp").style.display = "block";
		document.getElementById("mpcp").style.display = "none";
		if(document.getElementById("dwmachieve"))
		document.getElementById("dwmachieve").style.display = "none";
	//	document.getElementById("addnewmpcp").src = "./includes/images/hide.png";
		document.getElementById("delegate").options.selectedIndex =0;
		document.getElementById("emplist").style.display ="none";
	
	}
	else
	{
		document.getElementById("addmpcp").style.display = "none";
		document.getElementById("mpcp").style.display = "block";
		//document.getElementById("addnewmpcp").src = "./includes/images/add.png"
		if(document.getElementById("dwmachieve"))
		document.getElementById("dwmachieve").style.display = "table"; 
	}
	
	var mytd = document.getElementById(selectedid).parentNode;
	var mytr = mytd.parentNode;
	var accoptions = document.getElementById("managingitem").options;
	for(var i=0;i<accoptions.length;i++)
		if(accoptions[i].text == (getvaluefromhtml(mytr.cells[2])))
			document.getElementById("managingitem").selectedIndex = i;
	document.getElementById("recordid").value = document.getElementById(selectedid).value;
	document.getElementById("nos").readOnly = true;
	document.getElementById("nos").value = getvaluefromhtml(mytr.cells[1]);
	document.getElementById("mp").readOnly = true;
	document.getElementById("cp").readOnly = true;
	document.getElementById("uom").readOnly = true;
	document.getElementById("mp").value =  getvaluefromhtml(mytr.cells[3]);
	document.getElementById("cp").value =  getvaluefromhtml(mytr.cells[4]);
	document.getElementById("uom").value =  getvaluefromhtml(mytr.cells[5]);
	var uomfor = document.getElementById("uomformula").options;
	for(var i=0;i<uomfor.length;i++)
		if(uomfor[i].value == (getvaluefromhtml(mytr.cells[6])))
			document.getElementById("uomformula").selectedIndex = i;
			
			document.getElementById("t"+period).value =  getvaluefromhtml(mytr.cells[9]);
			document.getElementById("actual"+period).value =  getvaluefromhtml(mytr.cells[10]);
			
	var kralinkage = getvaluefromhtml(mytr.cells[11])
	if( kralinkage == "on" || kralinkage =="Yes")
			document.getElementById("kralink").checked = true;
			else
			
			document.getElementById("kralink").checked = false;
			document.getElementById("remarks").value =  getvaluefromhtml(mytr.cells[12]);
			calculcateachieve(period);
			//document.getElementById("achievement").value = parseFloat(document.getElementById("t"+period).value)/parseFloat(document.getElementById("actual"+period).value);
			//onchangeuomformula();
		
	//url+= "?SelectedStatusID="+document.getElementById(selectedid).value;	
	//loaddata(url);
	//selectedid=null;
	
}
function clickMPCPEditEntry(id,url)
{
	if(selectedid == null)		
	{
		alert("Please select an entry to edit!!");
		return;
	}
	
	
	if(document.getElementById("addmpcp").style.display == "none")
	{
		document.getElementById("addmpcp").style.display = "block";
		document.getElementById("mpcp").style.display = "none";
		if(document.getElementById("dwmachieve"))
		document.getElementById("dwmachieve").style.display = "none";
	//	document.getElementById("addnewmpcp").src = "./includes/images/hide.png";
		document.getElementById("delegate").options.selectedIndex =0;
		document.getElementById("emplist").style.display ="none";
	
	}
	else
	{
		document.getElementById("addmpcp").style.display = "none";
		document.getElementById("mpcp").style.display = "block";
		//document.getElementById("addnewmpcp").src = "./includes/images/add.png"
		if(document.getElementById("dwmachieve"))
		document.getElementById("dwmachieve").style.display = "table"; 
	}
	
	var mytd = document.getElementById(selectedid).parentNode;
	var mytr = mytd.parentNode;
	var accoptions = document.getElementById("managingitem").options;
	for(var i=0;i<accoptions.length;i++)
		if(accoptions[i].text == (getvaluefromhtml(mytr.cells[2])))
			document.getElementById("managingitem").selectedIndex = i;
	document.getElementById("recordid").value = document.getElementById(selectedid).value;
	document.getElementById("nos").readOnly = true;
	document.getElementById("nos").value = getvaluefromhtml(mytr.cells[1]);
	document.getElementById("mp").readOnly = true;
	document.getElementById("cp").readOnly = true;
	document.getElementById("uom").readOnly = true;
	document.getElementById("mp").value =  getvaluefromhtml(mytr.cells[3]);
	document.getElementById("cp").value =  getvaluefromhtml(mytr.cells[4]);
	document.getElementById("uom").value =  getvaluefromhtml(mytr.cells[5]);
	var uomfor = document.getElementById("uomformula").options;
	for(var i=0;i<uomfor.length;i++)
		if(uomfor[i].value == (getvaluefromhtml(mytr.cells[6])))
			document.getElementById("uomformula").selectedIndex = i;
	document.getElementById("tfrom").value =  getvaluefromhtml(mytr.cells[7]);
	document.getElementById("tt2").value =  getvaluefromhtml(mytr.cells[8]);
	document.getElementById("t3").value =  getvaluefromhtml(mytr.cells[9]);
	document.getElementById("t4").value =  getvaluefromhtml(mytr.cells[10]);
	document.getElementById("t5").value =  getvaluefromhtml(mytr.cells[11]);
	document.getElementById("t6").value =  getvaluefromhtml(mytr.cells[12]);
	document.getElementById("t7").value =  getvaluefromhtml(mytr.cells[13]);
	document.getElementById("t8").value =  getvaluefromhtml(mytr.cells[14]);
	document.getElementById("t9").value =  getvaluefromhtml(mytr.cells[15]);
	document.getElementById("t10").value =  getvaluefromhtml(mytr.cells[16]);
	document.getElementById("t11").value =  getvaluefromhtml(mytr.cells[17]);
	document.getElementById("t12").value =  getvaluefromhtml(mytr.cells[18]);
	document.getElementById("t1").value =  getvaluefromhtml(mytr.cells[19]);
	document.getElementById("t2").value =  getvaluefromhtml(mytr.cells[20]);
		document.getElementById("t1").value =  getvaluefromhtml(mytr.cells[19]);
	document.getElementById("t2").value =  getvaluefromhtml(mytr.cells[20]);
	var kralinkage = getvaluefromhtml(mytr.cells[25])
	if( kralinkage == "on" || kralinkage =="Yes")
			document.getElementById("kralink").checked = true;
			else
			
			document.getElementById("kralink").checked = false;
	document.getElementById("remarks").value =  getvaluefromhtml(mytr.cells[26]);
			onchangeuomformula();
		
	//url+= "?SelectedStatusID="+document.getElementById(selectedid).value;	
	//loaddata(url);
	//selectedid=null;
	
}

function setweightage(id,val,selected)
{
	var options1 = id.options;
	var i = 0;
	var flag =0;	
	//var res = val.split(".00");
	//value= res[0];
	value = parseInt(val);
	for(i=0;i<options1.length;i++)
	{
		if(value==(options1[i].value))
		{
			options1.selectedIndex = i;
			flag =1;
			break;
		}			
	}
	if(flag ==0)
	{
		if(options1)
		removeOptions(id);	
		var minimum = 10;
		var maximum = value;
		var index =0;
		for(i=minimum; i<=maximum;i+=5)
		{
			newoption = document.createElement('option');
			newoption.value = i;
			newoption.text = i+"%";
			id.add(newoption);
			if(selected && selected == i)
				options1.selectedIndex = index;
			index++;
		}
	}
	if(id.options==null || id.options.length ==0 )
	{
		return false;
	}
	return true;
}
function clickKRAEditEntry(id,url)
{
	if(selectedid == null)		
	{
		alert("Please select an entry to edit!!");
		return;
	}
	
	
	if(document.getElementById("addkra").style.display == "none")
	{
		document.getElementById("addkra").style.display = "block";
		
	}
	else
	{
		document.getElementById("addkra").style.display = "none";
	}
	
	var mytd = document.getElementById(selectedid).parentNode;
	var mytr = mytd.parentNode;
	
	var linkgoal = document.getElementById("linkgoal").options;
	for(var i=0;i<linkgoal.length;i++)
		if(linkgoal[i].text == (getvaluefromhtml(mytr.cells[2])))
			document.getElementById("linkgoal").selectedIndex = i;
	
	document.getElementById("kratitle").value =  getvaluefromhtml(mytr.cells[1]);
	
	var uom = document.getElementById("krauom").options;
	for(var i=0;i<uom.length;i++)
		if(uom[i].text == (getvaluefromhtml(mytr.cells[3])))
			document.getElementById("krauom").selectedIndex = i;
			
//	document.getElementById("empid").value = document.getElementById(selectedid).value;
//	document.getElementById("krauom").readOnly = true;
//	document.getElementById("linkgoal").readOnly = true;
	document.getElementById("Targetq1").value =  getvaluefromhtml(mytr.cells[4]);	
	document.getElementById("Targetq2").value =  getvaluefromhtml(mytr.cells[6]);
	document.getElementById("Targetq3").value =  getvaluefromhtml(mytr.cells[8]);
	document.getElementById("Targetq4").value =  getvaluefromhtml(mytr.cells[10]);
	var ret = getKraWeightage(mytr.cells[5].innerText,
							 mytr.cells[7].innerText,
							 mytr.cells[9].innerText,
							 mytr.cells[11].innerText);
	var kra = document.getElementById("kratype").options;
	for(var i=0;i<kra.length;i++)
		if(kra[i].text == (getvaluefromhtml(mytr.cells[12])))
			document.getElementById("kratype").selectedIndex = i;
//	setweightage(document.getElementById("Weightageq1"),mytr.cells[5].innerText);
	//setweightage(document.getElementById("Weightageq2"),mytr.cells[7].innerText);
	//setweightage(document.getElementById("Weightageq3"),mytr.cells[9].innerText);	
	//setweightage(document.getElementById("Weightageq4"),mytr.cells[11].innerText);
	
	document.getElementById("new").value =  'Edit';
	document.getElementById("btntxt").innerText ="Update";
	document.getElementById("recordid").value = document.getElementById(selectedid).value;
	
	//if(getvaluefromhtml(mytr.cells[12]) == "Yes")
	//		document.getElementById("kralink").checked = true;
		
	//url+= "?SelectedStatusID="+document.getElementById(selectedid).value;	
	//loaddata(url);
	//selectedid=null;
	
}

function clickdeleteEntry(id,url)
{
	if(selectedid == null)		
	{
		alert("Please select an entry to Delete!!");
		return;
	}
	var r = confirm("Are you sure to delete the entry?");
	if (r == true) 
	{
			url+= "?SelectedStatusID="+document.getElementById(selectedid).value+"&delete=1";	
	loaddata(url);
	selectedid=null;
	}
	
}
function clicksendmpcpEntry	(id,url)
{
	if(!confirm('Do you want to continue?\nYou will not be able to make any changes once submitted.'))
	{
		return;
	}
	loaddata(url);
	selectedid=null;
}
function clicksendEntry(id,url)
{
	/*if(selectedid == null)		
	{
		alert("Please select an entry to Delete!!");
		return;
	}
	url+= "?SelectedStatusID="+document.getElementById(selectedid).value;	*/
	if(!confirm('Do you want to continue?\nYou will not be able to make any changes once submitted.'))
	{
		return;
	}
	var table = document.getElementById('kratb');	
	var rows = table.rows.length;
	rows = rows-1;
	var lastNo= 1;
	var q1Wt =0;
	var q2Wt =0;
	var q3Wt =0;
	var q4Wt =0;
	if(rows <= 1)
	{
		alert("You must add atleast two KRAs for the FY");
		return;
	}
	for(i=1;i<rows;i++)
	{
		val1 =parseInt(getvaluefromhtml(table.rows[i].cells[5]));
		val2 = parseInt(getvaluefromhtml(table.rows[i].cells[7]));
		val3 = parseInt(getvaluefromhtml(table.rows[i].cells[9]));
		val4 = parseInt(getvaluefromhtml(table.rows[i].cells[11]));
		
		q1Wt = q1Wt+(val1);
		q2Wt = q2Wt+(val2);
		q3Wt = q3Wt+(val3);
		q4Wt = q4Wt+(val4);
		
		if(val1 == 0)
		{
			if(!confirm("Weightage for Q1 for KRA in Row "+i+" is 0"))
			{
				return;
			}

		}
			
		if(val2 == 0)
		{
			if(!confirm("Weightage for Q2 for KRA in Row "+i+" is 0"))
			{
				return;
			}

		}
			
		if(val3 == 0)
		{
			if(!confirm("Weightage for Q3 for KRA in Row "+i+" is 0"))
			{
				return;
			}

		}
			
		if(val4 == 0)
		{
			if(!confirm("Weightage for Q4 for KRA in Row "+i+" is 0"))
			{
				return;
			}

		}
		//q2Wt = q2Wt+parseInt(getvaluefromhtml(table.rows[i].cells[7]));
		//q3Wt = q3Wt+parseInt(getvaluefromhtml(table.rows[i].cells[9]));
		//q4Wt = q4Wt+parseInt(getvaluefromhtml(table.rows[i].cells[11]));
	}
	if(q1Wt != 100)
	{
		alert("Q1 Weightage aggregate should be 100%");
		return;
	}
	if(q2Wt != 100)
	{
		alert("Q2 Weightage aggregate should be 100%");
		return;
	}
	if(q3Wt != 100)
	{
		alert("Q3 Weightage aggregate should be 100%");
		return;
	}
	if(q4Wt != 100)
	{
		alert("Q4 Weightage aggregate should be 100%");
		return;
	}
	loaddata(url);
	selectedid=null;
	
}
 function onclickback()
 {
	 if(prervurl != null)
	 {
		loaddata(prervurl);
	 }
	else
	{
		history.go(-1);
		return true;
	}
 }
 function exporttoXls()
 {
	
	 var name = "timesheet";
	  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="https://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name) {
	   alert(table);
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    window.location.href = uri + base64(format(template, ctx))
  }

 }
 var saveAs = saveAs || (function(view) {
	"use strict";
	// IE <10 is explicitly unsupported
	if (typeof view === "undefined" || typeof navigator !== "undefined" && /MSIE [1-9]\./.test(navigator.userAgent)) {
		return;
	}
	var
		  doc = view.document
		  // only get URL when necessary in case Blob.js hasn't overridden it yet
		, get_URL = function() {
			return view.URL || view.webkitURL || view;
		}
		, save_link = doc.createElementNS("https://www.w3.org/1999/xhtml", "a")
		, can_use_save_link = "download" in save_link
		, click = function(node) {
			var event = new MouseEvent("click");
			node.dispatchEvent(event);
		}
		, is_safari = /constructor/i.test(view.HTMLElement) || view.safari
		, is_chrome_ios =/CriOS\/[\d]+/.test(navigator.userAgent)
		, throw_outside = function(ex) {
			(view.setImmediate || view.setTimeout)(function() {
				throw ex;
			}, 0);
		}
		, force_saveable_type = "application/octet-stream"
		// the Blob API is fundamentally broken as there is no "downloadfinished" event to subscribe to
		, arbitrary_revoke_timeout = 1000 * 40 // in ms
		, revoke = function(file) {
			var revoker = function() {
				if (typeof file === "string") { // file is an object URL
					get_URL().revokeObjectURL(file);
				} else { // file is a File
					file.remove();
				}
			};
			setTimeout(revoker, arbitrary_revoke_timeout);
		}
		, dispatch = function(filesaver, event_types, event) {
			event_types = [].concat(event_types);
			var i = event_types.length;
			while (i--) {
				var listener = filesaver["on" + event_types[i]];
				if (typeof listener === "function") {
					try {
						listener.call(filesaver, event || filesaver);
					} catch (ex) {
						throw_outside(ex);
					}
				}
			}
		}
		, auto_bom = function(blob) {
			// prepend BOM for UTF-8 XML and text/* types (including HTML)
			// note: your browser will automatically convert UTF-16 U+FEFF to EF BB BF
			if (/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(blob.type)) {
				return new Blob([String.fromCharCode(0xFEFF), blob], {type: blob.type});
			}
			return blob;
		}
		, FileSaver = function(blob, name, no_auto_bom) {
			if (!no_auto_bom) {
				blob = auto_bom(blob);
			}
			// First try a.download, then web filesystem, then object URLs
			var
				  filesaver = this
				, type = blob.type
				, force = type === force_saveable_type
				, object_url
				, dispatch_all = function() {
					dispatch(filesaver, "writestart progress write writeend".split(" "));
				}
				// on any filesys errors revert to saving with object URLs
				, fs_error = function() {
					if ((is_chrome_ios || (force && is_safari)) && view.FileReader) {
						// Safari doesn't allow downloading of blob urls
						var reader = new FileReader();
						reader.onloadend = function() {
							var url = is_chrome_ios ? reader.result : reader.result.replace(/^data:[^;]*;/, 'data:attachment/file;');
							var popup = view.open(url, '_blank');
							if(!popup) view.location.href = url;
							url=undefined; // release reference before dispatching
							filesaver.readyState = filesaver.DONE;
							dispatch_all();
						};
						reader.readAsDataURL(blob);
						filesaver.readyState = filesaver.INIT;
						return;
					}
					// don't create more object URLs than needed
					if (!object_url) {
						object_url = get_URL().createObjectURL(blob);
					}
					if (force) {
						view.location.href = object_url;
					} else {
						var opened = view.open(object_url, "_blank");
						if (!opened) {
							// Apple does not allow window.open, see https://developer.apple.com/library/safari/documentation/Tools/Conceptual/SafariExtensionGuide/WorkingwithWindowsandTabs/WorkingwithWindowsandTabs.html
							view.location.href = object_url;
						}
					}
					filesaver.readyState = filesaver.DONE;
					dispatch_all();
					revoke(object_url);
				}
			;
			filesaver.readyState = filesaver.INIT;

			if (can_use_save_link) {
				object_url = get_URL().createObjectURL(blob);
				setTimeout(function() {
					save_link.href = object_url;
					save_link.download = name;
					click(save_link);
					dispatch_all();
					revoke(object_url);
					filesaver.readyState = filesaver.DONE;
				});
				return;
			}

			fs_error();
		}
		, FS_proto = FileSaver.prototype
		, saveAs = function(blob, name, no_auto_bom) {
			return new FileSaver(blob, name || blob.name || "download", no_auto_bom);
		}
	;
	// IE 10+ (native saveAs)
	if (typeof navigator !== "undefined" && navigator.msSaveOrOpenBlob) {
		return function(blob, name, no_auto_bom) {
			name = name || blob.name || "download";

			if (!no_auto_bom) {
				blob = auto_bom(blob);
			}
			return navigator.msSaveOrOpenBlob(blob, name);
		};
	}

	FS_proto.abort = function(){};
	FS_proto.readyState = FS_proto.INIT = 0;
	FS_proto.WRITING = 1;
	FS_proto.DONE = 2;

	FS_proto.error =
	FS_proto.onwritestart =
	FS_proto.onprogress =
	FS_proto.onwrite =
	FS_proto.onabort =
	FS_proto.onerror =
	FS_proto.onwriteend =
		null;

	return saveAs;
}(
	   typeof self !== "undefined" && self
	|| typeof window !== "undefined" && window
	|| this.content
));
// `self` is undefined in Firefox for Android content script context
// while `this` is nsIContentFrameMessageManager
// with an attribute `content` that corresponds to the window

if (typeof module !== "undefined" && module.exports) {
  module.exports.saveAs = saveAs;
} else if ((typeof define !== "undefined" && define !== null) && (define.amd !== null)) {
  define("FileSaver.js", function() {
    return saveAs;
  });
}
 function exporttoXl(id,name) {
	  
  var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
  tab_text = tab_text + '<head><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
  tab_text = tab_text + '<x:Name>'+name+'</x:Name>';
  tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
  tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
  tab_text = tab_text + "<table border='1px'>";
  var exportTable = $('#' + id).clone();
  exportTable.find('input').each(function (index, elem) { $(elem).remove(); });
  tab_text = tab_text + exportTable.html();
  tab_text = tab_text + '</table></body></html>';
  var fileName = name + '.xls';
  

  //Save the file
  var blob = new Blob([tab_text], { type: "application/vnd.ms-excel;charset=utf-8" });
  window.saveAs(blob, fileName);

}
 function OpenPop() {
            window.scrollTo(0, 0);
            var width = document.documentElement.clientWidth + document.documentElement.scrollLeft;
            var height = document.documentElement.clientHeight + document.documentElement.scrollTop;
            var layer = document.createElement("div");
            layer.style.zIndex = 2;
            layer.id = "layer";
            layer.style.position = "absolute";
            layer.style.top = "0px";
            layer.style.left = "0px";
            layer.style.height = document.documentElement.scrollHeight + "px";
            layer.style.width = width + "px";
            layer.style.backgroundColor = "black";
            layer.style.opacity = "0.75";
            layer.style.filter += ("progid:DXImageTransform.Microsoft.Alpha(opacity=75)");
            document.body.style.position = "static";
			layer.onkeydown = function(e){
    alert(e.which);
    if(e.which == 27){
        closeIframe();
    }};
			
			layer.onclick = function () {closeIframe();};			
            document.body.appendChild(layer);
            var size = { "height": 680, "width": 640 };
            var iframe = document.createElement("iframe");
            var popUrl = 'popup.htm';
            iframe.name = "profile";
            iframe.id = "popup";
            iframe.src = "";
            iframe.style.height = size.height + "px";
            iframe.style.width = size.width + "px";
            iframe.style.position = "fixed";
            iframe.style.zIndex = 3;
			 iframe.style.overflow = "hidden";
            iframe.style.backgroundColor = "white";
            iframe.frameborder = "0";
            iframe.style.top = ((height + document.documentElement.scrollTop) / 2) - (size.height / 2) + "px";
            iframe.style.left = (width / 2) - (size.width / 2) + "px";
			iframe.onclick = function () {closeIframe();};
			
			 var divv = document.createElement("div");
            divv.style.zIndex = 4;
            divv.id = "container";
            divv.style.position = "fixed";
			 divv.frameborder = "3";
           // divv.style.top = "0px";
          // divv.style.left = "100px";
			iframe.appendChild(divv);
            document.body.appendChild(iframe);
        }
		
function closeIframe() 
{
	
    window.parent.ClosePop();
}

function ClosePop() {
    var layer = document.getElementById("layer");
    var iframe = document.getElementById("popup");
    document.body.removeChild(layer); // remove layer
    document.body.removeChild(iframe); // remove div
}
		
  function profilupdate () {
        // return true or false, depending on whether you want to allow the `href` property to follow through or not
		if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				
				OpenPop();
				var iframe = document.getElementById("popup"); 
				var doc; 
				if(iframe.contentDocument) { 
					doc = iframe.contentDocument; 
				} else {
					doc = iframe.contentWindow.document; 
				}
				doc.body.innerHTML = xmlhttp.responseText;
				
			}
        };	
		xmlhttp.open("POST","updateprofile.php",true);
				
		
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
  }
  var depclass = null;
  function createProfile(jsonobj)
  {
	  var widget = document.createElement('div');
		widget.className = "widget-box";		
		widget.style.width = "400px";
		widget.style.overflow = "hidden";
		widget.id = "profdiv";
			widget.style.marginTop = "-20%";
	widget.style.marginLeft = "55%";
//	widget.style.position = "absolute";
	var widgettitle = document.createElement('div');
	widgettitle.className = "widget-title bg_ly";	
	widgettitle.style.height = "50px";

		widget.appendChild(widgettitle);
	
var widgeticon = document.createElement('img');
	
	widgeticon.className = "icon";		
		widgeticon.style.width = "50px";
		widgeticon.style.height = "50px";
		widgeticon.src = "./includes/images/profile.png";
		widgettitle.appendChild(widgeticon);
		
			
var profimg = document.createElement('img');
	
	profimg.className = "icon";		
		profimg.style.width = "100px";
		profimg.style.height = "100px";
		profimg.src = "./uploads/"+jsonobj.id+".jpg";
		profimg.onerror=function(){this.src='./uploads/Default.png';};
		widget.appendChild(profimg);
		
		var h5 = document.createElement('h5');
		h5.innerText = "Profile";
		widgettitle.appendChild(h5);	

var ulist = document.createElement('ul');
	ulist.className = "recent-posts";
	widget.appendChild(ulist);
		var li = document.createElement('li');
		ulist.appendChild(li);
				var profilediv = document.createElement('div');
	
	profilediv.className = "article-post";
	profilediv.style.height = "50px";
	li.appendChild(profilediv); 

	var p1 = document.createElement('p');
	p1.innerText = jsonobj.name;
	var p2 = document.createElement('p');
	p2.innerText = jsonobj.pos;
	var p3 = document.createElement('p');
	p3.innerText = jsonobj.email;
	var p4 = document.createElement('p');
	p4.innerText = jsonobj.phone;
	
	
	var h1 = document.createElement('h5');
	h1.innerText = "Name";
	
		profilediv.appendChild(h1); 
		profilediv.appendChild(p1);
		
		
		li = document.createElement('li');
		ulist.appendChild(li);
				 profilediv = document.createElement('div');
	
	profilediv.className = "article-post";
	profilediv.style.height = "50px";
	li.appendChild(profilediv); 
	
	var h2 = document.createElement('h5');
		h2.innerText = "Designation";
	
	
   
			profilediv.appendChild(h2); 
		profilediv.appendChild(p2); 
		
		li = document.createElement('li');
		ulist.appendChild(li);
				 profilediv = document.createElement('div');
	
	profilediv.className = "article-post";
	profilediv.style.height = "50px";
	li.appendChild(profilediv); 
		
	var h3 = document.createElement('h5');
	h3.innerText = "Email";
	
	
	
			profilediv.appendChild(h3); 
		profilediv.appendChild(p3);
		
		li = document.createElement('li');
		ulist.appendChild(li);
				 profilediv = document.createElement('div');
	
	profilediv.className = "article-post";
	profilediv.style.height = "50px";
	li.appendChild(profilediv); 
	
	var h4 = document.createElement('h5');
	h4.innerText = "Phone";
	
			profilediv.appendChild(h4); 
		profilediv.appendChild(p4); 
		
	
	
            
                
		
		return (widget);		
		
          
  }
  function  addNode( obj , level, jsonobj)
  {
	    if(1)
		{
			var ulist2 = document.createElement('ul');
			obj.appendChild(ulist2);
			if(level == 0)
			{
				ulist2.className = 'director';
				ulist2.id = 'director';
				depclass = null;
			}
			else if(level == 1)
			{
					ulist2.className = 'departments';
					depclass = ulist2;
			}
			var ulli2 = document.createElement('li');			
			ulist2.appendChild(ulli2);
		}
		else
		{
			var ulli2 = document.createElement('li');
			ulli2.className = "department dep-"+level; 
			depclass.appendChild(ulli2);
		}
		
	

		var a1 = document.createElement('a');
	
		if(level==1)
		{
			a1.className = "self";
			
			/*create self profile div*/
			var widget = createProfile(jsonobj);
			document.getElementById("mainContent").appendChild(widget);
		}
		else
		{
				a1.id = jsonobj.id;
				a1.onclick  = function(){onClickAddGuardian(jsonobj.id);};
		}
		//alert( jsonobj.id);
		a1.url = "#";
		
		ulli2.appendChild(a1);
		
		/*CREATE TABLE*/
		var tbl = document.createElement('table');
		//tbl.style.width = '100%';
	//	tbl.setAttribute('border', '1');
	
		
		var tbdy = document.createElement('tbody');
		 var tr = document.createElement('tr');
		  var td = document.createElement('td');
		 	var img = document.createElement('img');
			img.src = "./uploads/"+jsonobj.id+".jpg";
			 img.onerror=function(){this.src='./uploads/Default.png';};
			img.style.height="50px";
			img.style.width="50px";
			td.appendChild(img);
			tr.appendChild(td);
			
			td = document.createElement('td');			
			var sp1 = document.createElement('span');
			var t = document.createTextNode(jsonobj.name);
				sp1.appendChild(t);
				td.appendChild(sp1);
				var br = document.createElement('br');
				td.appendChild(br);
				var sp2 = document.createElement('span');
					var t1 = document.createTextNode(jsonobj.pos);
					sp2.className = "user-pos";
					sp2.appendChild(t1);
					td.appendChild(sp2);				
				tr.appendChild(td);
		
			
		  tbdy.appendChild(tr);
		tbl.appendChild(tbdy);
		a1.appendChild(tbl);
		
		//<img src="./uploads/600163.jpg" onclick="return profilupdate();" height="100" width="100" align="center" alt="my-image" class="circular-image">
		
		//a1.appendChild(sp1);
	//	alert(jsonobj.name);
		return	ulli2;	
  }
  function onAddGuardian(details)
  {
	    //var shareInfoLen = jsonData.details.length;
	   
	   var text = '{"employees":[' +
'{"firstName":"John","lastName":"Doe" },' +
'{"firstName":"Anna","lastName":"Smith" },' +
'{"firstName":"Peter","lastName":"Jones" }]}';

obj = JSON.parse(details);	
//alert(obj.employees[1].firstName + " " + obj.employees[1].lastName);

	    var iDiv = document.createElement('div');
		iDiv.className = 'content';
		iDiv.id = 'content1';
		document.getElementById('mainContent').appendChild(iDiv);
		
		var fig = document.createElement('figure');
		fig.className = 'org-chart cf';
		iDiv.appendChild(fig);
		
		//var ulist = document.createElement('ul');
		//ulist.className = 'administration';
		//iDiv.appendChild(ulist);
		
		//var ulli1 = document.createElement('li');
		//ulist.appendChild(ulli1);
			//var mydetauil =  JSON.parse(details);
		var mydetail =  obj.details[0];
		if(obj.details[0])
		{
			node0 = addNode(iDiv,0, obj.details[0]);
			node1 = addNode(node0,1, obj.details[1]);
			i =2;
		}
		else
		{
			//node0 = addNode(iDiv,0, obj.details[1]);
			node1 = addNode(iDiv,1, obj.details[1]);
			i =2;
		}
		
		while(obj.details[i])
		{
			 nodex = addNode(node1,i,obj.details[i]);
			 i++;
		}
		if(i<=12)
			document.getElementById("profdiv").style.marginTop = "-"+(i*70)+"px";
		else
			document.getElementById("profdiv").style.marginTop = "-"+(i*80)+"px";
		iDiv.appendChild(ulist);
		
		
  }
  function onClickAddGuardian(selfid)
  {
	  
	  /*Fetch guardian details*/
	  if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				
					var jsonobj = xmlhttp.response;
					jsonobj = jsonobj.replace(/( \r\n|\n|\r)/gm,"");
					jsonobj	= JSON.stringify(jsonobj);
				//var arr = new Array();
				//arr= JSON.parse(jsonobj);
					var jsonobj1= JSON.parse(jsonobj,reviver2); 
					//jsonobj=  jQuery.parseJSON(jsonobj.details); 
					onAddGuardian(jsonobj1);
					document.getElementById("mainContent").style.overflow="hidden";
			
				
			}
        };	
		xmlhttp.open("GET","orgnichart.php?EmployeeID="+selfid,true);
				
		document.getElementById('mainContent').innerHTML ="";
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
  }
  
  function loadorg(selfid)
    {
	  
	  /*Fetch guardian details*/
	  if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				
					document.getElementById("mainContent").innerHTML ="<link href='https://www.jqueryscript.net/css/jquerysctipttop.css' rel='stylesheet' type='text/css'><link href='jquery.orgchart.css' media='all' rel='stylesheet' type='text/css' />"
+ "<style type='text/css'>#orgChart{width: auto;height: auto;}"+
"#orgChartContainer{width: 100%;height: 500px;overflow: auto;background: #FFFFFF;}</style>"+
					
					"<div id='orgChartContainer'><div id='orgChart'></div></div><div id='consoleOutput'></div>";
					   /* var testData = [
						{id: 1, name:  'My Organization', parent: 0},
						{id: 2, name: 'CEO Office', parent: 1},
						{id: 3, name: 'Division 1', parent: 1},						
						{id: 5, name: 'Sub Division', parent: 3},
						{id: 4, name: 'Division 2', parent: 1}
						];*/
						var jsonobj = xmlhttp.response;
					jsonobj = jsonobj.replace(/( \r\n|\n|\r)/gm,"");	

					jsonobj = jsonobj.substring(1,jsonobj.length)						
					var testData= JSON.parse(jsonobj); 
					
					//onAddGuardian(jsonobj1);
						//$.fn.showorg(testData.testData);
						setTimeout(function(){$.fn.showorg(testData.testData);}, 1000);
			
				
			}
        };	
		xmlhttp.open("GET","org.php?EmployeeID="+selfid,true);
		if(newurl != null)
		{
				prervurl = newurl;
		}		
			newurl="org.php?EmployeeID="+selfid;
				
		document.getElementById('mainContent').innerHTML ="";
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
  }
  
function accChange(obj)
{
	
	var x = obj.selectedIndex;
    var y =obj.options;
	if(y[x].text == "Other")
	{
		document.getElementById("accother").readOnly = false;
	}
	else
	{
		document.getElementById("accother").readOnly =true;
		document.getElementById("accother").value ="";
	}
}

function changedwmdate(obj,idform)
{
			 /*Fetch guardian details*/
		  if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {
				// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
					document.getElementById("monthtar").value = xmlhttp.responseText;
					
				}
			};	
			xmlhttp.open("GET","getMonthlyTargetmpcp.php?recordid=" + document.getElementById("recordid").value+
			"&date="+document.getElementById('date').value,true);					
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");			
			xmlhttp.send();
			
		
}


function changeprdEmp()
{
			 /*Fetch guardian details*/
		  if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {
				// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
					if(xmlhttp.responseText.includes("forgot_pwd"))
					{
						window.location.href = "catlmain.php";
					}
					
					document.getElementById("mainContent").innerHTML = xmlhttp.responseText;
					
				}
			};	
			xmlhttp.open("GET","annualappraisal.php?employeeid=" + document.getElementById("employeeid").value,true);					
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");			
			xmlhttp.send();
			
		
}
function changempcpEmp()
{
			 /*Fetch guardian details*/
		  if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {
				// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
					if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
					document.getElementById("mainContent").innerHTML = xmlhttp.responseText;
					
				}
			};	
			xmlhttp.open("GET","mpcpset.php?EmployeeID=" + document.getElementById("employeeid").value,true);					
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");			
			xmlhttp.send();
			
		
}
function changebudgetEmp()
{
			 /*Fetch guardian details*/
		  if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {
				// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
					if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
					document.getElementById("mainContent").innerHTML = xmlhttp.responseText;
					
				}
			};	
			xmlhttp.open("GET","mpcpgapview.php?employeeid=" + document.getElementById("employeeid").value,true);					
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");			
			xmlhttp.send();
			
		
}


function changeuomforcp(obj)
{
	
	var y = document.getElementById("uom");
	var z = document.getElementById("recordid");
	y.selectedIndex = obj.selectedIndex;
	z.selectedIndex = obj.selectedIndex;
	 changedwmdate();
	
}
function removeduplicateinSelect(obj)
{
	var x = obj.selectedIndex;
    var y =obj.options;
	var usedNames = {};
	var i =0;
	for(i=0;i<y.length;i++)
		if(usedNames[y[i].text])
			y.remove(i);
		else
			usedNames[y[i].text] = y[i].text;
}
function changedelegate(obj)
{
	
	var x = obj.selectedIndex;
    var y =obj.options;
	var usedNames = {};
	var i =0;
	for(i=0;i<y.length;i++)
		if(usedNames[y[i].text])
			y.remove(i);
		else
			usedNames[y[i].text] = y[i].text;
	if(y[x].text == "Employee")
	{
			 /*Fetch guardian details*/
		  if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {
				// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
					if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				
					document.getElementById("emplist").style.display = "table-row";
					document.getElementById("empmul").innerHTML = xmlhttp.responseText;
					document.getElementById("empmul").addEventListener("focusout", function (){alert("You cannot delete the delegation once submitted.\nPlease cross check!");});
					
				}
			};	
			xmlhttp.open("GET","getDelegatedList.php?recordid=" + document.getElementById(selectedid).value,true);					
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");			
			xmlhttp.send();
			
		
	}
	else
	{
		document.getElementById("emplist").style.display = "none";
	}
}
function selectbudgetcp(obj)
{
	obj1 = document.getElementById('focusarea');
	obj2 = document.getElementById('periodtb');
	{
		x = obj1.selectedIndex;
		y =obj1.options;
	}
	empid = "&employeeid=" + document.getElementById("employeeid").value;
	loaddata("mpcpgapview.php"+"?focusarea="+ encodeURIComponent(y[x].text)+"&periodtb="+obj2.value+"&recordid="+y[x].value+empid);
	
}
function selectcp(obj)
{
	obj1 = document.getElementById('focusarea');
	obj2 = document.getElementById('periodtb');
	{
		x = obj1.selectedIndex;
		y =obj1.options;
	}
	fy = "&fy=" + document.getElementById("fy").value;
	loaddata("Dwmmstr.php"+"?focusarea="+ encodeURIComponent(y[x].text)+"&periodtb="+obj2.value+"&recordid="+y[x].value+fy);
	
}
function mytbonload()
{
	 var table = document.getElementById("mpcptb");
	 if(table)
	 {
		var rows = table.rows.length;
		var i = rows-1;
		var lastNo= 1;
		if(rows>1)
			for(j=0;j<table.tHead.rows[0].cells.length;j++)
			{
				if(j=0)
				{
					//table.tHead.rows[0].cells[j].style.width = table.tBodies[0].rows[0].cells[j].style.width= "2%";	
				}
				//table.tHead.rows[0].cells[j].offsetWidth = table.tBodies[0].rows[0].cells[j].offsetHeight;
			}
			
	 }

}
function showmyteam(id)
{
 if(document.getElementById(id).style.display=="none")
 {
 	document.getElementById(id).style.display="block";
	document.getElementById('showteam').innerText="Hide My Team KRA Status";
 }
 else
 {
 	document.getElementById(id).style.display="none";	
	document.getElementById('showteam').innerText="Show My Team KRA Status";
 	
 }
}
function getchart(a)
{
	  /*Fetch guardian details*/
	  if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				
					   /* var testData = [
						{id: 1, name:  'My Organization', parent: 0},
						{id: 2, name: 'CEO Office', parent: 1},
						{id: 3, name: 'Division 1', parent: 1},						
						{id: 5, name: 'Sub Division', parent: 3},
						{id: 4, name: 'Division 2', parent: 1}
						];*/
						var jsonobj = xmlhttp.response;
					jsonobj = jsonobj.replace(/( \r\n|\n|\r)/gm,"");	

					//jsonobj = jsonobj.substring(1,jsonobj.length)						
					var testData= JSON.parse(jsonobj); 
					var ctx = document.getElementById("myChart");
					Chart.defaults.global.legend.display = false;
					var myChart =  new Chart(ctx,{
											type:'bar',
											data: testData[0]
											}
											);
											
				if(((testData[0].datasets[4].data[15]*0.98)>=testData[0].datasets[4].data[16])&&
					testData[0].datasets[1].backgroundColor == "Red")	
					{					
						document.getElementById("better").src ="./includes/images/up.png";
					}
				else if(((testData[0].datasets[4].data[15]*0.98)<=testData[0].datasets[4].data[16])&&
					testData[0].datasets[1].backgroundColor == "Green")
					{
						document.getElementById("better").src ="./includes/images/up.png";
					}
				else
				{
					document.getElementById("better").src ="./includes/images/down.png";
				}

				
					//onAddGuardian(jsonobj1);
						//$.fn.showorg(testData.testData);
					//	setTimeout(function(){$.fn.showorg(testData.testData);}, 1000);
				
			}
        };	
		var res = a.split("?");
		xmlhttp.open("GET","budgetchartview.php?"+res[1],true);
		//if(newurl != null)
		//{
		//		prervurl = newurl;
		//}		
		//newurl="org.php?EmployeeID="+selfid;
				
	//	document.getElementById('mainContent').innerHTML ="";
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
				
	/*
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
               'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
			
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    }
});*/
}

function confirm_alert(node) {
    if(confirm('Once approved, KRA cannot be modified \nClick on OK to continue.'))
	{
		loaddata(node);
	}
}
function changecomprating(id)
{
	var desiredele = document.getElementById("desired"+id);
	var desiredtxt = (desiredele.innerText);
	desire = desiredtxt.split("L");
	desired = parseInt(desire[1]);
	var actualele = document.getElementById("rating"+id);
	actualtxt = (actualele[actualele.selectedIndex].value);
	actuals = actualtxt.split("L");
	actual= parseInt(actuals[1]);
	var tni1 = document.getElementById("tni"+id);
	var tni2 = document.getElementById("tnifunc"+id);
	if(actual < desired)
	{
			tni1.style.display ="block";
			tni2.style.display ="block";
	}
	else
	{
			tni1.style.display ="none";
			tni2.style.display ="none";
	}
}
function closeInput(elm) {
	
	
	
		 /*Fetch guardian details*/
		  if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {
				// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
				if(xmlhttp.responseText.includes("forgot_pwd"))
				{
					window.location.href = "catlmain.php";
				}
				else if(xmlhttp.responseText.includes("SUCCESS"))
				{
					    var td = elm.parentNode;
						var value = elm.value;
						td.removeChild(elm);
						td.innerHTML = value;
				}
				else
				{
					alert("Failed to Update data.\nTry to update the data using the update entry button");
				}
					
				}
			};
			var td = elm.parentNode;
			var value = elm.value;			
			res = td.id.split("td");
			recordid= res[1];
			res = elm.id.split("id");
			period= res[1];
			xmlhttp.open("GET","mpcpupdateactual.php?recordid="+recordid+"&monthactual="+value+"&periodtb="+period,true);					
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");			
			xmlhttp.send();
			
		
	
	

}
function showtextBox(recordid,period)
{
	if (document.getElementById("id"+period) != null)
	{
		alert("Previous update is pending.\nKindly refresh the page and try again!");
		return;
	}
	
    var value = document.getElementById("td"+recordid).innerHTML;
    document.getElementById("td"+recordid).innerHTML = '';

    var input = document.createElement('input');
    input.setAttribute('type', 'text');
	input.setAttribute('name', 'monthactual');
    input.setAttribute('value', value);
	input.setAttribute('id', "id"+period);
    input.setAttribute('onBlur', 'closeInput(this)');
    document.getElementById("td"+recordid).appendChild(input);
    input.focus();
	
}