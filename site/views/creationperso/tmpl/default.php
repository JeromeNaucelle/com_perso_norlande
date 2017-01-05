<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

$js = <<<JS


	var dataMaitrise = null;
	var chartMaitrise = null;
	
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
		      	//alert(data['msg']);
		      } else if(data['result'] == 2) {
		      	// Question à l'utilisateur
		      	var test = {"Choix 1":"choix1", "Choix 2":"choix2"};
		      	$("#question_options").empty();
		      	for(var choix in test) {
		      		$("#question_options").append( '<input type="radio" name="radioOption" value="'+test[choix]+'"/> '+choix+' <br />' );
		      	}
		      	$.blockUI({ message: $('#question'), css: { width: '275px' } }); 
		      }
		      selectCompetencesAcquises(data['competences']);
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
    }
		
		/*
		var perso = new Object();
		function printPerso() {
				var textPerso = perso['nbCoups'] + " Coups<br>";
				textPerso += "+" + perso['forcePhysique'] + " force physique<br>";
				textPerso += "Rumeurs indiscr&egravetes : " + perso['rumeurs'] + '<br>';
				textPerso += "Actions de Guerre : " + perso['nbActionGuerre'] + '<br>';
				
				if (perso['accesTableConseil']) {
					textPerso +="Acc&egrave;s &agrave; la Table du Conseil<br>";
				}
				if (perso['accesTableGuerre']) {
					textPerso +="Acc&egrave;s &agrave; la Table de Guerre<br>";
				}
				
				var div = document.getElementById('affichage_perso');
				div.innerHTML = textPerso;
		}*/
		
      google.charts.load('current', {packages:["orgchart"]});
      google.charts.setOnLoadCallback(launch_ajax);

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
      
    $(document).ready(function() { 
 
        $('#test').click(function() { 
            
        }); 
 
 
        $('#alert_ok').click(function() { 
            $.unblockUI(); 
            return false; 
        }); 
        
        $('#question_ok').click(function() { 
        		var url = 'index.php?format=raw&option=com_perso_norlande&task=userChoiceDepenseXP';
            // update the block message 
            $.blockUI({ message: "<h1>Remote call in progress...</h1>" }); 
 
            $.ajax({ 
                url: 'wait.php', 
                cache: false, 
                complete: function() { 
                    // unblock when remote call returns 
                    $.unblockUI(); 
                } 
            }); 
        }); 
 
    }); 
JS;



// Add Javascript
$doc->addStyleSheet("components/com_perso_norlande/media/perso_norlande/css/style.css",'text/css',"screen");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery-3.1.1.min.js");
$doc->addScript("components/com_perso_norlande/media/perso_norlande/js/jquery.blockUI.js");
$doc->addScript("https://www.gstatic.com/charts/loader.js");
$doc->addScriptDeclaration($js, 'text/javascript');

echo "<input type='hidden' id='ajax_url' value='index.php?format=raw&option=com_perso_norlande&task=getArbreMaitrise&competence=".$this->competence."'>"; ?>


<h1>Test</h1>

  
<?php include(JPATH_COMPONENT . '/includes/menu.php'); ?>

<?php if($this->perso == NULL) {
	echo "Vous devez d&apos;abord cr&eacute;er ou s&eacute;lectionner un Personnage.";
	}
	else {
?>
	  		<ul id="submenu">
<?php
$menu_list = $this->list_maitrise;
for($i=0; $i < count($menu_list); $i++) 
{
	echo '<li><a href="index.php?option=com_perso_norlande&famille='.$this->famille.'&competence='.$menu_list[$i]['competence_id'].'">'.htmlentities($menu_list[$i]['competence_nom']).'</a>';
	echo "</li>";
}
?>	  		
		</ul>
	</div>
	<div id="affichage_perso"></div>
    <p id="chart_p"></p>
	</div>
	
	<div id="alert" style="display:none; cursor: default"> 
        <p id="alert_msg">Would you like to contine?.</p>
        <input type="button" id="alert_ok" value="OK" />
	</div> 
	
	<div id="question" style="display:none; cursor: default"> 
        <p id="question_msg">Would you like to contine?.</p>
        <div id="question_options"></div>
        <input type="button" id="question_ok" value="Valider" />
        <input type="button" id="question_cancel" value="Annuler" />
	</div> 

<?php 
}
?>
