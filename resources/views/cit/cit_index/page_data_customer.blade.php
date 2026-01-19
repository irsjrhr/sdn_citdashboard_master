
<div class="title_type_data">
    Customer Report 
</div>

<div class="container-fluid">
    {{-- Row Table --}}
    <div class="row row_table">
        <div class="col-12">
            <div class="table-responsive">
                @if (!empty($data_view_customer))
                <table class="table table-bordered table-striped align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Territory ID</th>
                            <th>Customer Code</th>
                            <th>Customer Name</th>
                            <th>Invoice Count</th>
                            <th>Total Difference</th>
                            <th>Total AR</th>
                            <th>Collected Amount</th>
                            <th>Confirmed Amount</th>
                            <th>Unconfirmed Amount</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($data_view_customer as $row_data)
                        <tr>
                            <td><?= $row_data['territoryid']; ?></td>
                            <td><?= $row_data['customercode']; ?></td>
                            <td><?= $row_data['customername']; ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['invoice_count']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['total_difference']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['total_ar']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['collected_amount']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['confirmed_amount']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['unconfirmed_amount']); ?></td>
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
        </div>
    </div>
    {{-- End Of Row Table --}}


</div>


