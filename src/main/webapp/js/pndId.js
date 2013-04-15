/* - pndId.js - */
/*
 * enables GND Triplestore requests for pndIdent within creator Input-fields 
 *
 * Requires jQuery (http://jquery.com/) 
 * author Andres Quast
 */

$(document).ready(addPndForm);


function addPndForm(){
	var suchImage = '<img src="../image/search_icon.jpg" alt="PND nach Person durchsuchen""/>';
	$(".PNDIdentNumber").parent().append(suchImage).trigger("click", requestPersonId());
};

function requestPersonId(){
    //var lastName = $(this + " input.lastName");
    //var firstName = $(this + " input.firstName");
    alert("bin hier");
    //if(pndFormCheck($(this.parent()))){
    //PersonData = window.open("https://alkyoneus.hbz-nrw.de/gnd/gndrequest.jsp?firstName=" + firstName + "&lastName=" + lastName + "&index=" + number, "Person Data", "height=800,width=600,scrollbars=yes");
    //PersonData.focus();
    //}
};

/*function pndFormCheck(number){
    var lastName = $('input.lastName');
    var firstName = $('input.firstName');
    if(lastName.value == null || lastName.value == "" || firstName.value == null || firstName.value == ""){
        alert('Füllen Sie bitte zunächst Nach- und Vornamenfelder aus');
    return false;
    }
    return true;

}*/
