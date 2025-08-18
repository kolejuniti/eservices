@extends('layouts.parcel')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="col-md-6 col-sm-6 col-12 ms-auto">
                <form method="POST" action="{{ route('parcel.claim.reports') }}">
                @csrf
                    <div class="input-group mb-3">
                        <button class="btn btn-secondary" disabled>Tarikh</button>
                        <input type="date" class="form-control" name="start_date" required>
                        <button class="btn btn-secondary" disabled>-</button>
                        <input type="date" class="form-control" name="end_date" required>
                        <button class="btn btn-warning" type="submit">Cari</button>
                    </div>
                </form>
            </div>
            @if(session('alert'))
                <div class="alert alert-danger">
                    {{ session('alert') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
        <div class="table-responsive">
            <table id="myTable" class="table table-bordered small table-sm text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tarikh Tuntutan</th>
                        <th>No. Rujukan</th>
                        <th>No. Siri</th>
                        <th>Jenis Kurier</th>
                        <th>Saiz</th>
                        <th>Amaun (RM)</th>
                        <th>COD (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parcels as $item)
                    <tr>
                        <td></td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->updated_at)->format('d-m-Y') }}</td>
                        <td class="text-center">{{ $item->tracking_number }}</td>
                        <td class="text-center">{{ $item->serial_number }}</td>
                        <td class="text-center">{{ $item->courier_name }}</td>
                        <td class="text-center">{{ $item->parcel_size }}</td>
                        <td class="text-center">{{ number_format($item->amount,2) }}</td>
                        <td class="text-center">{{ number_format($item->cod_amount,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-danger">
                        <th colspan="5" class="text-end"></th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">{{ number_format($parcels->sum('amount'), 2) }}</th>
                        <th class="text-center">{{ number_format($parcels->sum('cod_amount'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.js"></script>

<script>
    $(document).ready(function() {
        var t = $('#myTable').DataTable({
        columnDefs: [
            {
                targets: ['_all'],
                className: 'dt-head-center'
            }
        ],
        layout: {
                top1Start: {
                    div: {
                        html: '<h2>Laporan Tuntutan Parcel</h2>'
                    }
                },
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            }
        });
        t.on('order.dt search.dt', function () {
            let i = 1;
        
            t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                this.data(i++);
            });
        }).draw();
    });
</script>
@endsection
