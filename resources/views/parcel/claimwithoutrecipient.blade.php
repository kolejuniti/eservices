@extends('layouts.parcel')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="col-md-6 col-sm-6 col-12 ms-auto">
                <form method="POST" action="{{ route('parcel.claim.without.recipient') }}">
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
                @if ($start_date === null)
                @else
                <caption>Laporan yang dijana adalah bagi tarikh {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '' }} sehingga {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '' }}</caption>
                @endif
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Penerima</th>
                        <th>Pengirim</th>
                        <th>Tarikh Terima</th>
                        <th>No. Rujukan</th>
                        <th>Jenis Kurier</th>
                        <th>Saiz (RM)</th>
                        <th>COD (RM)</th>
                        <th>Catatan</th>
                        <th>Jumlah (RM)</th>
                        <th>Tarikh Ambil</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parcels as $item)
                    @if ($item->status == '2')
                    <tr class="table-success">
                    @else
                    <tr>
                    @endif
                        <form action="{{ route('parcel.claim.without.recipient.update', $item->id) }}" method="POST">
                        @csrf
                        <td></td>
                        <td class="text-uppercase">{{ $item->recipient_name ?? 'Tiada Maklumat Penerima' }}</td>
                        <td>{{ $item->sender_name ?? 'Tiada Maklumat Pengirim' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                        <td>{{ $item->tracking_number }}</td>
                        <td>{{ $item->courier_name }}</td>
                        <td>{{ $item->parcel_size }}&nbsp;({{ number_format($item->amount,2) }})</td>
                        <td class="text-center">{{ number_format($item->cod_amount,2) }}</td>
                        <td>{{ $item->notes }}</td>
                        <td class="text-center">
                            @if ($item->status == '2')
                                {{ "0.00" }}
                            @else
                                {{ number_format($item->amount + $item->cod_amount, 2) }}   
                            @endif
                        </td>
                        <td>
                            @if ($item->status == '2')
                                {{ \Carbon\Carbon::parse($item->updated_at)->format('d-m-Y') }}
                            @else
                            <input type="date" class="form-control form-control-sm text-center" name="pickup_date" id="pickup_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            @endif
                        </td>
                        <td>
                            @if ($item->status == '2')
                            @else
                            <button class="btn btn-sm btn-success" type="submit">Simpan</button>
                            @endif
                        </td>
                        </form>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-danger">
                    <tr>
                        <td colspan="7" class="text-end">Jumlah</td>
                        <td>{{ number_format($total_cod,2) }}</td>
                        <td colspan="4"></td>
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
                        html: '<h2>Senarai Parcel</h2>'
                    }
                },
                top1End: {
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Data Masuk & Sumber'
                        },
                        {
                            extend: 'excelHtml5',
                            title: 'Data Masuk & Sumber'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Data Masuk & Sumber'
                        },
                        {
                            extend: 'print',
                            title: 'Data Masuk & Sumber'
                        }
                    ]
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