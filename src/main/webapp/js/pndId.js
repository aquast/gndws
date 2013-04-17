/* - pndId.js - */
/*
 * enables GND Triplestore requests for pndIdent within creator Input-fields 
 *
 * Requires jQuery (http://jquery.com/) 
 * author Andres Quast
 */

$(document).ready(addPndForm);

var pRow;

function addPndForm(){
	var suchImage = "<div class=\"pnd\" style=\"float:left;\"><img src=\"../image/search_icon.jpg\" alt=\"PND nach Person durchsuchen\" /></div>";
	var imageDiv = $(".PNDIdentNumber").parent().append(suchImage);
	//$(".lastName").blur(requestPersonId);
	$("img").click(requestPersonId);
	$("body").append("<div class=\"result\"><h4>Ergebnis der PND ID-Abfrage</h4></div>");
	$(".result").hide();

};

function requestPersonId(){
    $(".resultalert").remove();	
    $(".result").hide();
    $(".item").remove();
	var firstName = $(this).parent().parent().parent().find(".firstName").val();
	var lastName = $(this).parent().parent().parent().find(".lastName").val();
	
	pRow = $(this).parent().parent().parent();
	if(pndFormCheck(firstName, lastName)){
		requestGndService(firstName, lastName); 	
		$(this).parent().parent().find(".pnd").append("<div class=\"resultalert\"></div>");
		//$(this).parent().parent().find("img").remove();
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
				$("div.resultalert").append($(jqxhr.responseText).find("resultSize").text());
				$("div.resultalert").slideDown("slow");
				$("div.resultalert").click(function(){
					$(".result").show();
					});
				$("div.result").append(responseParser(jqxhr.responseText));
				$("a.item strong").click(function(){
					var pnd = $(this).parent().parent().find("ul li:first-child").text();
					pRow.find("input.PNDIdentNumber").val(pnd);
					$(".result").hide();
				});
			})
			.fail(function(){
				alert("request failed: " + jqxhr.statusText);
				var xml = "<test><resultSize>4</resultSize></test>";
				$("div.resultalert").append($(xml).find("resultSize").text());
				$("div.resultalert").slideDown("slow");
				})
			.always(function(){
			});
		//alert(response);
	//alert("warte mal: " + jqxhr.responseText);
	//alert("warte mal2: " + testText);
    return jqxhr.responseText;
            	

}

function responseParser(xml){
	var resultField = "";
	var pndResult = $(xml).find("result");
	//pndResult.length();
	pndResult.each(function(){
		var prefferedName = $(this).find("prefferedName").text();
		var pndUri =$(this).find("pndUri").text();
		var pndId = $(this).find("pndID").text();
		var biogr = $(this).find("biograficData").text();
		var birth =$(this).find("yearOfBirth").text();
		
		resultField = resultField + "<div class=\"item\" ><a href=\"#\" class=\"item\" ><strong>" + prefferedName + "</strong></a>" 
			+ "<ul><li><strong>PND-ID: </strong>" +  pndId + "</li>"
			+ "<li><strong>Geburtsjahr: </strong>" + birth + "</li>"
			+ "<li><strong>Bibliographische Daten: </strong>" + biogr  + "</li></ul></div>";
	});
	return resultField;
}

function insertField(){
	;
}