function add_entrainement(competence_id){
	var url = "index.php?option=com_perso_norlande&task=addEntrainement&competence_id="+competence_id;
	$.ajax(
 	{
     // Post select to url.
     type : 'get',
     url : url,
     dataType : 'json', // expected returned data format.
     success : function(data)
     {
     		$( "#row_empty").remove();
     		$( "#tbl_entrainements" ).append( '<tr id="row_entrainement_'+competence_id+'"><td>'+data[competence_id]+'</td>' );
			$( "#row_entrainement_"+competence_id ).append( '<td><input type="button" id="entrainement_'+competence_id+'" name="button_submit" value="Supprimer" onclick="deleteEntrainement('+competence_id+')"/></td>' );
     },
     complete : function(data)
     {
         // do something, not critical.
     }
	});
}

function updatePointsCreationPerso() {
	var url = 'index.php?format=raw&option=com_perso_norlande&task=updatePointsCreationPerso';
	$.ajax(
 	{
     // Post select to url.
     type : 'post',
     url : url,
     data: $('#formPointsCreation').serialize(),
    // contentType: "application/json",
     dataType : 'json', // expected returned data format.
     success : function(data)
     {
      	// Affichage d'un message d'info
      	$( "#alert_msg" ).text( data['msg'] );
      	$('#pointsCreation').val(data['pointsCreation']);
      	$('#content').block({ message: $('#alert'), css: { width: '275px' } });
      	location.hash = "#alert";
	      
     }
 });
}

function updateAnciennete() {
	var url = 'index.php?format=raw&option=com_perso_norlande&task=updateAnciennete';
	$.ajax(
 	{
     // Post select to url.
     type : 'post',
     url : url,
     data: $('#formAnciennete').serialize(),
    // contentType: "application/json",
     dataType : 'json', // expected returned data format.
     success : function(data)
     {
      	// Affichage d'un message d'info
      	$( "#alert_msg" ).text( data['msg'] );
      	$('#anciennete').val(data['anciennete']);
      	$('#content').block({ message: $('#alert'), css: { width: '275px' } });
	      location.hash = "#alert";
     }
 });
}

function updateMonnaie() {
	var url = 'index.php?format=raw&option=com_perso_norlande&task=updateMonnaie';
	$.ajax(
 	{
     // Post select to url.
     type : 'post',
     url : url,
     data: $('#formMonnaie').serialize(),
    // contentType: "application/json",
     dataType : 'json', // expected returned data format.
     success : function(data)
     {
      	// Affichage d'un message d'info
      	$( "#alert_msg" ).text( data['msg'] );
      	$('#content').block({ message: $('#alert'), css: { width: '275px' } });
	      location.hash = "#alert";
     }
 });
}


function updateArmure() {
	var url = 'index.php?format=raw&option=com_perso_norlande&task=updateArmure';
	$.ajax(
 	{
     // Post select to url.s
     type : 'post',
     url : url,
     data: $('#formArmure').serialize(),
    // contentType: "application/json",
     dataType : 'json', // expected returned data format.
     success : function(data)
     {
      	// Affichage d'un message d'info
      	$( "#alert_msg" ).text( data['msg'] );
      	$('#content').block({ message: $('#alert'), css: { width: '275px' } });
	      location.hash = "#alert";
     }
 });
}

function postDeleteEntrainement(competence_id) {
		var url = "index.php?option=com_perso_norlande&task=deleteEntrainement&competence_id="+competence_id;
		$.ajax(
    	{
        // Post select to url.
        type : 'get',
        url : url,
        dataType : 'json', // expected returned data format.
        success : function(data)
        {
        		$( "#row_entrainement_"+competence_id).remove();
        		var rows = $( "#tbl_entrainements" ).find("tr");
        		if(rows.length == 0) {
        			$( "#tbl_entrainements" ).append( '<tr id="row_empty"><td>Aucun entrainement</td>' );
        		}
        },
        complete : function(data)
        {
            $('#content').unblock(); 
        }
    });
}

function deleteEntrainement(competence_id){
	var nom_entrainement = $( "#row_entrainement_"+competence_id).text();
	$("#question_msg").text("Voulez-vous supprimer l'entrainement du "+nom_entrainement +" ?");
	$('#question_cancel').click(function() { 
			$('#content').unblock();
			});
	$('#question_ok').click(function() { 
			postDeleteEntrainement(competence_id);
			});
	$('#content').block({ message: $('#question'), css: { width: '275px' } }); 
	location.hash = "#question";
}


 $(document).ready(function() {
 
  $('#alert_ok').click(function() { 
      $('#content').unblock(); 
      return false; 
  }); 
 
}); 


function validationPersoUser(){
	$("#question_msg").text("Attention : après validation seul un orga pourra modifier votre fiche ! Voulez-vous vraiment valider ?");
	var tmp = $('#question_ok').attr('value');	
		
	$('#question_ok').attr('value',"Oui");	
	$('#question_cancel').click(function() { 
			$('#content').unblock();
			$('#question_ok').attr('value',tmp);
				
			});
	$('#question_ok').click(function() { 
			document.location.href = "index.php?option=com_perso_norlande&task=validationUser";
			$('#question_ok').attr('value',tmp);
			
			});
	$('#content').block({ message: $('#question'), css: { width: '275px' } }); 
	location.hash = "#question";
}


function delete_perso(){
	$("#question_msg").text("Attention : Ce personnage sera définitivement supprimer ! Voulez-vous vraiment valider ?");
	var tmp = $('#question_ok').attr('value');	
		
	$('#question_ok').attr('value',"Oui");	
	$('#question_cancel').click(function() { 
			$('#content').unblock();
			$('#question_ok').attr('value',tmp);
				
			});
			
	$('#question_ok').click(function() { 
			document.location.href = "index.php?option=com_perso_norlande&task=deletePerso";
			});
	$('#content').block({ message: $('#question'), css: { width: '275px' } }); 
	location.hash = "#question";
	
}

function askForPersoAssociation() {
	var url = 'index.php?format=raw&option=com_perso_norlande&task=askForPersoAssociation';
	$.ajax(
 	{
     // Post select to url.
     type : 'post',
     url : url,
     data: $('#form_ask_perso_association').serialize(),
    // contentType: "application/json",
     dataType : 'json', // expected returned data format.
     error : function(xhr, ajaxOptions, thrownError)
     {
      	alert(xhr.responseJSON.Message);
     },
     success : function(data)
     {
      	// Affichage d'un message d'info
      	$( "#alert_msg" ).text( data['msg'] );
      	$('#content').block({ message: $('#alert'), css: { width: '275px' } });
      	location.hash = "#alert";
     }
 });
}


 $(document).ready(function() {
 
  $('#alert_ok').click(function() { 
      $('#content').unblock(); 
      return false; 
  }); 
 
}); 


function open_infos()
{
	window.open('index.php?option=com_perso_norlande&view=voirfiche&tmpl=component','Fiche Perso','menubar=no, scrollbars=yes, top=100, left=100, width=800, height=600');
}

function download_fiche()
{
	window.open('index.php?option=com_perso_norlande&view=voirfiche&layout=exportodt','Fiche Perso','menubar=no, scrollbars=yes, top=100, left=100, width=800, height=600');
}