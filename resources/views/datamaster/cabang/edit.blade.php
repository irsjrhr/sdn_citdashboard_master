<form action="{{ route('cabang.update', Crypt::encrypt($cabang->kode_cabang)) }}" id="formeditCabang" method="POST">
    @csrf
    @method('PUT')
    <x-input-with-icon icon="ti ti-barcode" label="Kode Cabang" name="kode_cabang" value="{{ $cabang->kode_cabang }}" readonly />
    <x-input-with-icon icon="ti ti-file-text" label="Nama Cabang" name="nama_cabang" value="{{ $cabang->nama_cabang }}" />
    <x-input-with-icon icon="ti ti-map-pin" label="Alamat Cabang" name="alamat_cabang" value="{{ $cabang->alamat_cabang }}" />
    <x-input-with-icon icon="ti ti-phone" label="Telepon Cabang" name="telepon_cabang" value="{{ $cabang->telepon_cabang }}" />
    <x-input-with-icon icon="ti ti-map-pin" label="Lokasi Cabang" name="lokasi_cabang" value="{{ $cabang->lokasi_cabang }}" />
    <div class="form-group mb-3">
        <label for="kode_region" class="form-label">Region</label>
        <select name="kode_region" id="kode_region" class="form-select">
            <option value="">-- Pilih Region --</option>
            @foreach($regions as $region)
                <option value="{{ $region->kode_region }}"
                    {{ (old('kode_region', $cabang->kode_region) == $region->kode_region) ? 'selected' : '' }}>
                    {{ $region->kode_region }} - {{ $region->nama_region }}
                </option>
            @endforeach
        </select>
        @error('kode_region')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <x-input-with-icon icon="ti ti-access-point" label="Radius Cabang" name="radius_cabang" value="{{ $cabang->radius_cabang }}" />
    <div class="form-group">
        <button class="btn btn-primary w-100" type="submit">
            <ion-icon name="send-outline" class="me-1"></ion-icon>
            Update
        </button>
    </div>
</form>

<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/cabang/edit.js') }}"></script>
