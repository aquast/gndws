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
       function requestServer(number) {
    	   	//alert('Server Request startet');
            var lastName = $('input.lastName');
            var firstName = $('input.firstName');
            var requestUrl;
            //validate form-fields:
            if(pndFormCheck(number)) {
            	// create requestURL:
            	requestUrl = 'https://alkyoneus.hbz-nrw.de/gnd/gndrequest.jsp?firstName=' + firstName[number].value + "&lastName=" + lastName[number].value + '&index=' + number;
        	   	//requestUrl = 'include.html';
            	//requestUrl = 'pom.xml';
            	
            	var options = {
            			type: 'GET',
            			url: requestUrl,
            			dataType: 'xml text',
            			
            			success: (function (data, textstatus, jqxhr){
            				response = jqxhr.responseXML;
            				
            				jQuery('div.resultarea').append(response + '<p>Hallo</p>');
            				alert('Anfrage geht ' + response);
            			})
            		
            		}
            
            	//jQuery.ajax('include.txt');
            	alert('starte Anfrage');
            	
            	jQuery.ajax(options);
            	
            	
            	//alert('hinter der Anfrage');
            	/*$.ajax({
            		 url: 'include.txt',
            	}).done(function(html){
            		alert('result: ' + html);
            	});*/
            	//var inp = jQuery('.resultarea');
            	//inp.append('<p>huhu</p>');
            }
	  }
       
       function requestMe(index){
    	   return false;
       }


function addOnClicks() {
// function creates javascript submit buttons 
// for starting a GND triple store search in a new
// window 
var number = 0;
var pndField = $('input.PNDIdentNumber');
    
    pndField.parent()
    	//.append(function(index){return jQuery('<img src="searchpnd.png" alt="PND nach Person durchsuchen" onclick="requestServer(' + index + ')" />');});
    	.append(function(index){return jQuery('<input type="image" src="../search_icon.jpg" alt="PND nach Person durchsuchen" onclick="requestServer(' + index + '); return false;" />');});

}

jQuery(document).ready(addOnClicks);