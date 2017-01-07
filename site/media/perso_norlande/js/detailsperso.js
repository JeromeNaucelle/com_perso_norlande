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

function deleteEntrainement(competence_id){
	var nom_entrainement = $( "#row_entrainement_"+competence_id).text();
	$("#del_entrainement_id").val(competence_id);
	$("#question_msg").text("Voulez-vous supprimer l'entrainement du "+nom_entrainement +" ?");
	$.blockUI({ message: $('#question'), css: { width: '275px' } }); 
	
}


 $(document).ready(function() {
 
  $('#question_cancel').click(function() { 
      $.unblockUI(); 
      return false; 
  }); 
  
  $('#question_ok').click(function() { 
  		var competence_id = $("#del_entrainement_id").val();
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
  }); 
 
}); 