<div class="modal fade" id="modal_detail_chart" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">

                        {{-- container detail chart - successRateCollectionBranch_all --}}
                        <div class="col-12 container_detail_chart" id="successRateCollectionBranch_all">

                            <!-- IDENTITAS -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1">Cabang / Territory</h6>
                                <p class="mb-0">
                                    <span id="detail_label">CIANJUR - COD</span>
                                </p>
                                <small class="text-muted">
                                    Territory: <span id="detail_territory">CIANJUR</span> |
                                    Order Type: <span id="detail_ordertype">COD</span>
                                </small>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-success fw-bold">Collected Rate</h6>
                                        <h3 class="mb-0"><span id="detail_collected_rate">94.18%</span></h3>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-danger fw-bold">Uncollected Rate</h6>
                                        <h3 class="mb-0"><span id="detail_uncollected_rate">5.82%</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- FINANCIAL -->
                            <div class="border rounded p-3 mb-3">
                                <h6 class="fw-bold mb-2">Financial Summary</h6>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Total AR</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_total_ar">Rp 1.146.475.063</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Collected Amount</td>
                                            <td class="text-end text-success fw-bold">
                                                <span id="detail_collected_amount">Rp 983.308.553</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Unconfirmed Amount</td>
                                            <td class="text-end text-warning fw-bold">
                                                <span id="detail_unconfirmed_amount">Rp 66.700.251</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Confirmed Amount</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_confirmed_amount">Rp 1.079.774.812</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Total Difference</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_difference" class="text-danger">
                                                    - Rp 117.106.685
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end of container detail chart - successRateCollectionBranch_all --}}

                        {{-- container detail chart - successCollectOverdue_branchAll --}}
                        <div class="col-12 container_detail_chart" id="successCollectOverdue_branchAll">

                            <!-- IDENTITAS -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1">Cabang / Territory</h6>
                                <p class="mb-0">
                                    <span id="detail_overdue_label">TASIKMALAYA</span>
                                </p>
                                <small class="text-muted">
                                    Territory:
                                    <span id="detail_overdue_territory">TASIKMALAYA</span>
                                </small>
                            </div>

                            <!-- RATE -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-success fw-bold">Overdue Collected Rate</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_overdue_collected_rate">0.00%</span>
                                        </h3>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-danger fw-bold">Overdue Uncollected Rate</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_overdue_uncollected_rate">100.00%</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- FINANCIAL -->
                            <div class="border rounded p-3 mb-3">
                                <h6 class="fw-bold mb-2">Overdue Financial Summary</h6>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Total AR Overdue</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_total_ar_overdue">
                                                    Rp 10.134.706.065
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Collected Amount Overdue</td>
                                            <td class="text-end text-success fw-bold">
                                                <span id="detail_collected_amount_overdue">
                                                    Rp 766.544.240
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Unconfirmed Amount Overdue</td>
                                            <td class="text-end text-warning fw-bold">
                                                <span id="detail_unconfirmed_amount_overdue">
                                                    Rp 10.134.706.065
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Confirmed Amount Overdue</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_confirmed_amount_overdue">
                                                    Rp 0
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Total Difference Overdue</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_difference_overdue" class="text-danger">
                                                    Rp 0
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- end of container detail chart - successCollectOverdue_branchAll --}}


                        {{-- container detail chart - sales TOP / driver COD --}}
                        <div class="col-12 container_detail_chart" id="badCollectionSalesTOPCOD">

                            <!-- IDENTITAS -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1">Sales / Driver</h6>
                                <p class="mb-0">
                                    <span id="detail_sales_label">unknown</span>
                                </p>
                                <small class="text-muted">
                                    Order Type:
                                    <span id="detail_sales_ordertype">TOP</span> |
                                    Avg Days Late:
                                    <span id="detail_avg_days_late">7</span> hari
                                </small>
                            </div>

                            <!-- RATE -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-success fw-bold">Confirmed Amount</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_collected_amount">
                                                Rp 81.662.469.269
                                            </span>
                                        </h3>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-warning fw-bold">Unconfirmed Amount</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_uncollected_amount">
                                                Rp 17.436.040.470
                                            </span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- FINANCIAL -->
                            <div class="border rounded p-3 mb-3">
                                <h6 class="fw-bold mb-2">Financial Summary</h6>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Total AR</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_total_ar">
                                                    Rp 99.098.509.739
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Total Difference</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_difference" class="text-success">
                                                    Rp 0
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        {{-- end of container detail chart - sales TOP / driver COD --}}

                        {{-- container detail chart - customer --}}
                        <div class="col-12 container_detail_chart" id="badCollectionCustomer_all">

                            <!-- IDENTITAS -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1">Customer</h6>
                                <p class="mb-0">
                                    <span id="detail_customer_label">
                                        PT. INDOMARCO PRISMATAMA
                                    </span>
                                </p>
                                <small class="text-muted">
                                    Customer Code:
                                    <span id="detail_customer_code">5810024345</span> |
                                    Total Invoice:
                                    <span id="detail_invoice_count">3.852</span>
                                </small>
                            </div>

                            <!-- HIGHLIGHT -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-success fw-bold">Collected Amount</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_collected_amount">
                                                Rp 20.969.807.159
                                            </span>
                                        </h3>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="text-warning fw-bold">Unconfirmed Amount</h6>
                                        <h3 class="mb-0">
                                            <span id="detail_uncollected_amount">
                                                Rp 5.794.746.512
                                            </span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- FINANCIAL -->
                            <div class="border rounded p-3 mb-3">
                                <h6 class="fw-bold mb-2">Financial Summary</h6>
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Total AR</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_total_ar">
                                                    Rp 26.764.553.671
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Total Difference</td>
                                            <td class="text-end fw-bold">
                                                <span id="detail_difference" class="text-success">
                                                    Rp 0
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        {{-- end of container detail chart - customer --}}


                    </div>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
