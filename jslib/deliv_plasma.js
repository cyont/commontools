/*
 * Deliverance JS Script
 *   This script handles synchronously fetching and fading deliverance driven display blocks.
 * 
 * Note: all the code which is not included in a function gets executed right away.
 * 
 * Author: Jaime & Nick
 */

// global variables/paramaters
var imageID = "image_2";
var inter;
var current = "image_1";
var connPage="connection.php";
var attempts = 0;
var maxAttempts = 10;
var defaultSlideTime = 15000;
var transitionSpeed = 2000;

// whichever page calls this routine, its URL will be placed in this variable.
var currPage = location.href;

// every 5 minutes, check the connection and reload if possible. Otherwise, stay with the two we have.
setInterval(check_connection_and_reload, 300000);

// This function begins the chain of the script and should generally be called onload of the body
function firstLoad() {
	firstRun = true;
	updateBlock();
	setTimeout(switch_images, defaultSlideTime);
}

function setMaxAttempts(value) {
	maxAttemps = value;
}

function setDefaultSlideTime(value) {
	defaultSlideTime = value
}

function setTransitionSpeed(value) {
	transitionSpeed = value;
}

// this function fades the image blocks and makes the appropriate calls back to itself using defaultSlideTime as a delay and cycles the images using updateBlock
function switch_images() {
	if(current=="image_1") {
		// Fade the two blocks appropriatly and call an annonomys fuction to move to the update and next slide when animation is done
		$("#image_1").fadeOut(transitionSpeed);
		$('#image_2').fadeIn(transitionSpeed, function() {
			// this is the image which is now being displayed
			current = "image_2";
			// the image that is invisible is the one we are going to update.
			imageID = "image_1";
			// call updateBlock and let it cycle a new image2 from deliverance
			updateBlock();
			// wait for the the defined slide time and call back to this switch_images fuciton again
			setTimeout(switch_images, defaultSlideTime);
		});
 	}
	else if(current=="image_2") {
		// Fade the two blocks appropriatly and call an annonomys fuction to move to the update and next slide when animation is done
		$('#image_2').fadeOut(transitionSpeed);
		$("#image_1").fadeIn(transitionSpeed, function() {
			// this is the image which is now being displayed
			current = "image_1";
			// the image that is invisible is the one we are going to update.
			imageID = "image_2";
			// call updateBlock and let it cycle a new image1 from deliverance
			updateBlock();
			// wait for the the defined slide time and call back to this switch_images fuciton again
			setTimeout(switch_images, defaultSlideTime);
    });
  }
}

// This function is called to update the image divs using logic of the current and imageID variables by calling the delivPage to get the new content each time
function updateBlock(){
	var ajaxRequest;  // The variable that makes Ajax possible!
	try {
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	}
	catch (e) {
		// Internet Explorer Browsers
		try {
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function() {
		if(ajaxRequest.readyState == 4) {
			// Take the new image and put it in place
			if (firstRun==true) {
				// If this is the firstRun define the cur and prev images by hand
				curImage = document.getElementById("image_1").innerHTML;
				prevImage = document.getElementById("image_2").innerHTML;
			}
			else {
				// Otherwise pick up from where we last were
				curImage = document.getElementById(current).innerHTML;
				prevImage = document.getElementById(imageID).innerHTML;
			}
			
			// A bunch of cleanup before we compare the different content returned to see if we have something new
			if (curImage.substring(curImage.length - 3)!=" />") {
				curImage = curImage.substring(0,curImage.length - 1);
				curImage = curImage+" />";
			}
			if (prevImage.substring(prevImage.length - 3)!=" />") {
				prevImage = prevImage.substring(0,prevImage.length - 1);
				prevImage = prevImage+" />";
			}
			newImage = ajaxRequest.responseText;
			if (newImage.substring(newImage.length - 3)!=" />") {
				newImage = newImage.substring(0,newImage.length - 1);
				newImage = newImage+" />";
			}
			// compare to see if the images max, go ahead and enter if we reached max attemps otherwise fall to else and call back
			if (curImage!=newImage&&prevImage!=newImage||attempts>=maxAttempts) {
				// if we got something new go ahead and right it into the div, otherwise its similair to one showing and keep what we have, this should prevent displaying the same back and forth when only 2 are present while keeping cases for large number of displays
				if (!(curImage!=newImage&&prevImage==newImage||attempts>=maxAttempts)) {
					//alert("cur: \""+curImage+"\"\n\nnew: \""+newImage+"\"\n\nisnew: "+(curImage!=newImage));
					document.getElementById(imageID).innerHTML = newImage;
					//alert(newImage);
					//alert(imageID);
				}
				// we are now past first run in any case
				firstRun = false;
				// reset the attempts for the next attempt at updating the blocks
				attempts = 0;
			}
			else {
				attempts++;
				updateBlock();
			}
		}
	}
	// this defines where we will make the ajax call to
	ajaxRequest.open("POST", './'+delivPage, true);
	// this sends the call
	ajaxRequest.send(null); 
}

function check_connection_and_reload()
{
  
  var xmlHttp;
  
  // we go down the list of compatible browsers,
  // through a series of try/catch blocks.
  // if we don't find one, we give and error message.
  try
  {
      // Firefox, Opera 8.0+, Safari
      xmlHttp=new XMLHttpRequest();
  }
  catch (e)
  {
      // Internet Explorer
      try
      {
        xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
      catch (e)
      {
        try
        {
            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e)
        {
            alert("Your browser does not support AJAX!");
            return false;
        }
      }
  }
  
  xmlHttp.onreadystatechange = function() 
  {
    if(xmlHttp.readyState==4) 
    {
      if(xmlHttp.responseText=='success') 
      {
        window.location.reload();
      }
    }
  };
  
  xmlHttp.open("POST", './'+connPage, true);
  xmlHttp.send('');

}

function switch_onreload(curmenu){
  
  document.getElementById("image_1").style.opacity =0;
  document.getElementById("image_2").style.opacity =0;
  
  if(curmenu == "image_1" ) 
  {
    curmenu = "image_2";
  } 
  else if(curmenu == "image_2") 
  {
    curmenu = "image_1";
  } 
  
  document.getElementById(curmenu).style.opacity =1;
  current=curmenu;
  
}
