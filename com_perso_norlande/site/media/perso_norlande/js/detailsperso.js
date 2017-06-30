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
      	$.blockUI({ message: $('#alert'), css: { width: '275px' } });
	      
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
      	$.blockUI({ message: $('#alert'), css: { width: '275px' } });
	      
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
      	$.blockUI({ message: $('#alert'), css: { width: '275px' } });
	      
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
      	$.blockUI({ message: $('#alert'), css: { width: '275px' } });
	      
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
            $.unblockUI(); 
        }
    });
}

function deleteEntrainement(competence_id){
	var nom_entrainement = $( "#row_entrainement_"+competence_id).text();
	$("#question_msg").text("Voulez-vous supprimer l'entrainement du "+nom_entrainement +" ?");
	$('#question_cancel').click(function() { 
			$.unblockUI();
			});
	$('#question_ok').click(function() { 
			postDeleteEntrainement(competence_id);
			});
	$.blockUI({ message: $('#question'), css: { width: '275px' } }); 
	
}


 $(document).ready(function() {
 
  $('#alert_ok').click(function() { 
      $.unblockUI(); 
      return false; 
  }); 
 
}); 


function validationPersoUser(){
	$("#question_msg").text("Attention : après validation seul un orga pourra modifier votre fiche ! Voulez-vous vraiment valider ?");
	var tmp = $('#question_ok').attr('value');	
		
	$('#question_ok').attr('value',"Oui");	
	$('#question_cancel').click(function() { 
			$.unblockUI();
			$('#question_ok').attr('value',tmp);
				
			});
	$('#question_ok').click(function() { 
			document.location.href = "index.php?option=com_perso_norlande&task=validationUser";
			$('#question_ok').attr('value',tmp);
			
			});
	$.blockUI({ message: $('#question'), css: { width: '275px' } }); 
	
}


function delete_perso(){
	$("#question_msg").text("Attention : Ce personnage sera définitivement supprimer ! Voulez-vous vraiment valider ?");
	var tmp = $('#question_ok').attr('value');	
		
	$('#question_ok').attr('value',"Oui");	
	$('#question_cancel').click(function() { 
			$.unblockUI();
			$('#question_ok').attr('value',tmp);
				
			});
			
	$('#question_ok').click(function() { 
			document.location.href = "index.php?option=com_perso_norlande&task=deletePerso";
			});
	$.blockUI({ message: $('#question'), css: { width: '275px' } }); 
	
}


 $(document).ready(function() {
 
  $('#alert_ok').click(function() { 
      $.unblockUI(); 
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