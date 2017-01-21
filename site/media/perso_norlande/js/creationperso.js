var dataMaitrise = null;
var chartMaitrise = null;
var competences_acquises = null;

function launch_ajax(){
	var url = document.getElementById('ajax_url').value;
	$.ajax(
 	{
     // Post select to url.
     type : 'get',
     url : url,
     dataType : 'json', // expected returned data format.
     success : function(data)
     {
     		drawChart(data['arbre']);
     		selectCompetencesAcquises(data['competences_acquises']);
     },
     complete : function(data)
     {
         // do something, not critical.
     }
 });
}
 
 function user_selection(competence_id){
	var url = 'index.php?format=raw&option=com_perso_norlande&task=userSelect';
	//var data_post = {"competence":competence_id};
	$.ajax(
 	{
     // Post select to url.
     type : 'post',
     url : url,
     data: {"competence":competence_id},
    // contentType: "application/json",
     dataType : 'json', // expected returned data format.
     success : function(data)
     {
     		
	      if(data['result'] == 4 || data['result'] == -1) {
	      	// Affichage d'un message d'info (erreur)
	      	$( "#alert_msg" ).text( data['msg'] );
	      	$.blockUI({ message: $('#alert'), css: { width: '275px' } }); 
	      	selectCompetencesAcquises(competences_acquises);
	      } else if(data['result'] == 2) {
	      	// Question à l'utilisateur
	      	//var xp = {niveau_competence:3,cristaux:{incolore:2, occultisme:3}, entrainement:{12:"Maitre des poisons", 20:"Maitre des anges"}};
	      	questionDepenseXp(data['xp'], data['niveauCompetence']);
	      	$.blockUI({ message: $('#question_dep_xp'), onUnblock: resetQuestionXp(),css: { position: 'absolute', textAlign: 'left', heigth:'40em', width:'300px', overflow: 'auto!important' }}); 
	      }
	      
     },
     complete : function(data)
     {
         // do something, not critical.
     }
 });
}
 
function selectCompetencesAcquises(competences)
{
	var arraySelection = [];
	for(var i=0; i<competences.length; i++) {
		node_id = dataMaitrise.getFilteredRows([{'column': 0, 'value': competences[i].toString()}]);
		arraySelection.push({'row': node_id[0]});
	}
	chartMaitrise.setSelection(arraySelection);
	competences_acquises = competences;
}
	
function drawChart(arbre_maitrise_json) {
	dataMaitrise = new google.visualization.DataTable();
	dataMaitrise.addColumn('string', 'Maitrise');
	dataMaitrise.addColumn('string', 'Maitrise requise');
	
	// For each orgchart box, provide the name, manager, and tooltip to show.
	dataMaitrise.addRows(arbre_maitrise_json);
	  
	  
	// Create the chart.
	chartMaitrise = new google.visualization.OrgChart(document.getElementById('chart_p'));
	chartMaitrise.draw(dataMaitrise, {allowHtml:true, nodeClass:'myNodeClass'});
	  
	google.visualization.events.addListener(chartMaitrise, 'select', selectHandler);
  
	function selectParents(row_id) {
		var arraySelection = [{row: row_id}];
		while(row_id != 0) {
			var parentValue = dataMaitrise.getValue(row_id, 1);
			var potentialParents = dataMaitrise.getFilteredRows([{column: 0, value: parentValue}]);
			arraySelection.push({row: potentialParents[0]});
			chartMaitrise.setSelection(arraySelection);
			row_id = potentialParents[0];
		}
	}
  
  
	function selectHandler() {
		var selection = chartMaitrise.getSelection();
		var competence_id = dataMaitrise.getValue(selection[0].row, 0);
		user_selection(competence_id);
		//selectParents(selection[0].row);
	}
}


function resetQuestionXp() {
	var initialDiv = $(document).data("initialDepXpClone");
	$("#question_dep_xp").replaceWith(initialDiv);
	$(document).data("initialDepXpClone", initialDiv.clone(true));
}

function isNormalInteger(str) {
    var n = Math.floor(Number(str));
    return String(n) === str && n >= 0;
}

function checkNbCristaux() {
	var nbNeeded = $("#niveauCompetence").val();
	var el = $("#depense_cristaux").find(":text");
	var nbCristauxUsed = 0;
	for (var i=0; i < el.length; i++) {
		var item = $(":text")[i];
		if (isNormalInteger(el[i].value)) {
			nbCristauxUsed += parseInt(el[i].value);
			$("#depense_cristaux").find(item).css( "background-color", "" );
		} else {
			$("#depense_cristaux").find(item).css( "background-color", "red" );
		}
	}
	if (nbCristauxUsed == nbNeeded) {
		postChoixDepenseXP('depense_cristaux');
	} else {
		alert("Vous devez utiliser exactement "+nbNeeded+ " cristaux");
	}
}

function checkNbPointsCreation() {
	var nbNeeded = $("#niveauCompetence").val();
	var el = $("#depense_points_creation").find(":text");
	var nbPcUsed = 0;
	var item = $(":text")[0];
	if (isNormalInteger(el[0].value)) {
		nbPcUsed += parseInt(el[0].value);
		$("#depense_cristaux").find(item).css( "background-color", "" );
	} else {
		$("#depense_cristaux").find(item).css( "background-color", "red" );
	}
	if (nbPcUsed == nbNeeded) {
		postChoixDepenseXP('depense_points_creation');
	} else {
		alert("Vous devez utiliser exactement "+nbNeeded+ " points");
	}
}

function postChoixDepenseXP(form) {
	var url = 'index.php?format=raw&option=com_perso_norlande&task=userChoiceDepenseXP';
	$.ajax(
 	{
     // Post select to url.
     type : 'post',
     url : url,
     data: $('form#'+form).serialize(),
    // contentType: "application/json",
     dataType : 'json', // expected returned data format.
     success : function(data)
     {
			if(data["error"] == 0) {
				selectCompetencesAcquises(data['competences']);
			} else {
				selectCompetencesAcquises(competences_acquises);
				alert(data['msg']);
			} 
			
     },
     complete : function(data)
     {
         // do something, not critical.
         $.unblockUI();
     }
 });
}

function cancelDepenseCristaux() {
	selectCompetencesAcquises(competences_acquises);
	$.unblockUI(); 
	return false; 
}

function questionDepenseXp(xp, competenceLevel) {
	//var xp = {niveau_competence:3,cristaux:{incolore:2, occultisme:3}, entrainement:{12:"Maitre des poisons", 20:"Maitre des anges"}};
	//var xp = {entrainement:{12:"Maitre des poisons", 20:"Maitre des anges"}};
	//var xp = {cristaux:{incolore:2, occultisme:3}};
	
	if (xp.points_creation) {
		$("#depense_points_creation").show();
		
		// TODO : faire l'affectation avec JQuery
		document.getElementById("niveauCompetence").value = competenceLevel;
		$("#submit_points_creation").before("<p>Dépenser "+competenceLevel+" points de création parmi vos points suivants :</p>");
		$("#submit_points_creation").before('<label for="dep_points_creation">Points de création : </label><input type="text" name="dep_points_creation" value="0" class="shortNb"> / '+xp.points_creation+'<br>');
		return;
	}
	
	if (xp.cristaux) {
		$("#depense_cristaux").show();
		document.getElementById("niveauCompetence").value = competenceLevel;
		$("#submit_cristaux").before("<p>Dépenser "+competenceLevel+" cristaux parmi les cristaux suivants :</p>");
		for (var type in xp.cristaux) {
			$("#submit_cristaux").before('<label for="dep_cristaux_'+type+'">'+type+' : </label><input type="text" name="dep_cristaux_'+type+'" value="0" class="shortNb"> / '+xp.cristaux[type]+'<br>');
		}
	}
	
	if (xp.cristaux && xp.entrainement) {
		$("#depense_entrainement").before('<p style="text-align:center"><b>ou</b></p>');
	}
	if (xp.entrainement) {
		$("#depense_entrainement").show();
		for (var competenceId in xp.entrainement) {
			$("#submit_entrainement").before('<input type="radio" name="dep_entrainement_group" value="'+competenceId+'">'+xp.entrainement[competenceId]+'<br>');
		}
	}		
}
   
$(document).ready(function() {
 
 	var initialDiv = $("#question_dep_xp").clone(true);
	$(document).data("initialDepXpClone", initialDiv);
	$('#alert_ok').click(function() { 
		$.unblockUI(); 
		return false; 
	}); 
 
}); 
 
google.charts.load('current', {packages:["orgchart"]});
google.charts.setOnLoadCallback(launch_ajax);
