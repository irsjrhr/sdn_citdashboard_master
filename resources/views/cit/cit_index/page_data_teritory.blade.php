
<div class="title_type_data">
    Summary Per Territory 
</div>

<div class="container-fluid">
    {{-- Row Table --}}
    <div class="row row_table">
        <div class="col-12">
            <div class="table-responsive">
                @if ( !empty($data_view_teritory) )

                <table class="table table-bordered table-striped align-middle small">
                    <thead class="table-light">
                        <tr>
                            <td>Territory ID</td>
                            <td>Territory Name</td>
                            <td>Order Type</td>
                            <td>Invoice Count</td>
                            <td>Total AR</td>
                            <td>Collected Amount</td>
                            <td>Confirmed Amount</td>
                            <td>Total Difference</td>
                            <td>Outstanding AR Amount</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_view_teritory as $row_data)
                        <tr>
                            <td><?= $row_data['territoryid']; ?></td>
                            <td><?= $row_data['territoryname']; ?></td>
                            <td><?= $row_data['ordertype']; ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['invoice_count']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['total_ar']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['collected_amount']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['confirmed_amount']); ?></td>
                            <td class="kolom_angka"><?= number_format($row_data['total_difference']); ?></td>
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
{{--     <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td> S </td>
                    <td> S </td>
                    <td> S </td>
                    <td> S </td>
                </tr>
                @for ($i = 0; $i < 5; $i++)
                <tr>
                    <td>s</td>
                    <td>s</td>
                    <td>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12" style="border-bottom: 3px solid #000;">
                                    s
                                </div>
                                <div class="col-12">
                                    s
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12" style="border-bottom: 3px solid #000;">
                                    s
                                </div>
                                <div class="col-12">
                                    s
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endfor

                
            </table>
        </div>
    </div>
    --}}


</div>




