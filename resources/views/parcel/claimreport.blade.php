@extends('layouts.parcel')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="col-md-8 col-sm-10 col-12 ms-auto">
                <form method="POST" action="{{ route('parcel.claim.reports') }}">
                @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Tarikh Mula</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') ?? $start_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Tarikh Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') ?? $end_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Telah Dituntut</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Belum Dituntut</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button class="btn btn-warning w-100" type="submit">Cari</button>
                        </div>
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
                        <th>No. Siri</th>
                        <th>No. Rujukan</th>
                        <th>Jenis Kurier</th>
                        <th>Saiz</th>
                        <th>COD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parcels as $item)
                    <tr>
                        <td></td>
                        <td class="text-center">{{ $item->serial_number }}</td>
                        <td class="text-center">{{ $item->tracking_number }}</td>
                        <td class="text-center">{{ $item->courier_name }}</td>
                        <td class="text-center">{{ $item->parcel_size }}</td>
                        <td class="text-center">
                            <input type="checkbox" {{ $item->cod_id == 1 ? 'checked' : '' }} disabled>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
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
