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


    private function build_header_table(){

        $result = [];
        $result['maps_header_teritory'] = [
            "territoryname"        => "Territory Name",
            "ordertype"            => "Order Type",
            "total_ar"             => "Total AR",
            "collected_amount"     => "Collected Amount",
            "confirmed_amount"     => "Confirmed Amount",
            "unconfirmed_amount"   => "Unconfirmed Amount",
            "total_difference"     => "Total Difference",
            "collection_rate_pct"  => "Collection Rate (%)",
            "trust_gap_pct"        => "Trust Gap (%)"
        ];

        $result['maps_header_driversales'] = [
            "salesnameordrivername"   => "Sales / Driver Name",
            "ordertype"               => "Order Type",
            "ar_handled"              => "AR Handled",
            "collected_amount"        => "Collected Amount",
            "confirmed_amount"        => "Confirmed Amount",
            "unconfirmed_amount"      => "Unconfirmed Amount",
            "total_difference"        => "Total Difference",
            "avg_days_late"           => "Average Days Late",
            "invoices_with_difference"=> "Invoices With Difference",
            "missing_evidence_count"  => "Missing Evidence Count"
        ];

        $result['maps_header_customer'] = [
            "customercode"        => "Customer Code",
            "ordertype"           => "Order Type",
            "customername"        => "Customer Name",
            "invoice_count"       => "Invoice Count",
            "total_difference"    => "Total Difference",
            "total_ar"            => "Total AR",
            "collected_amount"    => "Collected Amount",
            "confirmed_amount"    => "Confirmed Amount",
            "unconfirmed_amount"  => "Unconfirmed Amount"
        ];


        return $result;

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
        $stmt = $pdo->prepare("EXEC sp_PortalSDN_GetCITSummaryData
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


        //Kategori datasets
        $row_cashier_driver = $datasets[0][0]; //card
        $row_ar_remaining = $datasets[1][0]; //card
        $data_teritory = $datasets[2]; //Tabel
        $data_driversales = $datasets[3]; //Tabel
        $data_customer = $datasets[4]; //Tabel
        $data_payment = $datasets[5]; //Tabel
        $data_uncollectible = $datasets[6]; //Tabel


        //=========== End Of Build Datasets  ===========



        //=========== Build Filter Data  ===========
        $build_filterData = $this->build_filterData( $request );

        //=========== Build Summary AR Data : branches, driver, customer  ===========
        $summaryDataTop = $this->build_dashboardTopData($datasets); 


        //TOP dan COD summary territory
        $summary10territory = $summaryDataTop['summary10territory'];
        $result_territory_TOP = $summary10territory['result_TOP'];
        $result_territory_COD = $summary10territory['result_COD'];


        //TOP dan COD summary driversales
        $summary10drivers = $summaryDataTop['summary10drivers'];
        $result_drivers_TOP = $summary10drivers['result_TOP'];
        $result_drivers_COD = $summary10drivers['result_COD'];

        //TOP dan COD summary customer
        $summary10customer = $summaryDataTop['summary10customer'];
        $result_customer_TOP = $summary10customer['result_TOP'];
        $result_customer_COD = $summary10customer['result_COD'];

        // $summary10uncollectible = $summaryDataTop['summary10uncollectible'];
        // $data_summary_view['result_uncollectible_TOP'] = $summary10uncollectible['result_TOP'];
        // $data_summary_view['result_uncollectible_COD'] = $summary10uncollectible['result_COD'];

        // ========== Mapping Key Untuk Tabel Territory  ========
        $build_header_table =  $this->build_header_table();
        $maps_header_teritory = $build_header_table['maps_header_teritory'];
        $maps_header_driversales = $build_header_table['maps_header_driversales'];
        $maps_header_customer = $build_header_table['maps_header_customer'];


        return view('cit.index', array_merge($build_filterData, $summaryDataTop), compact(
            'row_cashier_driver',
            'row_ar_remaining',
            'data_teritory',
            'data_driversales',
            'data_customer',
            'data_payment',
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
    private function prepare_dataset_keyordertype( $datasets_grafik = [], $key_label_row ) : array{
        //Mengambil nilai dan key pada setiap row dataset grafik tapi hanya key dan nilai yang ada di row_param dengan catatan key yang ada di row datasets itu ada di row param


        $row_param = [
            "label" =>  ( string ) "NAMA LABEL", 
            "confirmed_amount" => (float) 0.0, //= collected_amount
            "unconfirmed_amount" => (float) 0.0,  //= ar_amount - collected amount 
            'ordertype' => (string) "COD OR TOP",
        ];
        $result_TOP = [];
        $result_COD = [];
        foreach ($datasets_grafik as $row_datasets) {
            $row_result_new = [];
            //========= NORMALISASI KEY ===========
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
            //Menambahkan key label 
            $row_result_new['label'] = $row_datasets[$key_label_row];


            //========= KLASIFIKASI DARI ORDERTYPE UNTUK RESULTNYA ===========
            $ordertype = $row_result_new['ordertype'];
            if (  $ordertype == "COD"  ) {
                //Tambahkan ke result COD
                $result_COD[] = $row_result_new;

            }else if ( $ordertype == "TOP" ) {
                //Tambahkan ke result TOP
                $result_TOP[] = $row_result_new;
            }
        }




        //Mengurutkan data berdasarkan nilai unclaimed terbersar uuntuk result_TOP
        usort($result_TOP, fn ($a, $b) => $b['unconfirmed_amount'] <=> $a['unconfirmed_amount']);
        //Mengambil top 10 data
        $result_TOP = array_slice($result_TOP, 0, 10);


         //Mengurutkan data berdasarkan nilai unclaimed terbersar untuk result_COD
        usort($result_COD, fn ($a, $b) => $b['unconfirmed_amount'] <=> $a['unconfirmed_amount']);
        //Mengambil top 10 data
        $result_COD = array_slice($result_COD, 0, 10);

        // dd($result_COD);
        // dd($result_TOP); 


        $result = [
            'result_COD' => $result_COD,
            'result_TOP' => $result_TOP
        ];

        return $result;

    }
    //Method untuk mengembalikan data summary pada dashboard
    private function build_dashboardTopData(array $data): array{

        /*
        DATA YANG DISUMMARY :
        - Dataset branch/territory -> index 2 
        - Dataset driver -> index 3
        - Dataset customer -> index 4
        */

        // =============================== TOP 10 CABANG =============================== 
        $datasets_territory = $data[2]; //source data_territory
        $summary10territory = $this->prepare_dataset_keyordertype( $datasets_territory, "territoryname" );

        // =============================== TOP 10 DRIVER =============================== 
        $datasets_driversales = $data[3];
        $summary10drivers = $this->prepare_dataset_keyordertype( $datasets_driversales, "salesnameordrivername" );

        //   =============================== TOP 10 CUSTOMER =============================== 
        $datasets_customer = $data[4];
        $summary10customer = $this->prepare_dataset_keyordertype( $datasets_customer, "customername" );


        // ========================= TOP 10 PAYMENT TYPE BERMASALAH =============================== 
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

        //  ============================= TOP 10 ALASAN TIDAK TERTAGIH ===========================
        $data_uncollectible = $data[6];
        $summary10uncollectible = [];
        foreach ($data_uncollectible ?? [] as $row) {
            $row_reason = [
                'reason'      => $row['uncollectiblereason'] ?? null,
                'case_count'  => (int) ($row['case_count'] ?? 0),
                'amount_risk' => (float) ($row['amount_at_risk'] ?? 0),
            ];
            $summary10uncollectible[] = $row_reason;
        }   
        //Mengurutkan data
        usort($summary10uncollectible, fn ($a, $b) => $b['case_count'] <=> $a['case_count']);
        //Mengambil top 10 data
        $summary10uncollectible = array_slice($summary10uncollectible, 0, 10); 


        // dd( $summary10uncollectible );



        // =============================== FINAL OUTPUT =============================== 

        $result = [
            'summary10territory'  => $summary10territory, //[ "result_TOP" => [ [], [], [] ], "result_COD" => [], [], [] ]
            'summary10drivers'   => $summary10drivers, //[ "result_TOP" => [ [], [], [] ], "result_COD" => [], [], [] ]
            'summary10customer' => $summary10customer, //[ "result_TOP" => [ [], [], [] ], "result_COD" => [], [], [] ]
            'summary10payment' => $summary10payment, 
            'summary10uncollectible' => $summary10uncollectible, 
        ];

        return $result;
    }



}
