$(function () {	

var graphOptions = {
  'height': 350,
  'title': 'Графік цін на топливо в рік',
  'width': 1000,
  'fixPadding': 18,
  'barFont': [0, 18, "bold"],
  'labelFont': [0, 13, 0]
};	
	
var propan_data = 22.90;
var gaz_data = 12.90;
var dizel_data = 25.00;
var enrj_data = 2.44;
var pelet_data = 3.50;	

$("#value_propan").val(propan_data);
$("#value_gaz").val(gaz_data);
$("#value_dizel").val(dizel_data);
$("#value_enrj").val(enrj_data);
$("#value_pelet").val(pelet_data);
	  


$( "#boiler_output" ).keyup(function() {
	if ( $( "#boiler_output" ).val() < 1 ){
		$( "#boiler_output" ).val(1);
	}
	$('#boiler_output').keypress(validateNumber);
	$('#example, #table_calc').empty(); 
	tableCreate();
});

function validateNumber(event) {
    var key = window.event ? event.keyCode : event.which;
    if (event.keyCode === 8 || event.keyCode === 46) {
        return true;
    } else if ( key < 48 || key > 57 ) {
        return false;
    } else {
    	return true;
    }
};

tableCreate();

function tableCreate(){
	
var boiler_output = $("#boiler_output").val(); 	
var kkal2kW = 860;
var dataForTable = [[],[],[],[],[],[],[]];

/* Название столбца  */			dataForTable[0][0] = '№';
/* Теплотворность (кВт) */  	dataForTable[0][1] = 1;	
/* Теплотворность (ККал) */  	dataForTable[0][2] = 2;	
/* КПД котла */  				dataForTable[0][3] = 3;	
/* Максимальный расход в час */ dataForTable[0][4] = 4;	
/* Номинальный расход в сутки */dataForTable[0][5] = 5;
/* Номинальный расход в месяц*/ dataForTable[0][6] = 6;	
/* Номинальный расход в год */  dataForTable[0][7] = 7;  

/* Название столбца  */			dataForTable[1][0] = 'Наименование';
/* Теплотворность (кВт) */  	dataForTable[1][1] = 'Теплотворность (кВт)';	
/* Теплотворность (ККал) */  	dataForTable[1][2] = 'Теплотворность (ККал)';	
/* КПД котла */  				dataForTable[1][3] = 'КПД котла';	
/* Максимальный расход в час */ dataForTable[1][4] = 'Максимальный расход в час*';	
/* Номинальный расход в сутки */dataForTable[1][5] = 'Номинальный расход в сутки';
/* Номинальный расход в месяц*/ dataForTable[1][6] = 'Номинальный расход в месяц';	 
/* Номинальный расход в год */  dataForTable[1][7] = 'Номинальный расход в год *';  

/* Пропан-бутан  */  			
/* Название столбца  */			dataForTable[2][0] = 'Пропан-бутан (кг)';
/* Теплотворность (кВт) */  	dataForTable[2][1] = 12.80;
/* Теплотворность (ККал) */  	dataForTable[2][2] = ( dataForTable[2][1] * kkal2kW ).toFixed(2) ;
/* КПД котла */  				dataForTable[2][3] = 0.92; 
/* Максимальный расход в час */ dataForTable[2][4] = ( boiler_output / (dataForTable[2][1] * dataForTable[2][3]) ).toFixed(2);
/* Номинальный расход в сутки */dataForTable[2][5] = ( dataForTable[2][4] * 0.7 * 12 ).toFixed(2);
/* Номинальный расход в месяц*/ dataForTable[2][6] = ( dataForTable[2][5] * 30 ).toFixed(2);
/* Номинальный расход в год */  dataForTable[2][7] = ( dataForTable[2][5] * 178 ).toFixed(2);	  

/* Природний газ */  			
/* Название столбца  */			dataForTable[3][0] = 'Природный газ (куб/м)';
/* Теплотворность (кВт) */  	dataForTable[3][1] = 9.2;
/* Теплотворность (ККал) */  	dataForTable[3][2] = ( dataForTable[3][1] * kkal2kW ).toFixed(2) ;
/* КПД котла */  				dataForTable[3][3] = 0.92; 
/* Максимальный расход в час */ dataForTable[3][4] = ( boiler_output / (dataForTable[3][1] * dataForTable[3][3]) ).toFixed(2);
/* Номинальный расход в сутки */dataForTable[3][5] = ( dataForTable[3][4] * 0.7 * 12 ).toFixed(2);
/* Номинальный расход в месяц*/ dataForTable[3][6] = ( dataForTable[3][5] * 30 ).toFixed(2);
/* Номинальный расход в год */  dataForTable[3][7] = ( dataForTable[3][5] * 178 ).toFixed(2);	  

/* Дизельне паливо */  			
/* Название столбца  */			dataForTable[4][0] = 'Дизтопливо (л)';
/* Теплотворность (кВт) */  	dataForTable[4][1] = 11.9;
/* Теплотворность (ККал) */  	dataForTable[4][2] = ( dataForTable[4][1] * kkal2kW ).toFixed(2) ;
/* КПД котла */  				dataForTable[4][3] = 0.85; 
/* Максимальный расход в час */ dataForTable[4][4] = ( boiler_output / (dataForTable[4][1] * dataForTable[4][3]) ).toFixed(2);
/* Номинальный расход в сутки */dataForTable[4][5] = ( dataForTable[4][4] * 0.7 * 12 ).toFixed(2);
/* Номинальный расход в месяц*/ dataForTable[4][6] = ( dataForTable[4][5] * 30 ).toFixed(2);
/* Номинальный расход в год */  dataForTable[4][7] = ( dataForTable[4][5] * 178 ).toFixed(2);	  

/* Електроенергетика */  			
/* Название столбца  */			dataForTable[5][0] = 'Электроенергия (кВт*ч)';
/* Теплотворность (кВт) */  	dataForTable[5][1] = 1;
/* Теплотворность (ККал) */  	dataForTable[5][2] = ( dataForTable[5][1] * kkal2kW ).toFixed(2) ;
/* КПД котла */  				dataForTable[5][3] = 0.98; 
/* Максимальный расход в час */ dataForTable[5][4] = ( boiler_output / (dataForTable[5][1] * dataForTable[5][3]) ).toFixed(2);
/* Номинальный расход в сутки */dataForTable[5][5] = ( dataForTable[5][4] * 0.7 * 12 ).toFixed(2);
/* Номинальный расход в месяц*/ dataForTable[5][6] = ( dataForTable[5][5] * 30 ).toFixed(2);
/* Номинальный расход в год */  dataForTable[5][7] = ( dataForTable[5][5] * 178 ).toFixed(2);	 

/* Пелети */  				
/* Название столбца  */			dataForTable[6][0] = 'Пеллеты (кг)';
/* Теплотворность (кВт) */  	dataForTable[6][1] = 4.5;
/* Теплотворность (ККал) */  	dataForTable[6][2] = ( dataForTable[6][1] * kkal2kW ).toFixed(2) ;
/* КПД котла */  				dataForTable[6][3] = 0.85; 
/* Максимальный расход в час */ dataForTable[6][4] = ( boiler_output / (dataForTable[6][1] * dataForTable[6][3]) ).toFixed(2);
/* Номинальный расход в сутки */dataForTable[6][5] = ( dataForTable[6][4] * 0.7 * 12 ).toFixed(2);
/* Номинальный расход в месяц*/ dataForTable[6][6] = ( dataForTable[6][5] * 30 ).toFixed(2);
/* Номинальный расход в год */  dataForTable[6][7] = ( dataForTable[6][5] * 178 ).toFixed(2);	 

$('#0_stoimost').html( ( (dataForTable[2][7] * propan_data).toFixed(2) + '' ).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ') );
$('#1_stoimost').html( ( (dataForTable[3][7] * gaz_data).toFixed(2) + '' ).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ') );
$('#2_stoimost').html( ( (dataForTable[4][7] * dizel_data).toFixed(2) + '' ).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ') );
$('#3_stoimost').html( ( (dataForTable[5][7] * enrj_data).toFixed(2) + '' ).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ') );
$('#4_stoimost').html( ( (dataForTable[6][7] * pelet_data).toFixed(2) + '' ).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ') );



var propan_graph_data =	(Math.floor((dataForTable[2][7] * propan_data) * 100) / 100);
var gaz_graph_data =	(Math.floor((dataForTable[3][7] * gaz_data) * 100) / 100);
var dizel_graph_data =  (Math.floor((dataForTable[4][7] * dizel_data) * 100) / 100);
var enrj_graph_data =  	(Math.floor((dataForTable[5][7] * enrj_data) * 100) / 100);
var pelet_graph_data = 	(Math.floor((dataForTable[6][7] * pelet_data) * 100) / 100);

	legends = {
		'Пропан-бутан': propan_graph_data,
		'Природний газ': gaz_graph_data,
		'Дизельне паливо': dizel_graph_data,
		'Електроенергетика': enrj_graph_data,
		'Пелети': pelet_graph_data
	};


	graphite(legends, graphOptions, example);


    var table = document.getElementById("table_calc");
    var tbody  = document.createElement('tbody'); 
    tbody.style.width  = '100px';
    tbody.style.border = '1px solid black';

    for(var i = 0; i < 8; i++){
        var tr = tbody.insertRow();
		
        for(var j = 0; j < 7; j++){
				
                var td = tr.insertCell();
                td.appendChild(document.createTextNode( (dataForTable[j][i] + '').replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ') ) );
                td.style.border = '1px solid black';
                
        }
    }
    table.appendChild(tbody);
 
}

	
	
    var slider_propan = $("#slider_propan").slider({
    range: "min",     
    value: propan_data,
    min: 0,
    max: 50,
    step: 0.01,
    	
      slide: function(event, ui) {  
			$("#value_propan").val(ui.value);
			propan_data = ui.value;
			
			$('#example, #table_calc').empty(); 
			
			tableCreate();
    	}
  });
  
  var slider_gaz = $("#slider_gaz").slider({
    range: "min",     
    value: gaz_data,
    min: 0,
    max: 50,
    step: 0.01,
    	
      slide: function(event, ui) {   
			$("#value_gaz").val(ui.value); 
			gaz_data = ui.value;
		
			$('#example, #table_calc').empty(); 
		
			tableCreate();
    	}
  });
  
  var slider_dizel = $("#slider_dizel").slider({
    range: "min",     
    value: dizel_data,
    min: 0,
    max: 50,
    step: 0.01,
    	
      slide: function(event, ui) {  
			$("#value_dizel").val(ui.value);  
			dizel_data = ui.value;
			
			$('#example, #table_calc').empty(); 
			
			tableCreate();
    	}
  });
  
   var slider_enrj = $("#slider_enrj").slider({
    range: "min",     
    value: enrj_data,
    min: 0,
    max: 50,
    step: 0.01,
    	
      slide: function(event, ui) {  
			$("#value_enrj").val(ui.value);
			enrj_data = ui.value;
			
			$('#example, #table_calc').empty(); 
			
			tableCreate();
    	}
  });
  
  var slider_pelet = $("#slider_pelet").slider({
    range: "min",     
    value: pelet_data,
    min: 0,
    max: 50,
    step: 0.01,
    	
      slide: function(event, ui) {  
			$("#value_pelet").val(ui.value);
			pelet_data = ui.value;
			
			$('#example, #table_calc').empty(); 
			
			tableCreate();
    	}
  });

  
});
