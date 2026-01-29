<div class="title_type_data">
   Tabular COH VS Bank In  
</div>
<div class="container-fluid">
    {{-- Row Table --}}
    <div class="row row_table">
        <div class="col-12">
            {{-- Table Responsive --}}
            <div class="table-responsive">
                @if (!empty($data_tabular_cohBankIn))

                <table class="table table-bordered table-striped align-middle small">
                    <thead class="table-light">
                        <tr>

                            <th>Branch Code</th>
                            <th>Branch Name</th>
                            <th>Collection Date</th>
                            <th>Transaction Type</th>

                            <th>Outstanding Amount Invoice</th>
                            <th>Nilai Input FSR / Driver</th>
                            <th>Total Confirm Collection</th>
                            <th>Diff (Cash, TF, BG) by Value</th>
                            <th>Unpaid Value</th>
                            <th>Performa Coll by Value</th>
                            <th>Diff Note</th>

                            <th>Total Doc Invoice</th>
                            <th>Total Collected Doc Invoice</th>
                            <th>Performa Coll by Inv</th>

                            <th>Total Inv TOP OD by Value</th>
                            <th>Total Paid Inv TOP OD by Value</th>
                            <th>Performa Coll AR OD by Value</th>
                            <th>Total Inv TOP OD by Doc</th>
                            <th>Paid Inv TOP OD by Doc</th>
                            <th>Performa Coll by Doc</th>

                            <th>COH</th>
                            <th>Bank In</th>
                            <th>Balance</th>
                            <th>Reason</th>
                            <th>Cash Bank Manual</th>
                            <th>Payment Performance Document</th>

                            <th>Cash Value FSR</th>
                            <th>Cash Confirm Kasir</th>
                            <th>Diff Cash</th>

                            <th>TF Value FSR</th>
                            <th>TF Confirm Kasir</th>
                            <th>Diff TF</th>

                            <th>Giro Value FSR</th>
                            <th>Giro Confirm Kasir</th>
                            <th>Diff Giro</th>
                        </tr>

                    </thead>

                    <tbody>
                        @foreach ($data_tabular_cohBankIn as $row_data)
                        <tr>

                            <td><?= $row_data['Branch Code'] ?></td>
                            <td><?= $row_data['Branch Name'] ?></td>
                            <td><?= $row_data['Collection Date'] ?></td>
                            <td><?= $row_data['Transaction Type'] ?></td>

                            <td><?= number_format($row_data['Outstanding Amount Invoice'],0,',','.') ?></td>
                            <td><?= number_format($row_data['Nilai Input FSR / Driver'],0,',','.') ?></td>
                            <td><?= number_format($row_data['Total Confirm Collection'],0,',','.') ?></td>
                            <td><?= number_format($row_data['Diff (Cash, TF, BG) by Value'],0,',','.') ?></td>
                            <td><?= number_format($row_data['Unpaid Value'],0,',','.') ?></td>
                            <td><?= $row_data['Performa Coll by Value'] ?></td>
                            <td><?= $row_data['Diff Note (Cash, TF, BG)'] ?></td>

                            <td><?= $row_data['Total Doc Invoice'] ?></td>
                            <td><?= $row_data['Total Collected Doc Invoice'] ?></td>
                            <td><?= $row_data['Performa Coll by Inv'] ?></td>

                            <td><?= $row_data['Total Inv TOP OD by Value'] ?></td>
                            <td><?= $row_data['Total Paid Inv TOP OD by Value'] ?></td>
                            <td><?= $row_data['Performa Coll AR OD by Value'] ?? '-' ?></td>
                            <td><?= $row_data['Total Inv TOP OD by Doc'] ?></td>
                            <td><?= $row_data['Paid Inv TOP OD by Doc'] ?></td>
                            <td><?= $row_data['Performa Coll by Doc'] ?></td>

                            <td><?= $row_data['COH'] ?></td>
                            <td><?= $row_data['Bank In'] ?></td>
                            <td><?= $row_data['Balance'] ?></td>
                            <td><?= $row_data['Reason'] ?></td>
                            <td><?= $row_data['Cash Bank Manual'] ?></td>
                            <td><?= $row_data['Payment_Performance_Document'] ?></td>

                            <td><?= number_format($row_data['Cash Value FSR/Driver'],0,',','.') ?></td>
                            <td><?= $row_data['Cash Confirm Kasir'] ?></td>
                            <td><?= $row_data['Diff Cash'] ?></td>

                            <td><?= $row_data['TF Value FSR/Driver'] ?></td>
                            <td><?= $row_data['TF Confirm Kasir'] ?></td>
                            <td><?= $row_data['Diff TF'] ?></td>

                            <td><?= $row_data['Giro Value FSR/Driver'] ?></td>
                            <td><?= $row_data['Giro Confirm Kasir'] ?></td>
                            <td><?= $row_data['Diff Giro'] ?></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @else

                <div class="alert alert-danger py-2 mb-3">
                    <strong> Data Not Founded </strong> 
                </div>

                @endif
            </div>
            {{-- End Of Table Responsive --}}

            @if ( !empty( $data_tabular_cohBankIn ) )

            <div class="float-end small">{{ $data_paginator_cohBankIn->links() }}</div>

            @endif

        </div>
    </div>
    {{-- End Of Row Table --}}

</div>