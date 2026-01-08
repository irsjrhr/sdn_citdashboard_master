$(document).ready(function() {


	$('.tab_indicator').on('click', function(e) {
		open_container_data( $(this) );
	});
	$('.tab_indicator').eq(0).click();


	$('.tab_indicator_section').on('click', function(e) {
		open_section( $(this) );
		// alert();

	});
	$('.tab_indicator_section').eq(0).click();



});




var 
triger_open = true,
COL_CONTAINER_DATA_ACTIVE; //DEBUG DOM YANG SEDANG ACTIVE

var open_container_data = ( tab_indicator_click ) => {



	if ( triger_open == false  ) {
		console.log('Menghentikan event, karena event yang lalu masih ada yang aktif atau belum selesai -  Sedang Terbuka', COL_CONTAINER_DATA_ACTIVE);
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



var 
triger_open_section = true,
COL_SECTION_DATA_ACTIVE; //DEBUG DOM YANG SEDANG ACTIVE

var open_section = ( tab_indicator_section_click ) => {



	if ( triger_open_section == false  ) {
		console.log('Menghentikan event, karena event yang lalu masih ada yang aktif atau belum selesai -  Sedang Terbuka', COL_SECTION_DATA_ACTIVE);
		return false;
	}

	triger_open_section =  false;
	var data_target = tab_indicator_section_click.attr('data-target');
	var tab_indicator_section = $('.tab_indicator_section');
	var tab_indicator_section_target = tab_indicator_section.filter(`.tab_indicator_section[data-target=${data_target}]`);


	var col_section_data = $('.col_section_data');
	var col_section_data_target = col_section_data.filter('#' + data_target);

	//Membuka container data berdasarkan tab el yang sedang dibuka
	col_section_data.removeClass('active');
	setTimeout(function(e) {
		triger_open_section = true;

		//Memberikan tanda active ke col_section_data target
		col_section_data_target.addClass('active');

		COL_SECTION_DATA_ACTIVE = col_section_data_target


		//Memberikan efek ke button
		tab_indicator_section.removeClass('active');
		tab_indicator_section_target.addClass('active');
	}, 500);

	console.log("Membuka", col_section_data_target);


}