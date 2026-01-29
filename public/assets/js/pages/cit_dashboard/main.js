$(document).ready(function() {


	//+++ Event Method Click Tab Indicator Untuk Col Container Data +++
	// tab_indicator_clickFirst();
	$('.tab_indicator').on('click', function(e) {
		open_container_data( $(this) );
	});


	$('.tab_indicator_section').eq(0).click();
	$('.tab_indicator_section').on('click', function(e) {
		open_section( $(this) );
		// alert();

	});

	//+++ Event menampilkan list branch berdasarkan region dan business unit yang dipilih +++ 
	render_branchByRegionBusinessUnit(); //By Default
	$('select[name=region], select[name=businessUnit]').on('change', function(){
		render_branchByRegionBusinessUnit(); //By Event 
	});










});



function render_branchByRegionBusinessUnit(){


	//======= TETAPKAN PATTERN FILTER METRIX ====================

	console.group("======== DEBUG FILTER METRIX REGION & BRANCH RESULT ====== ");


	//++++++ Tentukan Pattern BusinessUnit Value Selected
	var option_businessUnit_selected = $('select[name=businessUnit] option:selected');
	var valOption_businessUnit_selected = option_businessUnit_selected.val();

	//++++++ Tentukan Pattern Region Value Selected
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


	//======= LAKUKAN PENGKONDISIAN BRANCH FILTER BY METRIX PATTERNNYA ====================


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
		console.log('Filter Business Unit dan Region All')
		OPTION_BRANCH_TARGET = option_branch_metrix;


	}else if ( option_region_selected.is('.option_all') ) {

		// Ketika filter Region All dan BU tidak all, maka menampilkan branch yang hanya punya atribut data-business-unit atau filter branch berdasarkan business unit saja ( Region tidak dianggap )



		console.log('Filter Region All ( By Metrix Business Unit )')
		OPTION_BRANCH_TARGET = option_branch_metrixBusinessUnit;



	}else if (  option_businessUnit_selected.is('.option_all')  ) {
		// Ketika filter BU All dan region tidak all, maka menampilkan branch yang hanya punya atribut data-region atau filter branch berdasarkan region saja ( Business unit tidak dianggap )


		console.log('Filter Business Unit All ( By Metrix Region )')
		OPTION_BRANCH_TARGET = option_branch_metrixRegion;


	}else{
	// Ketika filter Region All dan BU tidak all, maka menampilkan branch yang hanya punya atribut data-business-unit atau filter branch berdasarkan business unit saja ( Region tidak dianggap )

		console.log('Filter Business Unit dan Region Tidak All ( By Metrix Keduanya ) ')

		OPTION_BRANCH_TARGET = option_branch_metrixRegionBusinessUnit;

	}


	//Hilangkan branch lain
	option_branch_metrix.removeClass('active');
	//Munculkan branch yang berelasi by pattern metrix
	OPTION_BRANCH_TARGET.addClass('active');

	// console.log( OPTION_BRANCH_TARGET );

	// return 1;



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


	//============== TAMPILKAN LIST FILTER REGION BERDASARKAN BUSINESS UNIT YANG TERPILIH ============= : 
	render_regionByBusinessUnit( option_businessUnit_selected, option_branch_metrixBusinessUnit );

	console.groupEnd();



}

function render_regionByBusinessUnit( option_businessUnit_selected,option_branch_metrixBusinessUnit ){

	/*
	-> option_businessUnit_selected digunakan untuk mengetahui apakah filter business unit itu dipilih all atau bukan 
	-> option_branch_metrixBusinessUnit digunakan untuk mengambil data region dari option branch yang terbuka dari atribut data-region
	*/


	//Lakukan pengkondisian jika filter option business unit yang dipilih itu all

	var select_region = $('select[name=region]');
	var option_region_metrix = select_region.find('.option_metrix');

	if ( option_businessUnit_selected.is('.option_all') ) {

		//Tampilkan semua region 
		option_region_metrix.addClass('active');

		return false;//Menghentikan laju fungsi
	}


	//Kumpulkan region yang punya branch yang memiliki business unit yang sedang terbuka. Atau region yang diambil dari branch yang sedang terbuka berdasarkan filter business unit

	//Ambil option branch yang terfilter metrix hanya dari business unit dan ambil regionnya dari  data-region nya 

	var list_region_active = [];
	for (var i = 0; i < option_branch_metrixBusinessUnit.length; i++) {
		var option_branch_metrix = $(option_branch_metrixBusinessUnit[i]);
		var data_region = option_branch_metrix.attr('data-region');
		list_region_active.push( data_region );

	}

	//Distict atau hilangkan data duplikat
	console.log("Before Distinct", list_region_active);
	list_region_active = [...new Set(list_region_active)];
	console.log("=== List Region Active Yang Terbuka ( After Distinct)", list_region_active);

	// return 1;


	//Pilih option_metrix region dari data-region-trim karena itu yang udah bersih
	console.log("+++++");
	option_region_metrix.removeClass('active');

	for (var i = 0; i < list_region_active.length; i++) {
		var region_active = list_region_active[i];
		var option_region_metrixTarget = option_region_metrix.filter('[data-region-trim="'+region_active+'"]');

		//Jadikan active region yang punya branches berdasarkan business unitnya
		console.log('Membuka filter region', region_active);
		option_region_metrixTarget.addClass('active');
	}
	console.log("+++++");










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




//Fungi untuk mengklik tab indicator berdasarkan COL_CONTAINER_DATA_ACTIVE yang tersimpan di local
var tab_indicator_clickFirst(){

	var COL_CONTAINER_DATA_SAVED = localStorage.getItem('COL_CONTAINER_DATA_ACTIVE');
	var tab_indicator = $('.tab_indicator');	
	var tab_indicator_target;
	if ( COL_CONTAINER_DATA_SAVED ) {

		tab_indicator_target = tab_indicator.filter(`.tab_indicator[data-target=${COL_CONTAINER_DATA_SAVED}]`);
	}else{
		tab_indicator_target = tab_indicator.eq(0);
	}

	open_container_data(tab_indicator_target);
}
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


		//Simpan ke localstorage untuk state 
		localStorage.setItem('COL_CONTAINER_DATA_ACTIVE', COL_CONTAINER_DATA_ACTIVE);

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





