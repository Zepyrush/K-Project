<div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
    <div class="image mr-1">
        @php
            $fileName = Auth::user()->foto;

            (file_exists('assets/image/users/' . $fileName)) ?
                $avatar = asset('assets/image/users/' . $fileName) :
                $avatar = asset('assets/image/avatar.png');
        @endphp
        <img id="sideProfile" src="{{ $avatar }}" class="img-circle elevation-2" alt="User Image">
    </div>

    <div class="info">
        @php
            (strlen(Auth::user()->name) > 16) ?
                $name = substr(Auth::user()->name, 0, 16) . '...' :
                $name = Auth::user()->name;
        @endphp
        <a id="userName" href="{{ route('profile.index') }}" class="d-block">{{ $name }}</a>
        <small class="text-muted">{{ Auth::user()->no_induk }}</small>
    </div>
</div>
