
var xmlHttp = createXmlHttpRequestObject(); 

function createXmlHttpRequestObject() 
{
 var xmlHttp;


if(window.XMLHttpRequest)
 {
 try {
 xmlHttp = new XMLHttpRequest();
 }
 catch (e) {
 xmlHttp = false;
 }
 }

 if (!xmlHttp)
 alert("Error creating the XMLHttpRequest object.");
 else 
 return xmlHttp;
}
function process(pid)
 { 
 if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
  {
 
var name =pid;

 xmlHttp.open("GET", "quickstart.php?id=" + name, true); 
 xmlHttp.onreadystatechange = handleServerResponse;
 xmlHttp.send(null);
   }
 else
 {

 setTimeout('process()', 1000);
     }
}
function handleServerResponse() {
    if (xmlHttp.readyState == 4) {
        if (xmlHttp.status == 200) {
            xmlResponse = xmlHttp.responseXML;
            profilePic = xmlResponse.getElementsByTagName('profile_pic')[0].textContent;
            
            // Extract the
            var userId = this.responseURL.split('=')[1];
            
            document.getElementById("modal-image" + userId).src = profilePic;
        } else {
            alert("There was a problem accessing the server: " + xmlHttp.statusText);
        }
    }
}

