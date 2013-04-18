/* - pndId.js - */
/*
 * enables GND Triplestore requests for pndIdent within creator Input-fields 
 *
 * Requires jQuery (http://jquery.com/) 
 * author Andres Quast
 */

$(document).ready(addPndForm);


function addPndForm(){
	var suchImage = "<div style=\"float:left;\"><img src=\"../image/search_icon.jpg\" alt=\"PND nach Person durchsuchen\" /></div>";
	var imageDiv = $(".PNDIdentNumber").parent().append(suchImage);
	$(".lastName").blur(requestPersonId)
	$("img").click(requestPersonId);
	$("body").append("<div class=\"result\"><h4>Ergebnis der PND ID-Abfrage</h4></div>");
	$(".result").hide();
};

function requestPersonId(){
    $(".result").slideUp();	
	var firstName = $(this).parent().parent().parent().find(".firstName").val();
	var lastName = $(this).parent().parent().parent().find(".lastName").val();
	if(pndFormCheck(firstName, lastName)){
		requestGndService(firstName, lastName); 	
/*PersonData = window.open("https://alkyoneus.hbz-nrw.de/gnd/gndrequest.jsp?firstName=" + firstName + "&lastName=" + lastName, "Person Data", "height=800,width=600,scrollbars=yes");
    	PersonData.focus();*/
    	}
};

function pndFormCheck(firstName, lastName){
    if(lastName == null || lastName == "" || firstName == null || firstName == ""){
        alert('Füllen Sie bitte zunächst Nach- und Vornamenfelder aus');
    return false;
    }
    return true;
}

function requestGndService(firstName, lastName){
	//return $("<p>" + lastName + ", " + firstName + "</p>");
	var requestUrl = "http://localhost:8080/axis2/services/gndRequester/getGndPersonInfo?firstName=" 
	+ firstName + "&lastName=" + lastName;
	var options = {
			
			type: 'GET',
			url: requestUrl,
			dataType: 'xml',
            	}
            
     var testText;
     var jqxhr = jQuery.ajax(options)
			.done(function(){
				$("div.result").append(jqxhr.responseText);
				$("div.result").slideDown("slow");
			})
			.fail(function(){
				alert("request failed: " + jqxhr.statusText);
				$("div.result").append(jqxhr.statusText);
				$("div.result").slideDown("slow");
				})
			.always(function(){
				//$("div.result").append(jqxhr.responseText);
				//$("div.result").slideDown("slow");
			});
		//alert(response);
	//alert("warte mal: " + jqxhr.responseText);
	//alert("warte mal2: " + testText);
    return jqxhr.responseText;
            	

}
