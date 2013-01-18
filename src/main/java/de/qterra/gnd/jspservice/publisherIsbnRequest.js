/* - bastPublisherRequest.js - */
/*
 * enables request to Publisher "NW-Verlag Bremerhaven" via ISBN 
 *
 *
 * Requires jQuery http://jquery.com/ 
 *
 */


	  /*
	   * creates Url for WS-Request and requests the WS via JSP page
	   */
       function createUrl() {
            var isbn = $('td.b.text()');
            if(isbn != 0){
            var url = "http://alkyoneus.hbz-nrw.de/gnd/gndrequest.jsp?firstName=" + issn);
            }
      	  return false;
	  }

       
 	  /*
 	   * read the ISBN from metadata table in index.html
 	   */
       function getIsbn() {
    	   var isbn = null;
    	   var tdLine = $('td.b');
    	   if(tdLine.value == 'ISBN: '){
    		   isbn = tdLine.parent.td:last;
    	   }
       }



function addOnClicks() {
// function creates javascript submit buttons 
// for starting a GND triple store search in a new
// window 
    
	var linkPlace = $("hr"); 
    linkPlace.parent()
         .append(function(){return jQuery('<a href="getIsbn()" alt="Gehe zur Verlagsversion" />');});
}

jQuery(document).ready(addOnClicks);