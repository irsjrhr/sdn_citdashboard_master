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
        $regionParts = explode(' ', $request->input('region', ''));
        $filters['region'] = $regionParts[1] ?? null;

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
        //Fetch Alll Data - Convert to array index multi dimensi value array associatif
        $datasets = [];
        do {
            $datasets[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());






        //Card Dataset
        $row_card_dashboard = $datasets[0][0]; 
        // dd($row_card_dashboard);
        $row_paymentType_pie = $datasets[1][0];

        // Data Grafik
        $data_teritory_grafik = $datasets[2]; 
        $data_overdue_grafik = $datasets[3]; 
        $data_driversales_grafik = $datasets[4]; 
        $data_customer_grafik = $datasets[5];

        // Data Tabel
        $data_view_teritory = $datasets[6];
        $data_view_driversales = $datasets[7];
        $data_view_customer = $datasets[8];
        //=========== End Of Build Datasets  ===========


        //=========== Build Filter Data  ===========
        $build_filterData = $this->build_filterData( $request );

        //=========== Build  Top 10 Summary AR Data TOP dan COD: branches, driver, customer  ===========


        $summary_territory = $this->build_datasetGrafik( $data_teritory_grafik, 'territoryname', [
            "mapping_key_data" => true,
            "TOP_data" => false,
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
        ]);

        // ++++ Data Summary territory ++++ 
        $result_territory_TOP = $summary_territory['result_TOP'];
        $result_territory_COD = $summary_territory['result_COD'];


        // ++++ Data Summary driver sales ++++ 
        $summary_drivers = $this->build_datasetGrafik( $data_driversales_grafik,'salesnameordrivername');
        $result_drivers_TOP = $summary_drivers['result_TOP'];
        $result_drivers_COD = $summary_drivers['result_COD'];



        // ++++ Data Summary customer ++++ 
        $summary_customer = $this->build_datasetGrafik( $data_customer_grafik, 'customername');
        $result_customer_TOP = $summary_customer['result_TOP'];
        $result_customer_COD = $summary_customer['result_COD'];


        // ========== Mapping Key Untuk Tabel View Detail  ========
        $build_header_table =  $this->build_header_table( $data_view_teritory, $data_view_driversales, $data_view_customer );
        $maps_header_teritory = $build_header_table['maps_header_teritory'];
        $maps_header_driversales = $build_header_table['maps_header_driversales'];
        $maps_header_customer = $build_header_table['maps_header_customer'];


        return view('cit.index', array_merge($build_filterData), compact(
            'row_card_dashboard',
            'row_paymentType_pie',
            'data_view_teritory',
            'data_view_driversales',
            'data_view_customer',
            'result_territory_TOP',
            'result_territory_COD',
            'result_drivers_TOP',
            'result_drivers_COD',
            'result_customer_TOP',
            'result_customer_COD',
            'maps_header_teritory',
            'maps_header_driversales',
            'maps_header_customer',
        ));

    }


    public function coh_reason(Request $request){



        //=========== Build Datasets   ===========


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
        //Fetch Alll Data - Convert to array index multi dimensi value array associatif
        $datasets = [];
        do {
            $datasets[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());





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

    public function coh_reason_detail(Request $request){

        //Route ini hanya bisa diakses kalo ada parameter ?branchCode dan nilainya gak kosong
        $branchCodeParam = null;
        if ( isset( $_GET['branchCode']) && !empty($_GET['branchCode'])  ) {
            $branchCodeParam = $request->input('branchCode');
        }else{
            return redirect()->route('cit.coh_reason');
        }
        $ROUTE_DEFAULT = route('cit.coh_reason_detail') . "?branchCode=" . $branchCodeParam;

        //=========== Build Datasets   ===========
        // Prepare Datasets with filters
        $pdo = $this->db->getPdo();
        $stmt = $pdo->prepare("EXEC sp_ZH_Collection_CIT_Dashboard
            @territoryId  = :branchCode
            ");
        $stmt->execute([
            'branchCode' => $branchCodeParam
        ]);
        //Fetch Alll Data - Convert to array index multi dimensi value array associatif
        $datasets = [];
        do {
            $datasets[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());



        //Data COH Primer
        $data_coh_detail = $datasets[0];
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


        // dd($data_coh_detail[0]);

        //=========== Build Filter Data  ===========
        $build_filterData = $this->build_filterData( $request );
        return view('cit.coh_reason_detail', $build_filterData, compact(
            'data_coh_detail', 
            'branchCodeParam',
            'ROUTE_DEFAULT',
            'key_rupiah_data'
        ));

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
        =====================================================================
        */


        //============= Handling Format Default Parameter Argument $option_build ========== 

        //Ketika User memasukkan argumen $option_build ke method dengan struktur yang kurang benar dari yang diharapkan (property $option_build_default) sehingga yang diterapkan itu key dan sturktur yang default

        //Handling default key value pada option build nilai default true pada option 
        $option_build = array_merge($this->option_build_default, $option_build); //Menimpa nilai dari key kalo ada key yang sama dengan denan $this->option_build_default 

        //Handling default format $option_build['sorting_data'] Untuk Sorting Data kalo argumennya gak bener 
        if ( is_array($option_build['sorting_data']) == true ) {
            $option_build['sorting_data'] = array_merge( $this->option_build_default['sorting_data'], $option_build['sorting_data']);
        }else{
            $option_build['sorting_data'] = $this->option_build_default['sorting_data'];
        }

         //Handling default format $option_build['limit_10_data'] Untuk Sorting Data
        if ( is_array($option_build['limit_10_data']) == true ) {
            $option_build['limit_10_data'] = array_merge( $this->option_build_default['limit_10_data'], $option_build['limit_10_data'] );
        }else{
            $option_build['limit_10_data'] = $this->option_build_default['limit_10_data'];
        }

        
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
        $option_mapping_key_data = ( $option_build['mapping_key_data'] == true ) ? true : false;
        //Cek apakah di row pada datasets ada kolom order type dan option build TOP data atau COD data
        $row_cek = $datasets_grafik[0];
        $option_clustering_data = ( isset( $row_cek['ordertype'] ) && ( $option_build['TOP_data'] == true || $option_build['COD_data'] == true ) ) ? true : false;


        // dd( ( $option_build['TOP_data'] == true || $option_build['COD_data'] == true ) );
        // dd( isset( $row_cek['ordertype']) );
        // dd( $option_clustering_data );

        //Melakukan mapping dan clustring dengan melakukan pengecekan setiap row data dari datasets
        foreach ($datasets_grafik as $row_datasets) {
            $row_result_new = [];

            //Menambahkan key label untu standarisasi
            $row_result_new['label'] = $row_datasets[$key_label_row];

            //========= ( MAPPING ) MELAKUKAN MAPPING DAN NORMALISASI KEY ===========
            if ( $option_mapping_key_data == true ) {
                //Jika Option Build Ingin Mapping Data
                //Mapping key kolom untuk data. Mengambil hanya key yang ada di row_param
                //Cek apakah key pada row param ada di row_dataset
                foreach ($row_param as $key_row_param => $nilai_row_param ) {
                    $param_add = false;

                    //Kalo key pada row_param itu pada row_datasets, maka ambil itu key dan nilainya 
                    if ( array_key_exists( $key_row_param, $row_datasets ) ) {
                        $param_add = true;
                        $nilai = $row_datasets[ $key_row_param ];

                        //Define tipe data nilainya berdasarkan nilai row paramnya
                        if ( is_string( $nilai_row_param ) ) {
                            $nilai = ( string ) $nilai;
                        }else if ( is_float( $nilai_row_param ) ) {
                            $nilai = ( float ) $nilai;
                        }

                        //Menambahkan ke key row_result_new dengan key tersebut
                        $row_result_new[$key_row_param] = $nilai;
                    }
                }

            }

            //========= ( CLUSTERING ) PEMBUATAN DAN PEMISAHAN DATA RESULT by ordertype result_TOP dan result_COD ===========
            //Ini hanta akan bekerja ketika setiap row data itu punya kolom ordertype dan antara option build TOP_data atau COD_data bernilai true
            //INGATT!! option_build TOP_data atau COD_data hanya akan bekerja ketika row datasets punya kolom ordertype
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
            $result_all[] = $row_result_new;
        }



        //============= FITUR DIBAWAH HANYA AKAN BERJALAN KETIKA DATA $result_TOP, $result_COD, dan $result_all sudah diisi



        //============= ( SORTING ) PENGURUTAN DATA by unconfirmed_amount ==============
        //Mengurutkan data berdasarkan nilai unconfirmed_amount terbersar untuk result_TOP dan result_COD dengan syarat punya kolom unconfirmed amount
        //Pengaturan sortring data kalo true maka pengaruh untuk semua

        //Mengurutkan data berdasarkan nilai unconfirmed_amount terbersar untuk result_TOP 
        if ( $option_build['sorting_data']['TOP_data'] == true ) {
            usort($result_TOP, fn ($a, $b) => $b['unconfirmed_amount'] <=> $a['unconfirmed_amount']);
        }
        //Mengurutkan data berdasarkan nilai unconfirmed_amount terbersar untuk result_COD 
        if ( $option_build['sorting_data']['COD_data'] == true ) {
            usort($result_COD, fn ($a, $b) => $b['unconfirmed_amount'] <=> $a['unconfirmed_amount']);
        }
        //Mengurutkan data berdasarkan nilai unconfirmed_amount terbersar untuk result_all 
        if ( $option_build['sorting_data']['all_data'] == true ) {
            usort($result_all, fn ($a, $b) => $b['unconfirmed_amount'] <=> $a['unconfirmed_amount']);
        }

        //============= ( LIMITING ) PENGAMBILAN HANYA 10 DATA  ==============
        //Mengambil top 10 data untuk result_TOP
        if ( $option_build['limit_10_data']['TOP_data'] == true ) {
            $result_TOP = array_slice($result_TOP, 0, 10);
        }
        //Mengambil top 10 data untuk result_COD
        if ( $option_build['limit_10_data']['COD_data'] == true ) {
            $result_COD = array_slice($result_COD, 0, 10);
        }
        //Mengambil top 10 data untuk result_all
        if ( $option_build['limit_10_data']['all_data'] == true ) {
            $result_all = array_slice($result_all, 0, 10);
        }


        // Hasilnya
        $result = [
            'result_COD' => $result_COD, // [ [], [], [] ] - Kumpulan data hanya row data dengan ordertype dengan nilai TOP
            'result_TOP' => $result_TOP, // [ [], [], [] ] - Kumpulan data hanya row data dengan ordertype dengan nilai COD
            'result_all' => $result_all // [ [], [], [] ] - Kumpulan semua data
        ];


        return $result;
    }


    // Method ini akan menghasilkan data yang digunakan pada grafik pie
    public function build_datasetPaymentTop10($datasets_payment){
        // ======================= TOP 10 PAYMENT TYPE BERMASALAH =========================== 
        $datasets_payment = $data[5];
        $summary10payment = [];
        foreach ($datasets_payment ?? [] as $row) {

            $row_payments = [
                'payment_type'     => $row['paymenttype'] ?? null,
                'total_difference' => (float) ($row['total_difference'] ?? 0),
                'avg_diff_pct'     => (float) ($row['avg_difference_pct'] ?? 0),
            ];
            $summary10payment[] = $row_payments;
        }
        //Mengurutkan data
        usort($summary10payment, fn ($a, $b) => $b['total_difference'] <=> $a['total_difference']);
        //Mengambil top 10 data
        $summary10payment = array_slice($summary10payment, 0, 10); 
        return $summary10payment;
    }
    private function build_header_table( $data_teritory, $data_driversales, $data_customer ){



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
