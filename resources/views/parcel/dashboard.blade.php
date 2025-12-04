@extends('layouts.parcel')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">{{ __('Parcel Services - Dashboard') }}</div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Statistics Cards -->
                        <div class="col-md-4">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Parcel Hari Ini</h5>
                                    <h2 class="mb-0">{{ $totalParcelsToday }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">Parcel Minggu Ini</h5>
                                    <h2 class="mb-0">{{ $totalParcelsThisWeek }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-secondary">
                                <div class="card-body">
                                    <h5 class="card-title">Parcel Bulan Ini</h5>
                                    <h2 class="mb-0">{{ $totalParcelsThisMonth }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Belum Dituntut</h5>
                                    <h2 class="mb-0">{{ $unclaimedParcels }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Telah Dituntut</h5>
                                    <h2 class="mb-0">{{ $claimedParcels }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-danger">
                                <div class="card-body">
                                    <h5 class="card-title">Jumlah COD Belum Dituntut</h5>
                                    <h2 class="mb-0">RM {{ number_format($pendingCOD, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Parcels -->
            <div class="card">
                <div class="card-header">Parcel Terkini (10 Terbaru)</div>
                <div class="card-body">
                    @if($recentParcels->isEmpty())
                        <p class="text-muted">Tiada parcel didaftarkan lagi.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarikh</th>
                                        <th>Penerima</th>
                                        <th>Kurier</th>
                                        <th>Saiz</th>
                                        <th>COD (RM)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentParcels as $parcel)
                                    <tr>
                                        <td>{{ $parcel->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($parcel->ic)
                                                <span class="badge bg-info">IC: {{ $parcel->ic }}</span>
                                            @else
                                                {{ $parcel->recipient_name ?? '-' }}
                                            @endif
                                        </td>
                                        <td>{{ $parcel->courier->name }}</td>
                                        <td>{{ $parcel->parcel_size }}</td>
                                        <td>{{ number_format($parcel->cod_amount, 2) }}</td>
                                        <td>
                                            @if($parcel->status == 1)
                                                <span class="badge bg-warning">Belum Dituntut</span>
                                            @else
                                                <span class="badge bg-success">Telah Dituntut</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
