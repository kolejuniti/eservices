@extends('layouts.parcel')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
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
            <div class="card mb-2">
                <div class="card-header">{{ __('Tuntutan Parcel') }}</div>
                <form action="{{ route('parcel.claim.with.recipient') }}" method="GET">
                <div class="card-body">
                    <div class="col-12 col-md-8 col-sm-8 offset-md-2 offset-sm-2">
                        <div class="input-group mb-3">
                            <input type="text" name="search" id="search" class="form-control" placeholder="Nama / No. Kad Pengenalan" >
                            <button class="btn btn-warning" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            @if($search)
                @if($recipients->isEmpty())
                    <p>Tiada data penerima</p>
                @else
                <div class="table-responsive">
                    <table id="myTable" class="table table-bordered small table-sm text-center">
                        <thead class="table-dark">
                            <tr>
                                <th colspan="3"></th>
                                <th colspan="2" class="text-center">Parcel Yang Belum Dituntut</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Nama Penerima</th>
                                <th>No. Kad Pengenalan</th>
                                <th>Bil. Parcel</th>
                                <th>Bil. Parcel (COD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recipients as $recipient)
                            <tr>
                                <td></td>
                                <td><a href="{{ route('recipient.parcel.details', ['ic' => $recipient->ic]) }}" class="btn btn-sm btn-link" target="_blank">{{ $recipient->recipient_name }}</a></td>
                                <td class="text-center">{{ $recipient->ic }}</td>
                                <td class="text-center">{{ $recipient->total_parcels }}</td>
                                <td class="text-center">{{ $recipient->cod_parcels }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            @endif
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
                        html: '<h2>Senarai Penerima Parcel</h2>'
                    }
                },
                top1End: {
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Senarai Penerima Parcel'
                        },
                        {
                            extend: 'excelHtml5',
                            title: 'Senarai Penerima Parcel'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Senarai Penerima Parcel'
                        },
                        {
                            extend: 'print',
                            title: 'Senarai Penerima Parcel'
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
