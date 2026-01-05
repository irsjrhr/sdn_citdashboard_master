$(document).ready(function() {


	$('.tab_indicator').on('click', function(e) {
		open_tab( $(this) );
	});
	$('.tab_indicator').eq(0).click();


});




var 
triger_open = true,
COL_CONTAINER_DATA_ACTIVE; //DEBUG DOM YANG SEDANG ACTIVE

var open_tab = ( tab_indicator_click ) => {



	if ( triger_open == false  ) {
		return false;
	}

	triger_open =  false;
	var data_target = tab_indicator_click.attr('data-target');
	var tab_indicator = $('.tab_indicator');
	var tab_indicator_target = tab_indicator.filter(`.tab_indicator[data-target=${data_target}]`);
	var col_container_data = $('.col_container_data');
	var col_container_data_target = col_container_data.filter('#' + data_target);

	//Membuka container data berdasarkan tab el yang sedang dibuka
	col_container_data.removeClass('active');
	setTimeout(function(e) {
		triger_open = true;

		//Memberikan tanda active ke col_container_data target
		col_container_data_target.addClass('active');

		COL_CONTAINER_DATA_ACTIVE = col_container_data_target

		//Memberikan efek ke button
		tab_indicator.removeClass('active');
		tab_indicator_target.addClass('active');
	}, 500);

	console.log("Membuka", col_container_data_target);


}