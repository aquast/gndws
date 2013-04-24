/* - pndId.js - */
/*
 * enables GND Triplestore requests for pndIdent within creator Input-fields 
 *
 * Requires jQuery (http://jquery.com/) 
 * author Andres Quast
 */

$(document).ready(addPndForm);

var pRow;

//add a PersonID-Input field to the form
function addPndForm(){
	var suchImage = "<div class=\"pnd\" style=\"margin-top:4px;\"><img src=\"../Icons/search_icon.jpg\" alt=\"PND nach Person durchsuchen\" style=\"float:left;\" />"
	+ "<input class=\"pndid\" type=\"text\" size=\"30\" value=\"PND-ID\" /></div>";
	var imageDiv = $(".person").parent().append(suchImage);
	$("head").append("<link  rel=\"stylesheet\" type=\"text/css\" href=\"/css/loddiggr.css\"/>");
	$("img").click(requestPersonId);
	$("body").append("<div class=\"result\"><div class=\"close\"><img src=\"../Icons/close-icon.png\" /></div><h4>Ergebnis der PND ID-Abfrage</h4></div>");
	$(".person").blur(requestPersonIdBlur);
	$(".result").hide();
	$(".close").click(function(){$(".result").slideUp("slow");});

};

// create the remote PND request
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
		//$(this).parent().parent().find(".pnd").append("<div class=\"resultalert\"></div>");
		//$(this).parent().parent().find(".pnd img").remove();
	}
};

function requestPersonIdBlur(){
    $(".resultalert").remove();	
    $(".result").hide();
    $(".item").remove();

    $(this).parent().parent().find(".person").unbind("blur");
	
	var personName = $(this).parent().parent().find(".person").val().split(",", 2);
	
	var firstName = personName[1].replace(" ", "");
	var lastName = personName[0].replace(" ", "");

	pRow = $(this).parent().parent();
	if(pndFormCheck(firstName, lastName)){
		backgroundRequestGndService(firstName, lastName); 	
		$(this).parent().parent().find(".pnd").append("<div class=\"resultalert\"></div>");
    	}
};


function pndFormCheck(firstName, lastName){
    if(lastName == null || lastName == "" || firstName == null || firstName == ""){
        alert('Füllen Sie bitte zunächst Nach- und Vornamenfelder aus');
    return false;
    }
    return true;
}

// perform remote request
function requestGndService(firstName, lastName){
	var requestUrl = "http://nyx.hbz-nrw.de/loddiggr/gndRequester/getGndPersonInfo?firstName=" 
	+ firstName + "&lastName=" + lastName;
	var options = {
			
			type: 'GET',
			url: requestUrl,
			dataType: 'xml',
            	}
            
     var testText;
     var jqxhr = jQuery.ajax(options)
			.done(function(){
				//$("div.resultalert").append("Hinweis: Es gibt <strong>" + $(jqxhr.responseText).find("resultSize").text() + " PND ID</strong> für diesen Namen");
				//$("div.resultalert").slideDown("slow");
				$(".result").slideDown("slow");

				// make resultalert click able:
				//$("div.resultalert").click(function(){
				//	$(".result").show();
				//	});
				$("div.result").append(responseParser(jqxhr.responseText));
				$("a.item strong").click(function(){
					$(this).parent().parent().find("ul li strong").remove();
					var pnd = $(this).parent().parent().find("ul li:first-child").text();
					pRow.find("input.pndid").val(pnd);
					$(".result").slideUp("slow");
				    $(".resultalert").slideUp("slow");	
					
				});
			})
			.fail(function(){
				alert("request failed: " + jqxhr.statusText);
				})
			.always(function(){
			});
    return jqxhr.responseText;
            	

}

//perform remote request
function backgroundRequestGndService(firstName, lastName){
	var requestUrl = "http://nyx.hbz-nrw.de/loddiggr/gndRequester/getGndPersonInfo?firstName=" 
	+ firstName + "&lastName=" + lastName;
	var options = {
			
			type: 'GET',
			url: requestUrl,
			dataType: 'xml',
            	}
            
     var testText;
     var jqxhr = jQuery.ajax(options)
			.done(function(){
				$("div.resultalert").append("Hinweis: Es gibt <strong>" + $(jqxhr.responseText).find("resultSize").text() + " PND ID</strong> für diesen Namen");
				$("div.resultalert").slideDown("slow");
				
				// make resultalert click able:
				$("div.resultalert").click(function(){
					$(".result").slideToggle("slow");
					});
				
				$("div.result").append(responseParser(jqxhr.responseText));
				$("a.item strong").click(function(){
					$(this).parent().parent().find("ul li strong").remove();
					var pnd = $(this).parent().parent().find("ul li:first-child").text();
					pRow.find("input.pndid").val(pnd);
					$(".result").slideUp("slow");
				    $(".resultalert").slideUp("slow");	
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
		var personName = prefferedName.split(",", 2);
		
		var firstName = personName[1].replace(" ", "");
		var lastName = personName[0].replace(" ", "");
		
		var pndUri =$(this).find("pndUri").text();
		var pndId = $(this).find("pndID").text();
		var biogr = $(this).find("biograficData").text();
		var birth =$(this).find("yearOfBirth").text();
		
		resultField = resultField + "<div class=\"item\" ><a href=\"#\" class=\"item\" ><strong>Treffer</strong></a>" 
			+ "<ul><li><strong>PND-ID: </strong>" +  pndId +"</li>"
			+ "<li><strong>Name: </strong>" +  firstName + " " + lastName +"</li>"
			+ "<li><strong>Geburtsjahr: </strong>" + birth + "</li>"
			+ "<li><strong>Bibliographische Daten: </strong>" + biogr  + "</li>" 
			+ "<li><strong>DNB-Eintrag: </strong><a href=\"" + pndUri + "\" target=\"_blamk\" >"+ pndUri  + "</a></li>" 
			+ "</li></ul></div>";
	});
	return resultField;
}
