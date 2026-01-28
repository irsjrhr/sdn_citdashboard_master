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



	//+++ Event menampilkan list branch berdasarkan region dan business unit yang dipilih +++ 
	render_branchByRegionBU(); //By Default
	$('select[name=region], select[name=businessUnit]').on('change', function(){
		render_branchByRegionBU(); //By Event 
	});










});




var business_unit =  ""
var region =  "";

function render_branchByRegionBU(){


	//======= TETAPKAN PATTERN FILTER METRIX ====================

	//BusinessUnit Value Selected
	var option_businessUnit_selected = $('select[name=businessUnit] option:selected');
	var valOption_businessUnit_selected = option_businessUnit_selected.val();
	//Region Value Selected
	var option_region_selected = $('select[name=region] option:selected');
	var valOption_region_selected = option_region_selected.val();


	//======= TETAPKAN BRANCH FILTER BY METRIX PATTERNNYA ====================


	//+++ Deklarasi Select Branch dan Option
	var select_branch = $('select[name=branch]');

	//+++ Deklarasi branch object by metrix
	//Semua branch
	var option_branch_metrix = select_branch.find('option.option_metrix');
	 // Semua branch berdasarkan metrix business unit 
	var option_branch_metrixRegionBusinessUnit = option_branch_metrix.filter( '[data-region="'+valOption_region_selected+'"]' ).filter('[data-business-unit="'+valOption_businessUnit_selected+'"]');
	// Semua branch berdasarkan metrix region
	var option_branch_metrixRegion = option_branch_metrix.filter( '[data-region="'+valOption_region_selected+'"]' ); 
	// Semua branch berdasarkan metrix region dan business unit
	var option_branch_metrixBusinessUnit = option_branch_metrix.filter( '[data-business-unit="'+valOption_businessUnit_selected+'"]' ); 


	//======= LAKUKAN PENGKONDISIA BRANCH FILTER BY METRIX PATTERNNYA ====================


	//Tentukan branch target berdasarkan event dan logic
	/*
	=> Membuat logic Ketika filter region dan business unitnya dipilih all
	- Ketika filter BU All dan region All, maka semua branch di tampilkan

	- Ketika filter BU All dan region tidak all, maka menampilkan branch yang hanya punya atribut data-region atau filter branch berdasarkan region saja ( Business unit tidak dianggap )

	- Ketika filter Region All dan BU tidak all, maka menampilkan branch yang hanya punya atribut data-business-unit atau filter branch berdasarkan business unit saja ( Region tidak dianggap )

	*/
	var OPTION_BRANCH_TARGET;
	if ( option_businessUnit_selected.is('.option_all') && option_region_selected.is('.option_all') ) {

		// Ketika filter BU All dan region All, maka semua branch di tampilkan
		alert('Filter Business Unit dan Region All')
		OPTION_BRANCH_TARGET = option_branch_metrix;


	}else if ( option_region_selected.is('.option_all') ) {

		// Ketika filter Region All dan BU tidak all, maka menampilkan branch yang hanya punya atribut data-business-unit atau filter branch berdasarkan business unit saja ( Region tidak dianggap )



		alert('Filter Region All ( By Metrix Business Unit )')
		OPTION_BRANCH_TARGET = option_branch_metrixBusinessUnit;



	}else if (  option_businessUnit_selected.is('.option_all')  ) {
		// Ketika filter BU All dan region tidak all, maka menampilkan branch yang hanya punya atribut data-region atau filter branch berdasarkan region saja ( Business unit tidak dianggap )


		alert('Filter Business Unit All ( By Metrix Region )')
		OPTION_BRANCH_TARGET = option_branch_metrixRegion;


	}else{
	// Ketika filter Region All dan BU tidak all, maka menampilkan branch yang hanya punya atribut data-business-unit atau filter branch berdasarkan business unit saja ( Region tidak dianggap )

		alert('Filter Business Unit dan Region Tidak All ( By Metrix Keduanya ) ')


		OPTION_BRANCH_TARGET = option_branch_metrixRegionBusinessUnit;

	}


	//Hilangkan branch lain
	option_branch_metrix.removeClass('active');
	//Munculkan branch yang berelasi by pattern metrix
	OPTION_BRANCH_TARGET.addClass('active');

	// console.log( OPTION_BRANCH_TARGET );

	// return 1;




	//================= DEBUG ===============
	console.group("======== DEBUG FILTER METRIX RESULT ====== ");




	//Debug Metrix By Business Unit
	console.group(`Debug Metrix By Business Unit: ${valOption_businessUnit_selected}`);
	console.log(`Jumlah Branch: ${option_branch_metrixBusinessUnit.length}`);
	console.log(option_branch_metrixBusinessUnit);
	console.groupEnd();


	//Debug Branch Metrix By Region
	console.group(`Debug Branch Metrix By Region: ${valOption_region_selected}`);
	console.log(`Jumlah Branch: ${option_branch_metrixRegion.length}`);
	console.log(option_branch_metrixRegion);
	console.groupEnd();

	//Debug Branch Metrix By Business Unit dan Region
	console.group(`Debug Branch Metrix By Business Unit: ${valOption_businessUnit_selected} dan Region: ${valOption_region_selected}`);
	console.log(`Jumlah Branch: ${option_branch_metrixRegionBusinessUnit.length}`);
	console.log(option_branch_metrixRegionBusinessUnit);
	console.groupEnd();

	console.groupEnd();





}




//Fungsi Membuka option branch berdasarkan region yang terselect kecuali all 
function render_branchByRegion(){

	console.log('+++++++++++++++');

	var option_region_selected = $('select[name=region] option:selected');
	var valOption_region_selected = option_region_selected.val();




	var select_branch = $('select[name=branch]');
	var option_branch_metrix = select_branch.find('.option_metrix');

	//Buat pengkondisian jika option region yang terselect itu adalah option all, maka option metrix branch terbuka semua 
	var option_branch_metrixTarget;
	if ( option_region_selected.is('.option_all') ) {

		//Jika region all
		// alert('ALL');

		option_branch_metrixTarget = option_branch_metrix;

		console.log("Membuka branch dengan region All");


	}else{

		//Jika region bukan all
		// alert('NOT ALL');

		option_branch_metrixTarget = option_branch_metrix.filter( '[data-region="'+valOption_region_selected+'"]' );
		valOption_region_selected

		console.log("Membuka branch dengan region " + valOption_region_selected);
	}


	//++++ Membuka branch berdasarkan region yang terselect
	console.log( "Banyak branch terbuka :", option_branch_metrixTarget.length );

	//Hilangkan semua branch yang bukan target
	option_branch_metrix.removeClass('active');
	option_branch_metrixTarget.addClass('active');

}


// function render_ByRegion(){

// 	console.log('+++++++++++++++');

// 	var option_region_selected = $('select[name=region] option:selected');
// 	var valOption_region_selected = option_region_selected.val();




// 	var select_branch = $('select[name=branch]');
// 	var option_branch_metrix = select_branch.find('.option_metrix');

// 	//Buat pengkondisian jika option region yang terselect itu adalah option all, maka option metrix branch terbuka semua 
// 	var option_branch_metrixTarget;
// 	if ( option_region_selected.is('.option_all') ) {

// 		//Jika region all
// 		// alert('ALL');

// 		option_branch_metrixTarget = option_branch_metrix;

// 		console.log("Membuka branch dengan region All");


// 	}else{

// 		//Jika region bukan all
// 		// alert('NOT ALL');

// 		option_branch_metrixTarget = option_branch_metrix.filter( '[data-region="'+valOption_region_selected+'"]' );
// 		valOption_region_selected

// 		console.log("Membuka branch dengan region " + valOption_region_selected);
// 	}


// 	//++++ Membuka branch berdasarkan region yang terselect
// 	console.log( "Banyak branch terbuka :", option_branch_metrixTarget.length );

// 	//Hilangkan semua branch yang bukan target
// 	option_branch_metrix.removeClass('active');
// 	option_branch_metrixTarget.addClass('active');

// }




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
	}, 100);

	console.log("Membuka", col_section_data_target);


}





