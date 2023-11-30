@php
    $mb = url()->current() != route('profile.index') ? 'mb-2' : '';
    $cardOutline = url()->current() == route('profile.index') ? 'card-primary card-outline' : '';
@endphp

<div class="card card-primary card-outline {{ $mb }}">
    <div class="card-header p-1">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="#profile" data-toggle="tab">
                    Data Anda
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#password" data-toggle="tab">
                    Password
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card {{ $cardOutline }}">
    <div class="card-body">
        <div class="tab-content">
            {{-- PROFILE --}}
            @include('dashboard._profile._data-info')

            {{-- PASSWORD --}}
            @include('dashboard._profile._ganti-password')
        </div>
    </div>
</div>
