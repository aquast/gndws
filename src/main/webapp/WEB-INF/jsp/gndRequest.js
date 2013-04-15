/* - gndrequest.js - */
/*
 * enables GND Triplestore requests for pndIdent within creator Input-fields 
 *
 *
 * Requires jQuery http://jquery.com/ 
 *
 */

        /*
         * Checks if all required fields are filled
         */ 
		function pndFormCheck(number){
            var lastName = $('input.lastName');
            var firstName = $('input.firstName');
            if(lastName[number].value == null || lastName[number].value == "" || firstName[number].value == null || firstName[number].value == ""){
                alert('Füllen Sie bitte zunächst Nach- und Vornamenfelder aus');
            return false;
            }
            return true;

        }

	  /*
	   * creates Url for WS-Request and requests the WS via JSP page
	   */
       function createUrl(number) {
            var lastName = $('input.lastName');
            var firstName = $('input.firstName');
            if(pndFormCheck(number)){
            PersonData = window.open("https://alkyoneus.hbz-nrw.de/gnd/gndrequest.jsp?firstName=" + firstName[number].value + "&lastName=" + lastName[number].value + "&index=" + number, "Person Data", "height=800,width=600,scrollbars=yes");
            PersonData.focus();
            }
      	  return false;
	  }


	 /*
	  * send back the pnd information for the chosen person to the parent window
	  */
	  function sendBackToParentWindow(gndData) {
		  var pkn = gndData;
		  //find the index value submitted as get-parameter 
		  var url = window.location.search.split("=");
		  window.opener.jQuery('input.lastName')[url[3]].value = pkn.children[1].children[0].children[0].textContent;
		  window.opener.jQuery('input.firstName')[url[3]].value = pkn.children[1].children[0].children[1].textContent;
		  window.opener.jQuery('input.PNDIdentNumber')[url[3]].value = pkn.children[1].children[0].children[2].textContent;
          self.close();
	  return false;
	}


function addOnClicks() {
// function creates javascript submit buttons 
// for starting a GND triple store search in a new
// window 
var number = 0;
var pndField = $('input.PNDIdentNumber');
    
    pndField.parent()
         .append(function(index){return jQuery('<input type="image" src="searchpnd.png" alt="PND nach Person durchsuchen" onclick="createUrl(' + index + ')" />');});
}

jQuery(document).ready(addOnClicks);