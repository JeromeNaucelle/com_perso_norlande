var dataMaitrise = null;
var chartMaitrise = null;
var competences_acquises = null;
var competenceIdOver = -1;

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
     dataType : 'html', // expected returned data format.
     success : function(data)
     {
     		$('#question_dep_xp').empty();
			$('#question_dep_xp').append(data);
			$('#content').block({ 
	      		message: $('#question_dep_xp'),
					css: { position: 'absolute', 
								textAlign: 'left', 
								heigth:'40em', 
								width:'300px', 
								overflow: 'auto!important' }
				}); 
	      
     },
     complete : function(data)
     {
         // do something, not critical.
     }
 });
}


function forgetCompetence(competence_id){
	var url = 'index.php?format=raw&option=com_perso_norlande&task=forgetCompetence';
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
     		$( "#alert_msg" ).text( data['msg'] );
	      $('#content').block({ message: $('#alert'), css: { width: '275px' } }); 
	      if(data['error'] != 0) {
	      	selectCompetencesAcquises(competences_acquises);
	      } else {
	      	selectCompetencesAcquises(data['competences']);
	      } 
	      
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
	google.visualization.events.addListener(chartMaitrise, 'onmouseover', mouseOverNode);
  
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
	
	
	/* créé parce que lorsque l'utilisateur clique sur une case déjà
	sélectionné, ça la dé-sélectionne et on ne peut pas récupérer l'identifiant
	de la case choisie. Donc on sauvegarde l'identifiant sur onMouseOver */
	function mouseOverNode(param) {
		competenceIdOver = dataMaitrise.getValue(param.row, 0);
	}
  
  
	function selectHandler() {
		var selection = chartMaitrise.getSelection();
		if(selection.length != 0) {
			var competence_id = dataMaitrise.getValue(selection[0].row, 0);
			user_selection(competence_id);
		} else {
			// l'utilisateur a cliqué sur une compétence déjà sélectionnée
			$("#question_ok").click(function(){ 
										forgetCompetence(competenceIdOver);
										});
										
			$("#question_cancel").click(function(){ 
											selectCompetencesAcquises(competences_acquises);
											$('#content').unblock(); 
											});
	      $('#content').block({ message: $('#question'), css: { width: '275px' } }); 
			
		}
	}
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
         $('#content').unblock();
     }
 });
}

function cancelDepenseCristaux() {
	selectCompetencesAcquises(competences_acquises);
	$('#content').unblock(); 
	return false; 
}

   
$(document).ready(function() {
	$('#alert_ok').click(function() { 
		$('#content').unblock(); 
		return false; 
	}); 
 
}); 
 
google.charts.load('current', {packages:["orgchart"]});
google.charts.setOnLoadCallback(launch_ajax);
