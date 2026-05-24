function setTypeForLeave (type, rowid) {

	if(type =="ABSENT")
	{
		/*if (confirm('Are you sure to change the type to ABSENT?This will reset the hours and other fields'))
		 {
		
		 
		 }
		 else
		 {

		 	return;
		 }*/
		spice  = document.getElementsByName("spic"+rowid);
		if(spice!=null)
		{
			spice[0].options.selectedIndex=-1;
		}
		man = document.getElementsByName("man"+rowid);
				
		if(man!=null)
		{
			
			man[0].value ="";
			
		}
		act = document.getElementsByName("act"+rowid);
				
		if(act!=null)
		{
			
			act[0].value ="";
			
		}
		timein = document.getElementsByName("timein"+rowid);
				
		if(timein!=null)
		{
			
			timein[0].value ="0.00";
			
		}
	}
};
function getEmployeeDetails (employeeid, rowid) {
        	
		//alert(myTextField);
		if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function()
        {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
			{			
				var myObj = JSON.parse(this.responseText);
				var x = document.getElementsByName("sec"+rowid);
				if(x!=null)
				{
					for(i=0;i<x[0].options.length;i++)
						if(x[0].options[i].value == myObj.section)
						{
							x[0].selectedIndex = i;
							break;
						}

				}
				empid = document.getElementsByName("empid"+rowid);
				if(empid!=null)
				{
					empid[0].value = myObj.employeeid.toString();
				}
				x = document.getElementsByName("grade"+rowid);
				if(x!=null)
				{
					x[0].value = myObj.grade;
				}

				spice  = document.getElementsByName("spic"+rowid);
				if(spice!=null)
				{
					if(myObj.grade=="WTR")
						spice[0].options.selectedIndex = -1;
				}
				//typeval = type.options[type.selectedIndex].value;
				man = document.getElementsByName("man"+rowid);
				
				if(man!=null)
				{
					if(myObj.grade=="WTR")
					{
						man[0].value ="";
					}
					else
						man[0].value = myObj.manning;
				}
				act = document.getElementsByName("act"+rowid);
				if(act!=null)
				{
					if(myObj.grade=="WTR")
					{
						act[0].value =""
					}
					else						
						act[0].value = myObj.actgrade;
				}
				type = document.getElementsByName("type"+rowid);
				if(type!=null)
				{
					if(type[0].options[type[0].selectedIndex].value == "ABSENT")
					{
						act[0].value = "";
						man[0].value ="";
						spice[0].options.selectedIndex = -1;
					}
				}				
			}
        };	
		xmlhttp.open("GET",
					"getEmployeeDetails.php?employeeid="+employeeid+"&param1=worksection&param2=grade&param3=manning");
					
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
        xmlhttp.send();
	
		return false;
    }
	
