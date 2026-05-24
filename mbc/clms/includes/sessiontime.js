       // var sessionTimeout = 1440; 
        
		window.addEventListener('load', function ()
        {
            //assigning minutes left to session timeout to Label
            document.getElementById("sessiontime").innerText = sessionTimeout;
            sessionTimeout = sessionTimeout - 1;
            
            //if session is not less than 0
            if (sessionTimeout >= 0)
                //call the function again after 1 minute delay
                window.setTimeout("DisplaySessionTimeout()", 1000);
            else
            {
                //show message box
               // alert("Your current Session is over.");
            }
        });
		function autosaveData()
		{
				if(document.getElementById("autosaveid")!=null)
				{
					document.getElementById("autosaveid").click();
				}
		}
		function DisplaySessionTimeout()
        {
            //assigning minutes left to session timeout to Label
            document.getElementById("sessiontime").innerText = sessionTimeout;
            sessionTimeout = sessionTimeout - 1;
            if (sessionTimeout==0)
			{
			alert("Your current Session is over.");return;}
            //if session is not less than 0
            if (sessionTimeout >= 60)
                //call the function again after 1 minute delay
                window.setTimeout("DisplaySessionTimeout()", 1000);
            else
            {
				if (sessionTimeout < 55)
				{
					window.setTimeout("DisplaySessionTimeout()", 1000);
					return;
				}
                //show message box
				window.setTimeout("autosaveData()",3000);
               // alert("Save Your data. Session will time out in 60 secs");
				
			//	window.setTimeout("DisplaySessionTimeout()", 5000);
            }
        }
		