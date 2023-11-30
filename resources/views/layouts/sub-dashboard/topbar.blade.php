<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul id="logoMiddle" class="navbar-nav ml-auto d-none" style="bottom: 15px">
        <li class="nav-item d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="nav-link position-relative" style="bottom: 15px; left: 6px;">
                <img src="{{ asset('assets/image/logo.png') }}" alt="logo" class="img-fluid" style="width: 50px;">
            </a>

            <h5 class="m-0 p-0 font-weight-bold ">
                {{ config("app.name") }}
            </h5>
        </li>
    </ul>

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="javascript:void(0)" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item logout">
            <a id="logoutButton" class="nav-link logout bg-danger" href="javascript:void(0)" role="button">
                <p class="font-weight-bold">
                    <i class="fas fa-sign-out-alt text-white mx-1"></i>
                    <span class="mx-1 text-uppercase">Logout</span>
                </p>
            </a>
        </li>
    </ul>
</nav>
