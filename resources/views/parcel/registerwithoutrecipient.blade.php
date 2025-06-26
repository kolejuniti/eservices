@extends('layouts.parcel')

@section('content')
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
            <div class="card">
                <div class="card-header">{{ __('Pendaftaran Parcel') }}</div>
                <form method="POST" action="{{ route('parcel.register.without.recipient') }}">
                @csrf
                <div class="card-body">
                    <div class="row g-2 mb-2 row-cols-1">
                        <div class="col-md-3 col-sm-3 col-3">
                            <label for="">Nama Penerima</label>
                        </div>
                        <div class="col-md-9 col-md-9 col-9">
                            <input name="recepient_name" id="recepient_name" type="text" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row g-2 mb-2 row-cols-1">
                        <div class="col-md-3 col-sm-3 col-3">
                            <label for="">Nama Pengirim</label>
                        </div>
                        <div class="col-md-9 col-md-9 col-9">
                            <input name="sender_name" id="sender_name" type="text" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 mb-2">
                        <label for="" class="fw-bold">Maklumat Parcel</label>
                    </div>
                    <div class="row g-2 mb-2 row-cols-1">
                        <div class="col-md-3 col-sm-3 col-3">
                            <label for="">Jenis Kurier</label>
                        </div>
                        <div class="col-md-3 col-md-3 col-3">
                            <select name="courier" id="courier" class="form-control form-control-sm" required>
                                <option value="" disabled selected>Senarai Kurier</option>
                                @foreach ($couriers as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-3 col-3">
                            <label for="">No. Rujukan</label>
                        </div>
                        <div class="col-md-3 col-md-3 col-3">
                            <input name="tracking_number" id="tracking_number" type="text" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-2 row-cols-1">
                        <div class="col-md-3 col-sm-3 col-3">
                            <label for="">Saiz Parcel</label>
                        </div>
                        <div class="col-md-3 col-md-3 col-3">
                            <select name="parcel_size" id="parcel_size" class="form-control form-control-sm" required>
                                <option value="" disabled selected>Senarai Saiz</option>
                                <option value="Kecil">Kecil - (RM0.00)</option>
                                <option value="Sederhana">Sederhana - (RM1.00)</option>
                                <option value="Besar">Besar - (RM2.00)</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-3 col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                                <label class="form-check-label" for="checkDefault">COD</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-md-3 col-3" id="codAmountContainer" style="display: none;">
                            <input name="cod_amount" id="cod_amount" type="text" class="form-control form-control-sm" placeholder="Jumlah COD (RM)">
                        </div>
                    </div>
                    <div class="row g-2 mb-2 row-cols-1">
                        <div class="col-md-3 col-sm-3 col-3">
                            <label for="">Catatan</label>
                        </div>
                        <div class="col-md-9 col-md-9 col-9">
                            <textarea name="notes" id="notes" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-sm btn-primary" type="submit">Daftar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const checkbox = document.getElementById('checkDefault');
    const codAmountContainer = document.getElementById('codAmountContainer');
    const codAmountInput = document.getElementById('cod_amount');

    checkbox.addEventListener('change', function () {
        if (this.checked) {
            codAmountContainer.style.display = 'block';
            codAmountInput.setAttribute('required', 'required');
        } else {
            codAmountContainer.style.display = 'none';
            codAmountInput.removeAttribute('required');
            codAmountInput.value = ''; // Clear the input when hiding, optional
        }
    });
</script>
@endsection
