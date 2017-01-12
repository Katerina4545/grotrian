var p="";
						
$.fn.dataTableExt.afnFiltering.push(
				function( oSettings, aData, iDataIndex ) {		
				
					var iMin = document.getElementById('min_3').value * 1;
					var iMax = document.getElementById('max_3').value * 1;
					
					var iVersion = aData[4] == "-" ? 0 : aData[4]*1;								
			
					var config = $("select#configurationSelect").val();
					var term = $("select#termSelect").val();
					var jvalue = $("select#jvalueSelect").val();
					
					if (aData[1]!=config && config!=''){
						return false;
					} else
					if (aData[2]!=term && term!=''){
						return false;
					} else
					if (aData[3]!=jvalue && jvalue!=''){
						return false;
					} else
					if ( iMin == "" && iMax == "" )
					{
						return true;
					}
					else if ( iMin== "" && iVersion < iMax )
					{
						return true;
					}
					else if ( iMin < iVersion && "" == iMax )
					{
						return true;
					}
					else if ( iMin < iVersion && iVersion < iMax )
					{
						return true;
					}
					
					else if ( iMin == iVersion && iVersion == iMax )
					{
						return true;
					}
					
					return false;
					
					}
			);	

$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
				// check that we have a column id
				if ( typeof iColumn == "undefined" ) return new Array();
	
				// by default we only wany unique data
				if ( typeof bUnique == "undefined" ) bUnique = true;
	
				// by default we do want to only look at filtered data
				if ( typeof bFiltered == "undefined" ) bFiltered = true;
	
				// by default we do not wany to include empty values
				if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
	
				// list of rows which we're going to loop through
				var aiRows;
	
				// use only filtered rows
				if (bFiltered == true) aiRows = oSettings.aiDisplay; 
				// use all rows
				else aiRows = oSettings.aiDisplayMaster; // all row numbers

				// set up data array	
				var asResultData = new Array();
	
				for (var i=0,c=aiRows.length; i<c; i++) {
					iRow = aiRows[i];
					var aData = this.fnGetData(iRow);
					var sValue = aData[iColumn];
		
					// ignore empty values?
					if (bIgnoreEmpty == true && sValue.length == 0) continue;

					// ignore unique values?
					else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
		
					// else push the value onto the result data array
					else asResultData.push(sValue);
				}			
				
				return asResultData.sort();
			};			

			$(document).ready(function() {	
				//��������� ���� ��� ���������� ������� ����������
				$("#configurationSelect").empty();
				$("#configurationSelect").append( $('<option value="">Any</option>'));
				$("#configurationSelect :last").attr("selected", "selected");
				$("#termSelect").empty();
				$("#termSelect").append( $('<option value="">Any</option>'));
				$("#termSelect :last").attr("selected", "selected");
				$("#jvalueSelect").empty();
				$("#jvalueSelect").append( $('<option value="">Any</option>'));		
				$("#jvalueSelect :last").attr("selected", "selected");
			
				$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback ){					
				
				    if ( typeof sNewSource != 'undefined' && sNewSource != null ){
				        oSettings.sAjaxSource = sNewSource;
					}
				
				    this.oApi._fnProcessingDisplay( oSettings, true );
				    var that = this;
     
					oSettings.fnServerData( oSettings.sAjaxSource, null, function(json) {
					/* Clear the old information from the table */
					that.oApi._fnClearTable( oSettings );        
			        /* Got the data - add it to the table */
			        for ( var i=0 ; i<json.aaData.length ; i++ ){
			            that.oApi._fnAddData( oSettings, json.aaData[i] );
			        }       
			
			        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
			        that.fnDraw( that );
			        that.oApi._fnProcessingDisplay( oSettings, false );
         
				    /* Callback user function - for event handlers etc */
			        if ( typeof fnCallback == 'function' ){
			            fnCallback( oSettings );
			        }
			    });
			}				
			
					

	if(locale=="en") dataTableslib={
		"sLengthMenu": "Show _MENU_ records per page",
		"sZeroRecords": "No entries",
		"sInfo": "Entries from _START_ to _END_ of _TOTAL_",
		"sInfoEmtpy": "Entries from 0 to 0 of 0",
		"sInfoFiltered": "(Filtred from _MAX_ entries)",
		"oPaginate": {
			"sFirst": "&lt;&lt;",
			"sPrevious": "&lt;",
			"sNext": "&gt;",
			"sLast": "&gt;&gt;"
		}
	};
	
	if(locale=="ru") dataTableslib={
		"sLengthMenu": "�������� _MENU_ ������� �� ��������",
		"sZeroRecords": "������ �����������",
		"sInfo": "������ � _START_ �� _END_ �� _TOTAL_ �������",
		"sInfoEmtpy": "������ � 0 �� 0 �� 0 �������",
		"sInfoFiltered": "(������������� �� _MAX_ �������)",
		"oPaginate": {
			"sFirst": "&lt;&lt;",
			"sPrevious": "&lt;",
			"sNext": "&gt;",
			"sLast": "&gt;&gt;"
		}
	};
				
	var oTable = $('#table1').dataTable({
		"aaSorting": [[ 4, "asc" ]],
		"sDom": 'l<"toolbar">rtip',	
		"oLanguage": dataTableslib,
		"aoColumns": 
		[	{ },			
			{ 
				"fnRender": function ( oObj ) {
					return oObj.aData[1].replace(/<sub>/gi,"~{").replace(/<\/sub>/gi,"}").replace(/@\{([^\}\{]*)\}/gi,"<sup>$1</sup>").replace(/~\{([^\}\{]*)\}/gi,"<sub>$1</sub>").replace(/#/gi,"<sup>0</sup>");
				} 
			},						
			{ 
				"fnRender": function ( oObj ) {
					return oObj.aData[2].replace(/<sup><pr>0<\/sup>/gi,"").replace(/<sup><suf>1<\/sup>/gi,"").replace(/[^0-9a-z<>,?\\\/\(\)\[\]\{\}\@\~]/gi,"").replace(/@\{([^\}\{]*)\}/gi,"<sup>$1</sup>").replace(/~\{([^\}\{]*)\}/gi,"<sub>$1</sub>");
				} 
			},						
			{ 
				"fnRender": function ( oObj ) {
					return oObj.aData[3].replace(",",".");
				}
			},
									
			{ "sType": "numeric" },	
									
			{ "sType": "numeric" },
			
			{ "sType": "html" },

			{ "sType": "html" }
		], 
		
		"iDisplayLength": 25,
		"bLengthChange": true,
		"bFilter": true,
		"bProcessing": true,
		"sPaginationType": "full_numbers",					
		"fnInitComplete": function() {
		 // Make custom toolbar
			$("div.toolbar").html('<input class="button white" id="saveLevels" value="'+SaveLevels+'" type="button"><input class="button white" id="deleteLevels" value="'+DeleteLevels+'" type="button"><input class="button white" id="createLevel" value="'+CreateLevel+'" type="button">');
		}			
	});		
	

	function fillSelectFields(){										
					//���������� ������� ��������� �������
					var config = $("select#configurationSelect").val();
					var term = $("select#termSelect").val();
					var jvalue = $("select#jvalueSelect").val();					
					
					//��������� ���� ��� ���������� ������� ����������
					$("#configurationSelect").empty();
					$("#configurationSelect").append( $('<option value="">Any</option>'));
					//$("#configurationSelect :last").attr("selected", "selected");
					$("#termSelect").empty();
					$("#termSelect").append( $('<option value="">Any</option>'));
					//$("#termSelect :last").attr("selected", "selected");
					$("#jvalueSelect").empty();
					$("#jvalueSelect").append( $('<option value="">Any</option>'));		
					//$("#jvalueSelect :last").attr("selected", "selected");
				
					// ���������� ������� � �������, ������������ � ������� � ������ ������. ������������� ���������� ������� - � ���������, ���������� ������� ������ �����
					var configurationsList = oTable.fnGetColumnData(1,true,true,true);
					var termsList = oTable.fnGetColumnData(2,true,true,true);
					var jvalueList = oTable.fnGetColumnData(3,true,true,true);
					
					// ��������� select-���� ����� �� ��������
					for (var i=0; i<configurationsList.length;i++){
						$("#configurationSelect").append( $('<option value="'+configurationsList[i]+'">'+configurationsList[i].replace(/<[^\/>]*>/gi,"[").replace(/<\/[^>]*>/gi,"]")+'</option>'));
						if (configurationsList[i] == config){
							$("#configurationSelect :last").attr("selected", "selected");
						}
					}
					for (var i=0; i<termsList.length;i++){
						$("#termSelect").append( $('<option value="'+termsList[i]+'">'+termsList[i].replace(/<[\/]*[^\/>]*span>/gi,"").replace(/<[^\/>]*>/gi,"[").replace(/<\/[^>]*>/gi,"]")+'</option>'));
						if (termsList[i] == term){
							$("#termSelect :last").attr("selected", "selected");
						}
					}
					for (var i=0; i<jvalueList.length;i++){
						$("#jvalueSelect").append( $('<option value="'+jvalueList[i]+'">'+jvalueList[i].replace(/<[^\/>]*>/gi,"[").replace(/<\/[^>]*>/gi,"]")+'</option>'));
						if (jvalueList[i] == jvalue){
							$("#jvalueSelect :last").attr("selected", "selected");
						}
					}					
				}

				/* Add event listeners to the two range filtering inputs */		

				$("#configurationSelect").change( function () {					
					oTable.fnDraw();
					fillSelectFields();
				} );
				
				$("#termSelect").change( function () {					
					oTable.fnDraw();
					fillSelectFields();
				} );
				
				$("#jvalueSelect").change( function () {					
					oTable.fnDraw();
					fillSelectFields();
				} );
				
				
				$('#min_3').keyup( function() { oTable.fnDraw(); fillSelectFields();} );
				$('#max_3').keyup( function() { oTable.fnDraw(); fillSelectFields();} );				
				
				fillSelectFields();
			

			
			function addSelection(level){			
				var level_config, term="", j, energy, lifetime ,source; 						
				//alert("dfg");
				
				level.children('.level_config').html(level.children('.level_config').html().replace(/\<span.*\<\/span\>/gi,""));
				
		    	if (!level.hasClass('row_selected') ){

		    		level.removeClass('selectable').addClass('row_selected').find(':checkbox').attr('checked', 'checked');
		    		
					level_config = level.children('.level_config').html().replace(/\<sup\>(\w+)\<\/sup\>/gi,"@{$1}").replace(/\<sub\>(.*)\<\/sub\>/gi,"~{$1}").replace(/\<sub\>/gi,"~{").replace(/\<\/sub\>/gi,"}");
		    						    		
		    		level.children('.level_config').html('<input size="" type="text" name="level_config[]" value="'+level_config+'"/>');


		    		termSecondpart= level.children('.term').children('span:first-child').text();
					termPrefix = level.children('.term').children('sup:nth-child(2)').text();
					//alert('!'+level.children('.term').children('sup:nth-child(2)').text()+'!');
		    		termFirstpart= level.children('.term').children('span:nth-child(3)').html().replace(/\<sup\>(\w+)\<\/sup\>/gi,"@{$1}").replace(/\<sub\>(.*)\<\/sub\>/gi,"~{$1}");
		    		termMultiply= level.children('.term').children('sup:last-child').text();

					term += '<input size="1" type="text" class="termSecondpart" name="termSecondpart[]" value="'+termSecondpart+'"/>';
		    		term += '<input size="1" type="text" class="termPrefix" name="termPrefix[]" value="'+termPrefix+'"/>';
		    		term += '<input size="1" type="text" class="termFirstpart" name="termFirstpart[]" style="width:70px" value="'+termFirstpart+'"/>';
		    		term += '<input size="1" type="text" class="termMultiply" name="termMultiply[]" value="'+termMultiply+'"/>';

		    		level.children('.term').html(term);
	    		

		    		j = level.children('.j').html();
		    		level.children('.j').html('<input size="" type="text" name="j[]" value="'+j+'"/>');

		    		energy = level.children('.energy').html();
		    		level.children('.energy').html('<input size="" type="text" name="energy[]" value="'+energy+'"/>');

		    		lifetime = level.children('.lifetime').html();
		    		level.children('.lifetime').html('<input size="" type="text" name="lifetime[]" value="'+lifetime+'"/>');

		    		//levelSources = level.children('.source').html();


					level.children('.ref_aggregator').data("ref", level.children('.ref_aggregator').html());
					level.children('.ref_aggregator').html('<a href="#" class=" button white" id="select_ref_aggregator">�������</a>');

					level.children('.ref_original').data("ref", level.children('.ref_original').html());
					level.children('.ref_original').html('<a href="#" class=" button white" id="select_ref_original">�������</a>');


					/*					if (level.find('.links').children('a').html() !=null) {
                                            level.children('.source').data("source", level.find('.links').html());
                                            var buttons = '<span id="buttons"><a href="#" class="button white" id="add_source">+</a><a class="button white" id="remove_source">-</a></span>';
                                        }	else var buttons = '<span id="buttons"><a href="#" class="button white" id="add_source">+</a></span>';

                                            level.children('.source').append(buttons);
                                        //alert(level.children('.source').data("source"));
                    */
				}
			}
			

			function checkConfigFunction(config, energy){
				var sublevelsOrder="spdfghijklmnopqrstuvwxyz"			
				config = config.replace(/\([^\)]*\)/gi,"");
				config = config.replace(/([a-z])([0-9])/g,"$1@{1}$2");
				if (/\w/.test(config[config.length-1])){
					config = config+"@{1}";			
				}
			
				// �������� �� ���������� ���������� ������������ (�������-����������-���������� ����������)
				if ((/[^0-9][a-z]/g.test(config))||(/[0-9][^0-9a-z\}]/g.test(config))||(/[^0-9a-z\@\{\}\~]/g.test(config))){
					return("�������� �������� ������������");				
				}
				
				// �������� �� ��, ����� �� � ��� � �������� �������
				if (/[^0-9]/g.test(config.replace(/[^\{]+(\{[^\}]*\})/g,"$1").replace(/[\{\}]/g,""))){
					//alert(config.replace(/[^\{]+(\{[^\}]*\})/g,"$1").replace(/[\{\}]/g,""));
					return("������������ ������� � ���������� ����������");
				}
				
				// �������� �� ���������� ���������� �� ���������� � ���������� �� �������
				var electronsOnSublevels = config.replace(/\}[^\{]+\{/g,"}{").replace(/[^\{]*/,"").replace(/\}/g,"").split("{");
				var levels = config.replace(/\@\{[^}]*\}/g,"").split(/[a-z]/)
				var sublevels = config.replace(/\@[^\}]*\}/g,"").replace(/[0-9]/g,"").split("");
				for (var i = 0; i<sublevels.length; i++){
					if (electronsOnSublevels[i+1]>(4*sublevelsOrder.indexOf(sublevels[i])+2)){
						return("������������ ���������� ���������� �� ����������")
					}
					if (sublevelsOrder.indexOf(sublevels[i])+1>levels[i]){
						return("������������ ���������� ���������� �� �������");
					}
				}
			
				// �������, ������� ���������� ������� � ������������
			
				var electronsCount = 0;
				for (var i = 0; i<sublevels.length; i++){
					electronsCount+=(electronsOnSublevels[i+1]*1);
				}		
			
				if (energy == 0){
					var tempstring = config.replace(/\@[^\}]*\}/g,"").replace(/([a-z])/g,"$1,");
					levelsAndSubLevels = tempstring.substring(0,tempstring.length-1).split(",");
					var x = 100,y = 100;
			
					var levelsString = "1s2s2p3s3p4s3d4p5s4d5p6s4f5d6p7s5f6d7p";	
				
					var currentHighestLevel = "1s";
			
					for (var i=0;i<levelsAndSubLevels.length;i++){								
						if (levelsString.indexOf(currentHighestLevel)<levelsString.indexOf(levelsAndSubLevels[i])){
							currentHighestLevel=levelsAndSubLevels[i];
						}
					}			
			
					stringToCountElectrons = levelsString.substring(0, levelsString.indexOf(currentHighestLevel)).replace(/([a-z])/g,"$1,");
					stringToCountElectrons = stringToCountElectrons.substring(0, stringToCountElectrons.length-1);
					arrayToCountElectrons = stringToCountElectrons.split(",");
									
					for (var i=0;i<arrayToCountElectrons.length;i++){
						if (tempstring.substring(0,tempstring.length-1).indexOf(arrayToCountElectrons[i])==-1)	{
							if ((arrayToCountElectrons[i]=="6d")&&((config.indexOf("7p@{1}")!=-1)||(config.indexOf("7p@{2}")!=-1))){
								continue;
							}
							if ((arrayToCountElectrons[i]=="5f")&&((config.indexOf("6d@{1}")!=-1)||(config.indexOf("6d@{2}")!=-1))){
								continue;
							}
							if ((arrayToCountElectrons[i]=="4f")&&((config.indexOf("5d@{1}")!=-1)||(config.indexOf("5d@{2}")!=-1))){
								continue;
							}
							electronsCount+=(sublevelsOrder.indexOf(arrayToCountElectrons[i].replace(/\d/g,""))*4+2);					
						}
					}
			
					if (config=="4d@{10}"){
						electronsCount-=2;
					}
				}
			
				return(electronsCount);				
			}
			
			

			
			function removeSelection(level){
			var level_config, term="", j, energy, lifetime ,source, rts; 		    	    		
			
				level.removeClass('row_selected').addClass('selectable').find(':checkbox').removeAttr('checked');			    		
	    		
				lev_config = level.children('.level_config').children().prop('value');
				level_config = level.children('.level_config').children().prop('value').replace(/@\{([^\}\{]*)\}/gi,"<sup>$1</sup>").replace(/~\{([^\}\{]*)\}/gi,"<sub>$1</sub>");
	    		level.children('.level_config').html(level_config);

	    		termSecondpart = level.children('.term').children('.termSecondpart').prop('value');
				termPrefix = level.children('.term').children('.termPrefix').prop('value');
	    		termFirstpart = level.children('.term').children('.termFirstpart').prop('value').replace(/@\{([^\}\{]*)\}/gi,"<sup>$1</sup>").replace(/~\{([^\}\{]*)\}/gi,"<sub>$1</sub>");
	    		termMultiply = level.children('.term').children('.termMultiply').prop('value');
	    		//alert(termMultiply)
	    		
	    		if (termSecondpart) term += '<span>'+termSecondpart+'</span>'; else term += '<span></span>';
				if (termPrefix) term += '<sup>'+termPrefix+'</sup>'; else term += '<sup></sup>';
	    		if (termFirstpart) term += '<span>'+termFirstpart+'</span>'; else term += '?';
	    		if (termMultiply) term += '<sup>'+termMultiply+'</sup>'; else term += '<sup></sup>';
	    		
	    		//alert(term);				   
	    		
	    		level.children('.term').html(term);

	    		j = level.children('.j').children().prop('value');
	    		level.children('.j').html(j);

	    		energy = level.children('.energy').children().prop('value');
	    		level.children('.energy').html(energy);

	    		lifetime = level.children('.lifetime').children().prop('value');
	    		level.children('.lifetime').html(lifetime); 
/*
				rts ="level_id="+level.find('.level_id').val();						
				rts+="&action=getSourceIDs";
	
				$.post("/levels_admin.php", rts,function(data) {
					//removeSelection(level);
					//alert(data);
					level.children('.source').html(data);
				});
	*/

				if (level.children('.ref_aggregator').data("ref")){
					level.children('.ref_aggregator').html(level.children('.ref_aggregator').data("ref"));
				}else level.children('.ref_aggregator').html('');

				if (level.children('.ref_original').data("ref")){
					level.children('.ref_original').html(level.children('.ref_original').data("ref"));
				}else level.children('.ref_original').html('');



/*	    		if (level.children('.source').data("source")){
	    			//alert(level.children('.source').data("source"));
	    			level.children('.source').html('<span class="links">'+level.children('.source').data("source")+"</span>");
	    		}else level.children('.source').html('<span class="links"></span>');
*/
				
				if (energy==""){
					energy=0;
				}				
				
				var checkConfigResult = checkConfigFunction(lev_config,energy*1);
				
//					if (((energy*1==0)&&(checkConfigResult != $("#electronNumber").html()))||(/[^0-9]/g.test(checkConfigResult))){
//						createPopupMessage(level, checkConfigResult);
//					} else {
						level.children('.level_config').removeClass("errorText");
//					}
			}					
			
			
			function createPopupMessage(level, checkConfigResult){
				if (/[0-9]/g.test(checkConfigResult)){
							checkConfigResult="�������� ���������� ����������";
						} 
						level.children('.level_config').addClass("errorText");						
						level.children('.level_config').html(level.children('.level_config').html()+"<span class='popupconfigerror'>"+checkConfigResult+"</span>")
						level.children('.level_config').hover(
							function() {
								level.children('.level_config').children('.popupconfigerror').css('display','inline');								
								level.children('.level_config').children('.popupconfigerror').css('left',level.children('.level_config').position().left+level.children('.level_config').width()+20);								
								level.children('.level_config').children('.popupconfigerror').css('top',level.children('.level_config').position().top);
							},
							function() {
								level.children('.level_config').children('.popupconfigerror').css('display','none');
							}
						);			
			}
			
			/* Add a click handler to the rows - this could be used as a callback */	
			
			$(document).on("click", "tr.selectable td:not(.source)",function(){
				addSelection($(this).parent());							    	
		    });			
			
			$(document).on("click", ".row_selected input:checkbox", function(){
				removeSelection($(this).parent().parent());
			});
			
			
				
			$("#saveLevels").click(function(){		
					  
				var str = $(".row_selected input").serialize();
				str+="&action=saveLevels&count="+$(".row_selected .row_id").length;					
				$.post("/levels_admin.php", str, function(data){					
					$('.row_selected').each(function() {
						removeSelection($(this));
					  });

					//$('.row_selected').removeClass('row_selected').addClass('selectable');
					//window.location.href = "/admin/ru/element/"+data;
					//window.location.href = "";
					//alert(data);
					//$("body").append(data);
				});
				//alert(str);
			});	
			
						
			$("#deleteLevels").click(function(){
				var str = $(".row_selected .row_id").serialize()+"&action=deleteLevels";
				//alert(str);
				if (confirm('������� ��������� ������?')) {									
					$.post("/levels_admin.php", str, function(data){
						$(".row_selected").remove();
						//alert(data);
					});							
				}
			});
			
			$("#createLevel").click(function(){	
				if (confirm('������� ����� �������?')) {
					var atom_id = $("#atom_id").val();	
					
					$.post("/levels_admin.php",  { atom_id: atom_id , action: "createLevel" }, function(data) {
						if (p=="") p="even"; else if ($('tr:first').hasClass("odd")) p="even"; else p="odd";  
						//var str ='<tr class="'+p+' row_selected"><input type="hidden" class="level_id" name="level_id[]" value="'+data+'"><td class=""><input type="checkbox" checked="checked"></td><!--  <td></td>--><td id="level_config" class=" sorting_1"><input size="" type="text" name="level_config[]" value=""></td><td id="term"><input size="1" type="text" class="termPrefix" name="termPrefix[]" value=""><input size="1" type="text" id="termFirstpart" name="termFirstpart[]" value=""><input size="1" type="text" id="termMultiply" name="termMultiply[]" value=""></td><td id="j"><input size="" type="text" name="j[]" value=""></td><td id="energy"><input size="" type="text" name="energy[]" value=""></td><td id="lifetime"><input size="" type="text" name="lifetime[]" value=""></td><td id="source"><a href="#" class="button white" id="add_source">+</a><a class="button white" id="remove_source">-</a></td></tr>';
						var str ='<tr class="'+p+' row_selected"><td class=""><input type="checkbox" checked="checked"><input type="hidden" class="row_id" name="row_id[]" value="'+data+'"></td><td class="level_config"><input type="text" name="level_config[]" value=""></td><td class="term"><input size="1" type="text" class="termSecondpart" name="termSecondpart[]" value=""><input size="1" type="text" class="termPrefix" name="termPrefix[]" value=""><input size="1" type="text" class="termFirstpart" name="termFirstpart[]" value=""><input size="1" type="text" class="termMultiply" name="termMultiply[]" value=""></td><td class="j"><input type="text" name="j[]" value=""></td><td class="energy"><input type="text" name="energy[]" value=""></td><td class="lifetime"><input type="text" name="lifetime[]" value=""></td><td class="source"><a href="#" class="button white" id="add_source">+</a></td></tr>';
						//alert(data);
						$('#table1').prepend(str);
						//alert(data);
						//window.location.href = "/admin/ru/element/"+data;
					});		
				}
			});		
			
			
			$("tr.selectable").each(function(){
				
				var level = $(this);
				var level_config, energy;
					    		
				level_config = level.children('.level_config').html().replace(/\<sup\>(\w+)\<\/sup\>/gi,"@{$1}").replace(/\<sub\>(.*)\<\/sub\>/gi,"~{$1}");	    		
				
	    		energy = level.children('.energy').html();
	    						
				if (energy==""){
					energy=0;
				}				
				
				var checkConfigResult = checkConfigFunction(level_config,energy*1);
				
//					if (((energy*1==0)&&(checkConfigResult != $("#electronNumber").html()))||(/[^0-9]/g.test(checkConfigResult))){
//						createPopupMessage(level, checkConfigResult);
//					} else {
						level.children('.level_config').removeClass("errorText");
//					}
		    });

				$(document).on("click", "#select_ref_aggregator", function(event){
					event.preventDefault();
					var field = "level_ref_aggregator";
					var level = $(this).parent().parent();
					//var atom_id = $("#atom_id").val();
					var level_id=level.find('.row_id').val();
					var ref_id;
					var str;
					$('.clicked').removeClass('clicked');
					level.addClass("clicked");

					str = '../addref/'+level_id+'/'+field;

					$.fancybox({
						'href'          : str,
						'width'             : '75%',
						'height'            : '75%',
						'autoScale'         : false,
						'type'              : 'iframe',
						'hideOnContentClick': false
					});
				});

				$(document).on("click", "#select_ref_original", function(event){
					event.preventDefault();
					var field = "level_ref_original";
					var level = $(this).parent().parent();
					//var atom_id = $("#atom_id").val();
					var level_id=level.find('.row_id').val();
					var ref_id;
					var str;
					$('.clicked').removeClass('clicked');
					level.addClass("clicked");

					str = '../addref/'+level_id+'/'+field;

					$.fancybox({
						'href'          : str,
						'width'             : '75%',
						'height'            : '75%',
						'autoScale'         : false,
						'type'              : 'iframe',
						'hideOnContentClick': false
					});
				});
		
});