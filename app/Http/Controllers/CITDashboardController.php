<?php
namespace App\Http\Controllers;

use App\Services\UserMetricFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;


class CITDashboardController extends Controller{

    private $db;
    private $userBranchCode;
    private $userTitle;
    private $userRegion;

    public function __construct(){

        // ========================== START CONNECTION ========
        $this->db = DB::connection('sqlsrv-sdndwh');
        // ========================== END CONNECTION ========

        // ========================== START USER INFO ========
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $this->userBranchCode = $user->karyawan->kode_cabang;
            $this->userTitle      = $user->karyawan->jabatan->nama_jabatan;
            $this->userRegion     = $user->karyawan->cabang->kode_region;

            return $next($request);
        });
    }

    public function index( Request $request ){



        //=========== Build Datasets   ===========
        $filters = [
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'branch'   => $request->input('branch'),
            'region'   => $request->input('region'),
            'branch'   => $request->input('branch')
        ];

        $filters = UserMetricFilterService::applyUserDefaultFilters(
            $filters,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );

        // Extract region code safely
        $filters['region'] = $request->input('region');

        // Prepare Datasets with filters
        $pdo = $this->db->getPdo();
        $stmt = $pdo->prepare("EXEC sp_PortalSDN_GetCITSummaryData_test
            @startDate    = :startDate,
            @endDate   = :endDate,
            @branchCode = :branchCode,
            @regionCode  = :regionCode
            ");
        $stmt->execute([
            'startDate' => $filters['startDate'],
            'endDate'  => $filters['endDate'],
            'regionCode'   => $filters['region'],
            'branchCode'   => $filters['branch']
        ]);
        //Fetch Alll Data - Convert to array index multi dimensi [ [], [], [], ....... ]
        $datasets = [];
        do {
            $datasets[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());




        //++++++ Card Dataset
        $row_card_dashboard = $datasets[0][0]; 

        //Menyaatukan key Total Invoice Document dengan Total Confirmed Invoice Document dalam 1 key dengan array 
        $row_card_dashboard['Total Invoice Document'] = $row_card_dashboard['Total Confirmed Invoice Document'] . "/" . $row_card_dashboard['Total Invoice Document'] ;
        unset( $row_card_dashboard['Total Confirmed Invoice Document'] );
        //Menyaatukan key Total Invoice Document dengan Total Confirmed Invoice Document dalam 1 key dengan array 
        $row_card_dashboard['Total Invoice Document AR OD'] = $row_card_dashboard['Total Confirmed Invoice Document AR OD'] . "/" . $row_card_dashboard['Total Invoice Document AR OD'] ;
        unset( $row_card_dashboard['Total Confirmed Invoice Document AR OD'] );

        

        //++++++ Payment Type Pie Chart
        $data_paymentType = $datasets[1]; //[  []  ]


        //++++++ Data Grafik
        $data_successCollect_branch = $datasets[2]; //Success Rate Collection [ [], [], [], ..... ]
        $data_successCollectOverdue_branch = $datasets[3]; //Success Rate Collection AR Overdue [ [], [], [], ..... ]
        $data_badCollectiondriver = $datasets[4]; // Bad Collection by Driver Or Sales [ [], [], [], ..... ]
        $data_badCollectionCustomer = $datasets[5]; // Bad Customer Collection [ [], [], [], ..... ]



        //++++++ Data View Detail Tabel
        $data_view_teritory = $datasets[6];  //[ [], [], [], ..... ]
        $data_view_driversales = $datasets[7]; // [ [], [], [], ..... ]
        // dd( $data_view_teritory );
        $data_view_customer = $datasets[8]; // [ [], [], [], ..... ]


        //=========== End Of Build Datasets  ===========

        //=========== Build Filter Data  ===========
        $build_filterData = $this->build_filterData( $request );

        //=========== Build Summary Data ================

        // ++++ Data Summary Success Rate Collection Branch/Teritory ++++ 
        $summary_paymentType = $this->build_datasetGrafik( 
            $data_paymentType, 
            'paymenttype', 
            [
                "mapping_key_data" => false,
                "TOP_data" => true,
                "COD_data" => true,
                "sorting_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ],
                "limit_10_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ], 
                "add_territoryid" => true 

            ]
        ); 
        // dd($summary_paymentType);
        //Menghasilkan  [ "result_TOP" => [[],[],[]],"result_COD" => [[],[],[]], "result_all" => [[],[],[]] ]


        // ++++ Data Summary Success Rate Collection Branch/Teritory ++++ 
        $summary_successCollect_branch = $this->build_datasetGrafik( 
            $data_successCollect_branch, 
            'territoryname', 
            [
                "mapping_key_data" => false,
                "TOP_data" => true,
                "COD_data" => true,
                "sorting_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ],
                "limit_10_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ], 
                "add_territoryid" => false 


            ]
        ); 
        // dd($summary_successCollect_branch);
        //Menghasilkan  [ "result_TOP" => [[],[],[]],"result_COD" => [[],[],[]], "result_all" => [[],[],[]] ]


        // ++++ Data Summary Success Rate Collection Overdue Branch/Teritory ++++ 
        $summary_successCollectOverdue_branch = $this->build_datasetGrafik( 
            $data_successCollectOverdue_branch, 
            'territoryname', 
            [
                "mapping_key_data" => false,
                "TOP_data" => false,
                "COD_data" => false,
                "sorting_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ],
                "limit_10_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ], 
                "add_territoryid" => true 

            ]
        );
        // dd($summary_successCollectOverdue_branch);
        //Menghasilkan  [ "result_TOP" => [[],[],[]],"result_COD" => [[],[],[]], "result_all" => [[],[],[]] ]


        // ++++ Data Summary Driversales TOP dan COD ++++ 
        $summary_badCollectionDriver = $this->build_datasetGrafik( 
            $data_badCollectiondriver, 
            'salesnameordrivername', 
            [
                "mapping_key_data" => false,
                "TOP_data" => true,
                "COD_data" => true,
                "sorting_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ],
                "limit_10_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ], 
                "add_territoryid" => true 

            ]
        );
        // dd(  $summary_badCollectionDriver );
        //Menghasilkan  [ "result_TOP" => [[],[],[]],"result_COD" => [[],[],[]], "result_all" => [[],[],[]] ]


        // ++++ Data Summary Customers ++++ 
        $summary_badCollectionCustomer = $this->build_datasetGrafik( $data_badCollectionCustomer, 
            'customername', 
            [
                "mapping_key_data" => false,
                "TOP_data" => false,
                "COD_data" => false,
                "sorting_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ],
                "limit_10_data" => [
                    "TOP_data" => false,
                    "COD_data" => false,
                    "all_data" => false
                ], 
                "add_territoryid" => true 

            ]
        );
        // dd(  $summary_badCollectionCustomer );
        //Menghasilkan  [ "result_TOP" => [[],[],[]],"result_COD" => [[],[],[]], "result_all" => [[],[],[]] ]


        // ========== Mapping Key Untuk Tabel View Detail  ========
        $build_header_table =  $this->build_header_table( $data_view_teritory, $data_view_driversales, $data_view_customer );
        $maps_header_teritory = $build_header_table['maps_header_teritory'];
        $maps_header_driversales = $build_header_table['maps_header_driversales'];
        $maps_header_customer = $build_header_table['maps_header_customer'];


        // ========== GET LAST UPDATE ======
        $last_update = $this->get_last_updateCIT();

        // dd(  $data_view_teritory );


        return view('cit.index', array_merge($build_filterData), compact(
            'row_card_dashboard',
            'data_view_teritory',
            'data_view_driversales',
            'data_view_customer',
            'data_paymentType',
            'summary_paymentType',
            'summary_successCollect_branch',
            'summary_successCollectOverdue_branch',
            'summary_badCollectionDriver',
            'summary_badCollectionCustomer',
            'maps_header_teritory',
            'maps_header_driversales',
            'maps_header_customer',
            'last_update'
        ));




    }



    //COH Reason untuk table data header
    public function coh_reason(Request $request){




        //========== Get Datasets From DB ==========
        if ( !isset($_GET['page']) && empty( $pageNumber ) ) {
            //Page pertama, kalo gak ada parameter ?get
            $pageNumber = 1;
        }else{
            $pageNumber = $request->input('page');
        }

        $pageSize = 100;
        $filters = [
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'branch' => $request->input('branch'),
            'pageNumber' => $pageNumber,
            'pageSize' => $pageSize,
        ];
        // Extract region code safely
        $regionParts = explode(' ', $request->input('region', ''));
        $filters['region'] = $regionParts[1] ?? null;

        // Prepare Datasets with filters
        $pdo = $this->db->getPdo();
        $stmt = $pdo->prepare("EXEC sp_ZH_Collection_CIT_Performance_Final
            @startDate    = :startDate,
            @endDate   = :endDate,
            @territoryId  = :territoryId,
            @pageNumber = :pageNumber,
            @pageSize = :pageSize
            ");
        $stmt->execute([
            'startDate' => $filters['startDate'],
            'endDate'  => $filters['endDate'],
            'territoryId'   => $filters['branch'],
            'pageNumber'   => $filters['pageNumber'],
            'pageSize'   => $pageSize,
        ]);
        //Fetch Alll Data - Convert to array index multi dimensi [ [ [], [], [] ] ]
        $datasets = [];
        do {
            $datasets[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());

        //========== End Of Get Datasets From DB ==========

        //Data COH Primer
        $data_coh = $datasets[0]; //Data Pagination atau bagian
        $total_all_data = (int) $data_coh[0]['TotalRows'];

        // dd( $datasets );
        // dd($data_coh);




        $data_paginator = new LengthAwarePaginator(
        $data_coh,                         // data per halaman ( data result dari SP )
        $total_all_data,                        // total seluruh data 
        $filters['pageSize'],          // per page
        $filters['pageNumber'],        // current page
        [
            'path'     => request()->url(),
            'pageName' => 'page',
            'query'    => request()->query(),
        ]);
        //Mapping number formating view
        $key_rupiah_data = [
            "Outstanding_AR",
            "Total_Collection",
            "Selisih_Payment",
            "Total_Payment_Cash",
            "Total_Payment_TF",
            "Total_Payment_Giro",
            "Total_Payment_Cash_TF",
            "Total_TOP_OD_Value",
            "Total_Paid_TOP_OD_Value",
            "COH",
            "Bank In",
            "Balance",
        ];



        //=========== Build Filter Data  ===========
        $build_filterData = $this->build_filterData( $request );



        return view('cit.coh_reason', $build_filterData,  compact(
            'data_coh', 
            'data_paginator',
            'key_rupiah_data', 
        ));
    }


    //COH Reason untuk table data detail dari header dengan detail COH dan CIT berdasarkan parameter branchCode yang dilempar
    public function coh_reason_detail(Request $request){

        //================================ Validasi parameter wajib =======================================
        //INGAT!!! Route ini hanya bisa diakses kalo ada parameter ?branchCode dan nilainya gak kosong
        $branchCodeParam = null;
        $collectionDateParam = null;
        if ( ( isset( $_GET['branchCode']) && !empty($_GET['branchCode']) ) && ( isset( $_GET['collectionDate']) && !empty($_GET['collectionDate']) )  ) {
            $branchCodeParam = $request->input('branchCode');
            $collectionDateParam = $request->input('collectionDate')
            ? Carbon::parse($request->input('collectionDate'))->startOfDay()
            : Carbon::now()->startOfYear()->startOfDay();
        }else{
            return redirect()->route('cit.coh_reason');
        }
        // $ROUTE_DEFAULT = route('cit.coh_reason_detail') . "?branchCode=" . $branchCodeParam;




        //=========================== End Of Validasi parameter wajib =======================================

        $pdo = $this->db->getPdo();

        //========================== Build Datasets - Detail CIT  =======================


        // Prepare Datasets with filters
        $stmt = $pdo->prepare("EXEC sp_ZH_Collection_CIT_Dashboard
            @territoryId  = :branchCode,
            @startDate  = :startDate,
            @endDate  = :endDate
            ");
        $stmt->execute([
            'branchCode' => $branchCodeParam,
            'startDate' => $collectionDateParam,
            'endDate' => $collectionDateParam,
        ]);
        //Fetch All Data - Convert to array index multi dimensi $datasets = [ [ [], [], .... ] ]
        $datasets_coh_detailCIT = [];
        do {
            $datasets_coh_detailCIT[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());

        $data_coh_detailCIT = $datasets_coh_detailCIT[0]; // $data_coh_detailCIT [ [], [], [] ]


        // dd( $data_coh_detailCIT );
        

        //========================== Build Datasets - Detail COH  ======================= 


        // Prepare Datasets with filters
        $stmt = $pdo->prepare("EXEC sp_ZH_COH_Summary
            @territoryId  = :branchCode,
            @startDate  = :startDate,
            @endDate  = :endDate
            ");
        $stmt->execute([
            'branchCode' => $branchCodeParam,
            'startDate' => $collectionDateParam,
            'endDate' => $collectionDateParam,
        ]);

        //Fetch Alll Data - Convert to array index multi dimensi $datasets = [ [ [], [] ] ]
        $datasets_coh_detailCOH = [];
        do {
            $datasets_coh_detailCOH[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());

        $data_coh_detailCOH = $datasets_coh_detailCOH[0]; // $data_coh_detailCOH [ [], [], [] ]

        // dd( $data_coh_detailCOH );


        //============================ End Of Build Datasets =====================================

        $key_rupiah_data = [
            "amount",
            "cash_collectionamount",
            "cash_confirmamount",
            "cash_differenceamount",
            "transfer_collectionamount",
            "transfer_confirmamount",
            "transfer_differenceamount",
            "giro_collectionamount",
            "giro_confirmamount",
            "giro_differenceamount",
            "total_payment",
            "claimpromoorreturn"
        ];


        // dd($data_coh_detailCOH[0]);


        //=========== Build Filter Data  ===========
        $build_filterData = $this->build_filterData( $request );
        return view('cit.coh_reason_detail', $build_filterData, compact(
            'data_coh_detailCOH', 
            'data_coh_detailCIT', 
            'branchCodeParam',
            // 'ROUTE_DEFAULT',
            'key_rupiah_data'
        ));

    }




    private function get_last_updateCIT(){
        $cit_table = $this->db->table('dbo.ZH_Collection_CIT_Dashboard');
        $query = $cit_table
        ->select('senddate')
        ->distinct()
        ->orderBy('senddate', 'DESC')
        ->first();

        $senddate = $query->senddate;


        return $senddate;
    }
    private function build_filterData( Request $request ){

        $result = [];

        $result['startDate'] = $request->input('startDate')
        ? Carbon::parse($request->input('startDate'))->startOfDay()
        : Carbon::now()->startOfYear()->startOfDay();

        $result['endDate'] = $request->input('endDate')
        ? Carbon::parse($request->input('endDate'))->endOfDay()
        : Carbon::now()->endOfYear()->endOfDay();

        $locationFilters = UserMetricFilterService::getFilters(
            $this->db,
            $this->userTitle,
            $this->userBranchCode,
            $this->userRegion
        );
        $result['regions']   = $locationFilters['regions'];
        $result['branches']  = $locationFilters['branches'];
        $locations = $locationFilters['locations'];

        return $result;
    }


    //Method untuk melakukan normalisasi data yang datanya memiliki key confirmed_amount dan unconfirmed amount seperti datasets_driversales, datasets_branch, dan datasets cumstomer
    // Method ini akan menghasilkan data yang digunakan pada data grafik baru
    private $option_build_default = [
        "mapping_key_data" => true,
        "TOP_data" => true,
        "COD_data" => true,
        "sorting_data" => [
            "TOP_data" => true,
            "COD_data" => true,
            "all_data" => true
        ], 
        "limit_10_data" => [
            "TOP_data" => true,
            "COD_data" => true,
            "all_data" => true
        ], 
        "add_territoryid" => false 
    ];
    private function build_datasetGrafik( $datasets_grafik = [], $key_label_row, $option_build = [] ) : array{

        /*
        ===== FITUR UTAMA =======
        => MAPPING 
        - Melakukan mapping dan normalisasi untuk mengambil nilai dan key pada setiap row dataset grafik tapi hanya key dan nilai yang ada di row_param dengan catatan key yang ada di row datasets itu ada di row param
        - Akan berjalan ketika $option_build['mapping_key_data'] bernilai true dan berpengatuh untuk semua $result_all, $result_TOP, $result_TOP

        => CLUSTERING by ordertype
        - Melakukan pemisahan data result berdasarkan kolom ordertype 
        - Ini hanya akan berjalan ketika row_datasets punya kolom ordertype dan beberapa kondisi :
        + Kalo $option_build['TOP_data'] maka akan berpengaruh pada $result_TOP
        + Kalo $option_build['COD_data'] maka akan berpengaruh pada $result_COD
        - Akan berpengaruh hanya ke $result_TOP dan $result_COD 
    
        => SORTING by unconfirmed_amount
        - Mengurutkan data dari nilai terbesar ke terkecil berdasarkan kolom unconfirmed_amount 
        - Ini hanya akan berjalan ketika row_datasets punya kolom unconfirmed_amount 
        - Akan berpengaruh berdasarkan $option_build dari beberapa key :
        + Kalo $option_build['limit_10_data']['TOP_data'] maka akan berpengaruh pada $result_TOP ( nilai true maka berjalan, nilai false maka tidak berjalan )
        + Kalo $option_build['limit_10_data']['COD_data'] maka akan berpengaruh pada $result_COD ( nilai true maka berjalan, nilai false maka tidak berjalan )
        + Kalo $option_build['limit_10_data']['all_data'] maka akan berpengaruh pada $result_all ( nilai true maka berjalan, nilai false maka tidak berjalan )

        => LIMITING 10 Data
        - Melakukan pengambilan hanya 10 data 
        - Akan berpengaruh berdasarkan $option_build dari beberapa key :
        + Kalo $option_build['limit_10_data']['TOP_data'] maka akan berpengaruh pada $result_TOP ( nilai true maka berjalan, nilai false maka tidak berjalan )
        + Kalo $option_build['limit_10_data']['COD_data'] maka akan berpengaruh pada $result_COD ( nilai true maka berjalan, nilai false maka tidak berjalan )
        + Kalo $option_build['limit_10_data']['all_data'] maka akan berpengaruh pada $result_all ( nilai true maka berjalan, nilai false maka tidak berjalan ) 

        => ADD TERRITORY ID 
        - Melakukan penambahan kode teritory/cabang pada label
        - Hal ini hanya bisa dilakukan ketika row memiliki kolom territoryid dan option_build['add_territoryid']
        - Fitur ini dilakukan pada saat iterasi mapping dan clustering setiap row setelah row_result_new sudah terbentuk atau termapping
        =====================================================================
        */


        //============= Handling Format Default Parameter Argument $option_build ========== 

        //Ketika User memasukkan argumen $option_build ke method dengan struktur yang kurang benar dari yang diharapkan (property $option_build_default) sehingga yang diterapkan itu key dan sturktur yang default

        //Handling default key value pada option build nilai default true pada option 
        $option_build = array_merge($this->option_build_default, $option_build); //Menimpa nilai dan key kalo ada key yang sama dengan yang ada di $this->option_build_default 

        //Handling default format $option_build['sorting_data'] Untuk Sorting Data kalo format argumennya gak bene. Jadi bentuk formatnya harus array
        if ( is_array($option_build['sorting_data']) == true ) {
            $option_build['sorting_data'] = array_merge( $this->option_build_default['sorting_data'], $option_build['sorting_data']);
        }else{
            $option_build['sorting_data'] = $this->option_build_default['sorting_data'];
        }

        //Handling default format $option_build['limit_10_data'] Untuk Sorting Data, Jadi bentuk formatnya harus array
        if ( is_array($option_build['limit_10_data']) == true ) {
            $option_build['limit_10_data'] = array_merge( $this->option_build_default['limit_10_data'], $option_build['limit_10_data'] );
        }else{
            $option_build['limit_10_data'] = $this->option_build_default['limit_10_data'];
        }


        //==== Define option build 
        $option_mapping_key_data = ( $option_build['mapping_key_data'] == true ) ? true : false;
        //Cek apakah di row pada datasets ada kolom order type dan option build TOP data atau COD data
        $row_cek = $datasets_grafik[0];
        $option_clustering_data = ( isset( $row_cek['ordertype'] ) && ( $option_build['TOP_data'] == true || $option_build['COD_data'] == true ) ) ? true : false;
        $option_sorting = $option_build['sorting_data']; //[ "TOP_data" => "", "COD_data" => "all_data" => "" ]
        $option_limiting = $option_build['limit_10_data']; //[ "TOP_data" => "", "COD_data" => "all_data" => "" ]


        
        $result_TOP = [];  //Kumpulan data hanya row data dengan ordertype dengan nilai TOP
        $result_COD = []; //Kumpulan data hanya row data dengan ordertype dengan nilai COD
        $result_all = []; //Kumpulan semua data 

        //===========  NORMALISASI DAN MAPPING =============================
        // Menyiapkan dan Mengisi $result_TOP, $result_COD, dan $result_all

        $row_param = [
            "label" =>  ( string ) "NAMA LABEL", // Ini untuk keperluan data name di label 
            "confirmed_amount" => (float) 0.0, //= collected_amount
            "unconfirmed_amount" => (float) 0.0,  // key untuk sortar_amount - collected amount 
            'ordertype' => (string) "COD OR TOP",
        ];

        // dd( ( $option_build['TOP_data'] == true || $option_build['COD_data'] == true ) );
        // dd( isset( $row_cek['ordertype']) );




        //Melakukan mapping dan clustring dengan melakukan pengecekan setiap row data dari datasets
        foreach ($datasets_grafik as $key => $row_datasets) {



            //++++++ Define row result baru dengan ada key label untuk standarisasi +++
            // Ubah nilai dengan keynya key_label_row dengan key label
            $row_result_new = [];
            $row_result_new['label'] = $row_datasets[$key_label_row];
            // unset( $datasets_grafik[$key][$key_label_row] );

            //========= ( MAPPING ) MELAKUKAN MAPPING DAN NORMALISASI KEY PADA ROW BARU ===========
            //Jika Option Build Ingin Mapping Data
            //Mapping key kolom untuk data. Mengambil hanya key yang ada di row_param
            //Cek apakah key pada row_param ada di key setiap row_dataset
            //Melakukan Isi key row result new berdasarkan $option_mapping_key_data
            //Jika option mapping itu treu, Lakukan pengecekan apakah key pada row_datasets ada di key row_param. Kalo dia ada, maka lakukan Cek apakah key pada row datasets ada di key row_param

             //+++ Lakukan Mapping Key Data Berdasarkan Dengan option buildnya
            if ( $option_mapping_key_data == true ) {

                 //Jika option_mapping_key_data itu true, maka lakukan clusterting dengan Cek apakah key pada row datasets ada di key row_param. Jika ada maka tambahkan key dan nilai tersebut ke row baru 
                foreach ($row_datasets as $key_row_dataset => $value_row_datasets ) {
                    if ( array_key_exists( $key_row_dataset, $row_param) == true ) {
                    //Kalo key pada row datasetsnya ada di row param, maka tambahkan key dan valuenya ke $row_result_new
                    //Menambahkan ke key row_result_new dengan key tersebut
                    //+++ Ubah tipe data nilai_add yang akan ditambahkan, berdasarkan tipe data dari nilai degan key yang sama di row_param +++
                        $nilai_row_param = $row_param[ $key_row_dataset ];
                        $nilai_baru = $value_row_datasets;
                        if ( is_string( $nilai_row_param ) ) {
                        //Jika pada row param nilai dengan key ini tipe datanya string. Maka ubah ke string
                            $nilai_baru = ( string ) $nilai_baru;
                        }else if ( is_float( $nilai_row_param ) ) {
                        //Jika pada row param nilai dengan key ini tipe datanya float. Maka ubah ke float
                            $nilai_baru = ( float ) $nilai_baru;
                        }

                        $row_result_new[$key_row_dataset] = $nilai_baru;
                    }

                }
            }else{
                // Jika option_mapping_key_data itu false, maka jangan lakukan clustering dan masukkan semmua key dan nilai pada row datasest ke row_result_new tanpa adanya pengecekan, kemudian datanya apa adanya tapi ada key. kolom label untuk kebutuhan FE
                $row_result_new = array_merge( $row_result_new, $row_datasets );
            }


            //====== Proses selanjutnya dibawah itu artinya row_result_new sudah di build keynya ===== 

            //========= ADD TERITORY - Penambahan territory id pada label  =========
            //INGAT!! ini hanya bisa dilakukan ketika row data punya key kolom territoryid dan option_build['add_territoryid']
            // Jadinya format : label - territoryid 
            if ( $option_build['add_territoryid'] == true && isset( $row_result_new['territoryid'] ) ) {
                $row_result_new['label'] = $row_result_new['label'] . " - " . $row_result_new['territoryid'];
            }




            //========= ( CLUSTERING ) PEMBUATAN DAN PEMISAHAN DATA RESULT by ordertype result_TOP dan result_COD ===========
            //Ini hanta akan bekerja ketika setiap row data itu punya kolom ordertype dan antara option build TOP_data atau COD_data bernilai true
            //INGATT!! option_build TOP_data atau COD_data hanya akan bekerja ketika row datasets punya kolom ordertype
            //Yang di cek dari kolomnya itu dari row_result_new 
            //$result_all tidak terpengaruh clustering
            if (  $option_clustering_data == true ) {
                //Row Resultnya Punya Order Type dan Option Build Untuk TOP Data Atau COD Data dinyalakan, maka lakukan clustering berdasarkan ordertype
                $ordertype = $row_result_new['ordertype'];
                if ( $option_build['TOP_data'] == true && $ordertype == "TOP"  ) {
                    //Jika option buildnya TOP data diaktifkan dan kolom ordertypenya TOP, row ini akan di tambahkan ke result_TOP
                    $result_TOP[] = $row_result_new;
                }else if ( $option_build['COD_data'] == true && $ordertype == "COD"  ) {
                    //Jika option buildnya COD data diaktifkan dan kolom ordertypenya COD, row ini akan di tambahkan ke result_COD
                    $result_COD[] = $row_result_new;
                }
            }

            //Memasukkan row baru untuk result_all jenis ke result_all 
            // Pada result all, untuk row nya punya kolom order type, pada labelnya tambahkan keterangan apakah ini jenis TOP atau COD dengan concat
            if ( isset($row_result_new['ordertype']) ) {
                $nama_label = $row_result_new['label'] . " - " . $row_result_new['ordertype'];
                $row_result_new['label'] = $nama_label;
            }
            $result_all[] = $row_result_new;
        }



        //============= FITUR DIBAWAH HANYA AKAN BERJALAN KETIKA DATA $result_TOP, $result_COD, dan $result_all sudah diisi
        //============= ( SORTING ) PENGURUTAN DATA by unconfirmed_amount ==============
        //Mengurutkan data berdasarkan nilai unconfirmed_amount terbersar untuk result_TOP dan result_COD dengan syarat punya kolom unconfirmed amount
        //Pengaturan sortring data kalo true maka pengaruh untuk semua

        //Mengurutkan data berdasarkan nilai unconfirmed_amount terbersar untuk result_TOP 
        if ( $option_sorting['TOP_data'] == true ) {
            usort($result_TOP, fn ($a, $b) => $b['unconfirmed_amount'] <=> $a['unconfirmed_amount']);
        }
        //Mengurutkan data berdasarkan nilai unconfirmed_amount terbersar untuk result_COD 
        if ( $option_sorting['COD_data'] == true ) {
            usort($result_COD, fn ($a, $b) => $b['unconfirmed_amount'] <=> $a['unconfirmed_amount']);
        }
        //Mengurutkan data berdasarkan nilai unconfirmed_amount terbersar untuk result_all 
        if ( $option_sorting['all_data'] == true ) {
            usort($result_all, fn ($a, $b) => $b['unconfirmed_amount'] <=> $a['unconfirmed_amount']);
        }

        //============= ( LIMITING ) PENGAMBILAN HANYA 10 DATA  ==============
        //Mengambil top 10 data untuk result_TOP
        if (  $option_limiting['TOP_data'] == true ) {
            $result_TOP = array_slice($result_TOP, 0, 10);
        }
        //Mengambil top 10 data untuk result_COD
        if (  $option_limiting['COD_data'] == true ) {
            $result_COD = array_slice($result_COD, 0, 10);
        }
        //Mengambil top 10 data untuk result_all
        if (  $option_limiting['all_data'] == true ) {
            $result_all = array_slice($result_all, 0, 10);
        }


        // Hasilnya
        $result = [
            'result_COD' => $result_COD, // [ [], [], [] ] - Kumpulan data hanya row data dengan ordertype dengan nilai TOP
            'result_TOP' => $result_TOP, // [ [], [], [] ] - Kumpulan data hanya row data dengan ordertype dengan nilai COD
            'result_all' => $result_all // [ [], [], [] ] - Kumpulan semua data
        ];


        // dd($result);
        return $result;
    }


    private function build_header_table( $data_teritory, $data_driversales, $data_customer ){
        //INGAT!! Semua key header di semua data maps_header keynya harus sama denggan key dari row data aslinya 
        //maps_header_teritory harus sama dengan key yang ada di data_teritory
        //maps_header_driversales harus sama dengan key yang ada di data_driversales
        //maps_header_customer harus sama dengan key yang ada di data_customer

        $result = [];
        $result['maps_header_teritory'] = [
            "territoryid"        => "Territory ID",
            "territoryname"      => "Territory Name",
            "invoice_count"      => "Invoice Count",
            "total_difference"   => "Total Difference",
            "total_ar"           => "Total AR",
            "collected_amount"   => "Collected Amount",
            "confirmed_amount"   => "Confirmed Amount",
            "unconfirmed_amount" => "Unconfirmed Amount",
        ];

        $result['maps_header_driversales'] = [
            "territoryid"        => "Territory ID",
            "salesnameordrivername" => "Sales / Driver Name",
            "ordertype"             => "Order Type",
            "total_ar"              => "Total AR",
            "collected_amount"      => "Collected Amount",
            "confirmed_amount"      => "Confirmed Amount",
            "unconfirmed_amount"    => "Unconfirmed Amount",
            "total_difference"      => "Total Difference",
            "avg_days_late"         => "Average Days Late",
        ];


        $result['maps_header_customer'] = [
            "territoryid"        => "Territory ID",
            "customercode"       => "Customer Code",
            "customername"       => "Customer Name",
            "invoice_count"      => "Invoice Count",
            "total_difference"   => "Total Difference",
            "total_ar"           => "Total AR",
            "collected_amount"   => "Collected Amount",
            "confirmed_amount"   => "Confirmed Amount",
            "unconfirmed_amount" => "Unconfirmed Amount",
        ];


        return $result;

    }


}
