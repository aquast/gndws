/* - pndId.js - */
/*
 * enables GND Triplestore requests for pndIdent within creator Input-fields 
 *
 * Requires jQuery (http://jquery.com/) 
 * author Andres Quast
 */

$(document).ready(addPndForm);

var pRow;

//add a PersonID-field to the form
function addPndForm(){
	var suchImage = "<div class=\"pnd\" style=\"margin-top:4px;\"><img src=\"../Icons/search_icon.jpg\" alt=\"PND nach Person durchsuchen\" style=\"float:left;\" />"
	+ "<input class=\"pndid\" type=\"text\" size=\"30\" value=\"PND-ID\" /></div>";
	var imageDiv = $(".person").parent().append(suchImage);
	//$(".lastName").blur(requestPersonIdBlur);
	$("head").append("<link  rel=\"stylesheet\" type=\"text/css\" href=\"/css/loddiggr.css\"/>");
	$("img").click(requestPersonId);
	$("body").append("<div class=\"result\"><h4>Ergebnis der PND ID-Abfrage</h4></div>");
	$(".result").hide();

};


function requestPersonId(){
    $(".resultalert").remove();	
    $(".result").hide();
    $(".item").remove();
	var personName = $(this).parent().parent().find(".person").val().split(",", 2);
	
	var firstName = personName[1].replace(" ", "");
	var lastName = personName[0].replace(" ", "");
	
	pRow = $(this).parent().parent().parent();
	if(pndFormCheck(firstName, lastName)){
		requestGndService(firstName, lastName); 	
		$(this).parent().parent().find(".pnd").append("<div class=\"resultalert\"></div>");
		//$(this).parent().parent().find("img").remove();
    	}
};

function requestPersonIdBlur(){
    $(".resultalert").remove();	
    $(".result").hide();
    $(".item").remove();

    //$(this).parent().parent().find(".lastName").unbind("blur");
	
	var firstName = $(this).parent().parent().find(".firstName").val();
	var lastName = $(this).parent().parent().find(".lastName").val();

	pRow = $(this).parent().parent();
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
	var requestUrl = "http://phacops.dyndns.org:8080/axis2/services/gndRequester/getGndPersonInfo?firstName=" 
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
					$(this).parent().parent().find("ul li strong").remove();
					var pnd = $(this).parent().parent().find("ul li:first-child").text();
					//var firstName = $(this).parent().parent().find("ul li:first-child").text();
					pRow.find("input.PNDIdentNumber").val(pnd);
					$(".result").hide();
				});
			})
			.fail(function(){
				alert("request failed: " + jqxhr.statusText);
				})
			.always(function(){
			});
    return jqxhr.responseText;
            	

}

function responseParser(xml){
	var resultField = "";
	var pndResult = $(xml).find("result");
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
