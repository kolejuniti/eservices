@extends('layouts.parcel')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Validation Errors:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Pendaftaran Parcel') }}</div>
                <div class="card-body">
                    <div class="col-md-12 col-sm-12 mb-2">
                        <label for="" class="fw-bold">Carian Penerima</label>
                    </div>
                    <div class="row g-2 mb-2 row-cols-1">
                        <div class="col-md-3 col-sm-3 col-3">
                            <label for="">Nama / No. KP / No. Matriks</label>
                        </div>
                        <div class="col-md-3 col-md-3 col-3">
                            <input name="search" id="search" type="text" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-md-6 col-6">
                            <select name="ic" id="ic" class="form-control form-control-sm">
                                <option value="">Senarai Penerima</option>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('parcel.register.with.recipient') }}">
                    @csrf
                        <div class="col-md-12 col-sm-12 mb-2">
                            <label for="" class="fw-bold">Maklumat Penerima</label>
                        </div>
                        <div class="row g-2 mb-2 row-cols-1">
                            <div class="col-md-3 col-sm-3 col-3">
                                <label for="">Nama</label>
                            </div>
                            <div class="col-md-9 col-md-9 col-9">
                                <label for="recipient_name" id="recipient_name"></label>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 row-cols-1">
                            <div class="col-md-3 col-sm-3 col-3">
                                <label for="">No. KP</label>
                            </div>
                            <div class="col-md-3 col-md-3 col-3">
                                <input type="text" name="ic" id="recipient_ic" value="" class="form-control form-control-sm" readonly>
                            </div>
                            <div class="col-md-3 col-sm-3 col-3">
                                <label for="">No. Matriks / Staf</label>
                            </div>
                            <div class="col-md-3 col-md-3 col-3">
                                <input type="text" name="id" id="recipient_id" value="" class="form-control form-control-sm" readonly>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 row-cols-1">
                            <div class="col-md-3 col-sm-3 col-3">
                                <label for="">No. Telefon</label>
                            </div>
                            <div class="col-md-3 col-md-3 col-3">
                                <input type="text" name="phone" id="recipient_phone" value="" class="form-control form-control-sm" readonly>
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
                                <label for="">No. Siri</label>
                            </div>
                            <div class="col-md-3 col-md-3 col-3">
                                <input name="serial_number" id="serial_number" type="text" class="form-control form-control-sm" required>
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
                                    <input class="form-check-input" type="checkbox" value="1" id="checkDefault" name="cod">
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
<script>
    document.getElementById('search').addEventListener('keydown', function () {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent the default form submission behavior

            const search = this.value;

            // Make an AJAX request to fetch recipients matching the search
            fetch(`{{ route('parcel.recipient.search') }}?search=${search}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('ic');
                    select.innerHTML = ''; // Clear existing options
                    
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Senarai Penerima';
                    defaultOption.selected = true; // Ensure it's selected initially
                    defaultOption.disabled = true; // Make it non-selectable
                    select.appendChild(defaultOption);

                    // Populate the dropdown with new options
                    data.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.ic; // IC is used as the value
                        option.textContent = student.name; // Name is displayed in the dropdown
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching students:', error));
        }
    });

    // Trigger AJAX request when an IC is selected from the dropdown
    document.getElementById('ic').addEventListener('change', function () {
        const ic = this.value; // Get the selected IC value

        if (ic) {
            // Send an AJAX request to get the recipient details
            fetch(`{{ route('parcel.recipient.detail') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ ic })
            })
                .then(response => response.json())
                .then(data => {
                    const recipientNameLabel = document.getElementById('recipient_name');
                    const recipientICInput = document.getElementById('recipient_ic');
                    const recipientIDInput = document.getElementById('recipient_id');
                    const recipientPhoneInput = document.getElementById('recipient_phone');

                    if (data.recipient) {
                        recipientNameLabel.textContent = data.recipient.name || '-';
                        recipientICInput.value = data.recipient.ic || '';
                        recipientIDInput.value = data.recipient.id || '';
                        recipientPhoneInput.value = data.recipient.phone || '';
                    } else {
                        recipientNameLabel.textContent = 'No recipient found';
                        recipientICInput.value = '';
                        recipientIDInput.value = '';
                        recipientPhoneInput.value = '';
                    }
                })
                .catch(error => console.error('Error fetching recipient details:', error));
        } else {
            // Clear the recipient name if no IC is selected
            document.getElementById('recipient_name').textContent = '';
            document.getElementById('recipient_ic').textContent = '';
        }
    });
</script>
@endsection
