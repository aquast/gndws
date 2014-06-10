/* - pndId.js - */
/*
 * enables GND Triplestore requests for pndIdent within creator Input-fields 
 *
 * Requires jQuery (http://jquery.com/) 
 * author Andres Quast
 */

$(document).ready(addForms);

var pRow;

function addForms(){
	addPndForm();
	addOrcidForm();
}

function addPndForm(){
	var suchImage = "<div class=\"pnd\" style=\"float:left;\"><img src=\"../image/search_icon.jpg\" alt=\"PND nach Person durchsuchen\" /></div>";
	var imageDiv = $(".PNDIdentNumber").parent().append(suchImage);
	//$(".lastName").blur(requestPersonIdBlur);
	$("div .pnd img").click(requestPersonId);
	$("body").append("<div class=\"result\"><h4>Ergebnis der PND ID-Abfrage</h4></div>");
	$(".result").hide();

};

function addOrcidForm(){
	var suchImage = "<div class=\"orcid\" style=\"float:left;\"><img src=\"../image/search_icon.jpg\" alt=\"Orcid nach Person durchsuchen\" /></div>";
	var imageDiv = $(".OrcidIdentNumber").parent().append(suchImage);
	//$(".lastName").blur(requestPersonIdBlur);
	$("div .orcid img").click(requestPersonId);
	$("body").append("<div class=\"result\"><h4>Ergebnis der ORCID ID-Abfrage</h4></div>");
	$(".result").hide();

};

function requestPersonId(){
    $(".resultalert").remove();	
    $(".result").hide();
    $(".item").remove();
	var firstName = $(this).parent().parent().parent().find(".firstName").val();
	var lastName = $(this).parent().parent().parent().find(".lastName").val();
	
	pRow = $(this).parent().parent().parent();
	if(personFormCheck(firstName, lastName) && $(this).parent().is(".pnd")){
		requestGndService(firstName, lastName); 	
		$(this).parent().parent().find(".pnd").append("<div class=\"resultalert\"></div>");
		//$(this).parent().parent().find("img").remove();
    	}
	if(personFormCheck(firstName, lastName) && $(this).parent().is(".orcid")){
		requestOrcidService(firstName, lastName); 	
		//requestGndService(firstName, lastName); 	
		$(this).parent().parent().find(".orcid").append("<div class=\"resultalert\"></div>");
		//$(this).parent().parent().find("img").remove();
    	}
};

/*function requestService(firstName, lastName, clID){
	if(clID == ".pnd"){
		requestGndService(firstName, lastName);
	}
	if(clID == ".orcid"){
		requestOrcidService(firstName, lastName);
	}
} */

function requestPersonIdBlur(){
    $(".resultalert").remove();	
    $(".result").hide();
    $(".item").remove();

    //$(this).parent().parent().find(".lastName").unbind("blur");
	
	var firstName = $(this).parent().parent().find(".firstName").val();
	var lastName = $(this).parent().parent().find(".lastName").val();

	pRow = $(this).parent().parent();
	if(personFormCheck(firstName, lastName)){
		requestGndService(firstName, lastName); 	
		$(this).parent().parent().find(divClassId).append("<div class=\"resultalert\"></div>");
		//$(this).parent().parent().find("img").remove();
    	}
};

function personFormCheck(firstName, lastName){
    if(lastName == null || lastName == "" || firstName == null || firstName == ""){
        alert('Füllen Sie bitte zunächst Nach- und Vornamenfelder aus');
    return false;
    }
    return true;
}

function requestGndService(firstName, lastName){
	//return $("<p>" + lastName + ", " + firstName + "</p>");
	var requestUrl = "http://nyx.hbz-nrw.de:8080/loddiggr/api/personInfo?firstName=" 
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
					var preferredLastName = $(this).parent().parent().find("#lname").text();
					var preferredFirstName = $(this).parent().parent().find("#fname").text();
					//var firstName = $(this).parent().parent().find("ul li:first-child").text();
					pRow.find("input.PNDIdentNumber").val(pnd);
					pRow.find("input.lastName").val(preferredLastName);
					pRow.find("input.firstName").val(preferredFirstName);
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
    return jqxhr.responseText;
            	

}

function requestOrcidService(firstName, lastName){
	//return $("<p>" + lastName + ", " + firstName + "</p>");
	var requestUrl = "http://pub.orcid.org/search/orcid-bio?q=" 
	+ "given-names:" + firstName + "+AND+" + "family-name:" + lastName;
	var options = {
			
			type: 'GET',
			url: requestUrl,
			dataType: 'xml',
            	}
            
     var testText;
     var jqxhr = jQuery.ajax(options)
			.done(function(){
				//$("div.resultalert").append("Works");
				var count = $(jqxhr.responseText).find("orcid-search-results").attr("num-found");
				if (count >= 0){
					$("div.resultalert").append(count);					
				}else{
					$("div.resultalert").append("0");
					
				}
				
				$("div.resultalert").slideDown("slow");
				$("div.resultalert").click(function(){
					$(".result").show();
					});
				$("div.result").append(responseParserOrcid(jqxhr.responseText));
				$("a.item strong").click(function(){
					$(this).parent().parent().find("ul li strong").remove();
					var orcid = $(this).parent().parent().find("ul li:first-child").text();
					//var firstName = $(this).parent().parent().find("ul li:first-child").text();
					pRow.find("input.OrcidIdentNumber").val(orcid);
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
    return jqxhr.responseText;
            	

}

function responseParser(xml){
	var resultField = "";
	var pndResult = $(xml).find("PersonResultList");
	pndResult.each(function(){
		var preferredName = $(this).find("preferredName").text();
		var preferredFirstName = $(this).find("preferredFirstName").text();
		var preferredLastName = $(this).find("preferredLastName").text();
		var pndUri =$(this).find("perIdentUri").text();
		var pndId = $(this).find("persIdent").text();
		var biogr = $(this).find("biogr").text();
		var birth =$(this).find("birth").text();
		
		resultField = resultField + "<div class=\"item\" ><a href=\"#\" class=\"item\" ><strong><span id=\"lname\">" + preferredLastName + "</span>, <span id=\"fname\">" + preferredFirstName + "</span></strong></a>" 
			+ "<ul><li><strong>PND-ID: </strong>" +  pndId + "</li>"
			+ "<li><strong>Geburtsjahr: </strong>" + birth + "</li>"
			+ "<li><strong>Bibliographische Daten: </strong>" + biogr  + "</li></ul></div>";
	});
	return resultField;
}

function responseParserOrcid(xml){
	var resultField = "";
	var pndResult = $(xml).find("orcid-search-result");
	pndResult.each(function(){
		var prefferedName = $(this).find("given-names").text() + " " + $(this).find("family-name").text();
		var orcidUri =$(this).find("orcid-id").text();
		var orcidId = $(this).find("orcid").text();
		var biogr = $(this).find("biography").text();
		
		resultField = resultField + "<div class=\"item\" ><a href=\"#\" class=\"item\" ><strong>" + prefferedName + "</strong></a>" 
			+ "<ul><li><strong>ORCID-ID: </strong>" +  orcidId + "</li>"
			+ "<li><strong>Bibliographische Daten: </strong>" + biogr  + "</li></ul></div>";
	});
	return resultField;
	
	
	
}
