<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $setting["businessSetting"]["short_name"] }}</title>

        @if (str_contains($setting["businessSetting"]["favicon"], 'default') == true)
            <link rel="icon" href="{{ url('images/setting/default-favicon.ico') }}">
        @endif

        @if (str_contains($setting["businessSetting"]["favicon"], 'default') == false)
            <link rel="icon" href="{{  asset("storage/images/setting/".$setting["businessSetting"]["favicon"]) }}">
        @endif

        <!-- Scripts -->
        <script src="{{ asset("jquery/jquery.min.js") }}" ></script>
        <script src="{{ asset("bootstrap/bootstrap.bundle.min.js") }}"></script>
        {{-- @auth <script src="{{ asset("bootstrap-dashboard/dashboard.js") }}"></script> @endauth --}}
        @stack('onPageExtraScript')

        <!-- Fonts -->
        <link href="{{ asset("fonts/nunito.css") }}" rel="stylesheet">
        @guest <link href="{{ asset("fonts/bootstrap/bootstrap-icons.css") }}" rel="stylesheet"> @endguest
        @auth <script src="{{ asset("fonts/font awesome/js/all.js") }}"></script> @endauth

        <!-- Styles -->
        <link href="{{ asset("bootstrap/bootstrap.min.css") }}" rel="stylesheet">
        @auth <link href="{{ asset("bootstrap-dashboard/dashboard.css") }}" rel="stylesheet"> @endauth
        @stack('onPageExtraCss')

        {{--@vite(['resources/sass/app.scss', 'resources/js/app.js'])  --}}
    </head>
    <body>
        <div id="app">
            @guest
                <nav class="navbar navbar-expand-md navbar-expand-md navbar-light fixed-top shadow" style="background-color: #e3f2fd;">
                    <div class="container-lg">
                        <a class="navbar-brand" @auth href="{{ route('dashboard.index') }}" @endauth @guest href="{{ route('login') }}" @endguest>
                            {{ config('app.name') }}
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto">
                                @auth
                                    <li class="nav-item">
                                        <a class="nav-link {{(Request::is('dashboard')||(Request::is('dashboard/*'))) ? 'active':null}}" aria-current="page" href="{{route("dashboard.index")}}">Dashboard</a>
                                    </li>
                                @endauth

                            </ul>
                            <ul class="navbar-nav ms-auto">
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::is('login') ? 'active':null }}"  href="{{ route('login') }}">Login</a>
                                    </li>
                                @endif
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                            <a class="nav-link {{ Request::is('register') ? 'active':null }}"  href="{{ route('register') }}">Register</a>
                                    </li>
                                @endif

                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="container-lg" style="margin-top: 5rem;">
                    @hasSection('statusMesageSection')
                        @yield('statusMesageSection')
                    @endif

                    <div class="card border-primary">
                        <div class="card-header">@hasSection('mainCardTitle')@yield('mainCardTitle')@endif</div>
                        <div class="card-body">

                            <div class="card border-primary mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3 mb-2">
                                            <div class="card border-secondary" style="min-height: 400px !important;">
                                                <div class="card-header">Advertisement 1</div>
                                                <div class="card-body">
                                                    <ul>
                                                        <li>Advertisement 1.1</li>
                                                        <li>Advertisement 1.2</li>
                                                        <li>Advertisement 1.3</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6" style="min-height: 400px !important;">
                                            @yield('content')
                                        </div>

                                        <div class="col-lg-3 mb-2">
                                            <div class="card border-secondary" style="min-height: 400px !important;">
                                                <div class="card-header">Advertisement 2</div>
                                                <div class="card-body">
                                                    <ul>
                                                        <li>Advertisement 2.1</li>
                                                        <li>Advertisement 2.2</li>
                                                        <li>Advertisement 2.3</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-secondary">
                                <div class="card-header">Advertisement 3</div>
                                <div class="card-body">
                                    <ul>
                                        <li>Advertisement 3.1</li>
                                        <li>Advertisement 3.2</li>
                                        <li>Advertisement 3.3</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-lg">
                    <footer class="d-flex flex-wrap justify-content-between align-items-center py-2 my-2 border-top mb-2">
                        <div class="col-md-8 d-flex">
                            <span class="text-muted"><b><i>&copy;</i> {{ date("Y",strtotime(now())) }}  {{ config('app.name') }} </b>, <b><i>Server: </i> Dev</b>, <b><i>Release date: </i> 29-Jul-2022 </b>, <b><i>Version: </i>230528112022</b></span>
                        </div>

                        <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                            <li class="ms-3"><a class="text-muted" href="#"><i class="bi bi-facebook"></i></a></li>
                        </ul>
                    </footer>

                    <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1">
                        <div class="col-md-8 d-flex align-items-center">
                            <span class="text-muted"><b><i>Develop by: </i>Seikh Md Tahmid Farzan</b></span>
                        </div>
                        <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                            <li class="ms-3"><a class="text-muted" href="https://www.facebook.com/tahmid.farzan007/" target="_blank"><i class="bi bi-facebook"></i></a></li>
                            <li class="ms-3"><a class="text-muted" href="https://www.instagram.com/tfarzan007/" target="_blank"><i class="bi bi-instagram"></i></a></li>
                            <li class="ms-3"><a class="text-muted" href="https://www.linkedin.com/in/seikh-md-tahmid-farzan-540546a3/" target="_blank"><i class="bi bi-linkedin"></i></a></li>
                        </ul>
                    </footer>
                </div>
            @endguest

            @auth
                <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
                    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="{{ route("dashboard.index") }}">{{ config('app.name') }}</a>
                    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <input class="form-control form-control-dark w-100 rounded-0 border-0" type="text" placeholder="Search" aria-label="Search">
                    <div class="navbar-nav">
                        <div class="nav-item text-nowrap">
                            <a type="button" class="nav-link px-3" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                </header>

                <div class="container-fluid">
                    <div class="row">
                        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                            <div class="position-sticky pt-3 sidebar-sticky">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{(Request::is('dashboard') || (Request::is('dashboard/*'))) ? 'active' : null}}" aria-current="page" href="{{ route("dashboard.index") }}">
                                            <i class="fa-solid fa-gauge"></i>
                                            Dashboard
                                        </a>
                                    </li>

                                    @if (Auth::user()->hasUserPermission(["UMP01"]) == true)
                                        <li class="nav-item">
                                            <a class="nav-link {{(Request::is('user') || (Request::is('user/*'))) ? 'active' : null}}" aria-current="page" href="{{ route("user.index") }}">
                                                <i class="fa-solid fa-users"></i>
                                                Users
                                            </a>
                                        </li>
                                    @endif

                                    <li class="nav-item">
                                        <a class="nav-link {{(Request::is('activity-log') || (Request::is('activity-log/*')) || (Request::is('authentication-log')) || (Request::is('authentication-log/*'))) ? 'active' : null}}" data-bs-toggle="collapse" href="#logCollapseDiv" role="button" aria-expanded="false" aria-controls="logCollapseDiv">
                                            <i class="fa-sharp fa-solid fa-chart-simple"></i>
                                            Logs <i class="fa-solid fa-angle-down"></i>
                                        </a>

                                        <div class="collapse bg-light" id="logCollapseDiv">
                                            <div class="card card-body bg-light border-0 p-0 m-0 px-2 mx-2 ">
                                                <ul class="nav flex-column">
                                                    @if (Auth::user()->hasUserPermission(["ACLMP01"]) == true)
                                                        <li class="nav-item">
                                                            <a class="nav-link {{(Request::is('activity-log') || (Request::is('activity-log/*'))) ? 'active' : null}}" href="{{ route("activity.log.index") }}">
                                                                <i class="fa-solid fa-chart-simple"></i>
                                                                Activity logs
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["AULMP01"]) == true)
                                                        <li class="nav-item">
                                                            <a class="nav-link {{(Request::is('authentication-log') || (Request::is('authentication-log/*'))) ? 'active' : null}}" href="{{ route("authentication.log.index") }}">
                                                                <i class="fa-solid fa-user-clock"></i>
                                                                Authentication logs
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </li>

                                    @if (Auth::user()->hasUserPermission(["SMP01"]) == true)
                                        <li class="nav-item">
                                            <a class="nav-link {{(Request::is('setting') || (Request::is('setting/*'))) ? 'active' : null}}" href="{{ route("setting.index") }}">
                                                <i class="fa-solid fa-gear"></i>
                                                Settings
                                            </a>
                                        </li>
                                    @endif

                                    <li class="nav-item">
                                        <a class="nav-link {{(Request::is('user-permission') || (Request::is('user-permission/*')) || (Request::is('user-permission-group')) || (Request::is('user-permission-group/*'))) ? 'active' : null}}" data-bs-toggle="collapse" href="#extraModulCollapse" role="button" aria-expanded="false" aria-controls="extraModulCollapse">
                                            <i class="fa-solid fa-bolt"></i>
                                            Extra <i class="fa-solid fa-angle-down"></i>
                                        </a>

                                        <div class="collapse bg-light" id="extraModulCollapse">
                                            <div class="card card-body bg-light border-0 p-0 m-0 px-2 mx-2 ">
                                                <ul class="nav flex-column">
                                                    @if (Auth::user()->hasUserPermission(["UPMP01"]) == true)
                                                        <li class="nav-item">
                                                            <a class="nav-link {{(Request::is('user-permission') || (Request::is('user-permission/*'))) ? 'active' : null}}" href="{{ route("user.permission.index") }}">
                                                                <i class="fa-solid fa-user-shield"></i>
                                                                User permissions
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if (Auth::user()->hasUserPermission(["UPMP01"]) == true)
                                                        <li class="nav-item">
                                                            <a class="nav-link {{(Request::is('user-permission-group') || (Request::is('user-permission-group/*'))) ? 'active' : null}}" href="{{ route("user.permission.group.index") }}">
                                                                <i class="fa-solid fa-users-line"></i>
                                                                User permission groups
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                                    <span>Saved reports</span>
                                    <a class="link-secondary" href="#" aria-label="Add a new report">
                                        <span data-feather="plus-circle" class="align-text-bottom"></span>
                                    </a>
                                </h6>
                                <ul class="nav flex-column mb-2">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <span data-feather="file-text" class="align-text-bottom"></span>
                                            Current month
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <span data-feather="file-text" class="align-text-bottom"></span>
                                            Last quarter
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <span data-feather="file-text" class="align-text-bottom"></span>
                                            Social engagement
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <span data-feather="file-text" class="align-text-bottom"></span>
                                            Year-end sale
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>

                        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                                <h1 class="h2">
                                    @hasSection('mainPageName')
                                        @yield('mainPageName')
                                    @endif
                                </h1>

                                @hasSection('navBreadcrumbSection')
                                    @yield('navBreadcrumbSection')
                                @endif

                            </div>

                            @hasSection('statusMesageSection')
                                @yield('statusMesageSection')
                            @endif

                            <div class="card border-primary">
                                <div class="card-header">@hasSection('mainCardTitle') @yield('mainCardTitle') @endif</div>
                                <div class="card-body">

                                    @hasSection('authContentOne')
                                        @yield('authContentOne')

                                        <div class="card mb-3">
                                            <div class="card-body text-dark">
                                                <h5 class="card-title">Dark card title Add 1</h5>
                                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                            </div>
                                        </div>
                                    @endif

                                    @hasSection('authContentTwo')
                                        @yield('authContentTwo')

                                        <div class="card mb-3">
                                            <div class="card-body text-dark">
                                                <h5 class="card-title">Dark card title Add 2</h5>
                                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                            </div>
                                        </div>
                                    @endif

                                    @hasSection('authContentThree')
                                        @yield('authContentThree')

                                        <div class="card mb-3">
                                            <div class="card-body text-dark">
                                                <h5 class="card-title">Dark card title Add 2</h5>
                                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </main>

                        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-3">
                            <footer class="py-4 bg-light mt-auto">
                                <div class="container-fluid px-4">
                                    <div class="row mb-3">
                                        <div class="col-md-6 d-flex align-items-center mb-1">
                                            <b>
                                                <span class="mb-3 me-2 mb-md-0 text-muted lh-1">
                                                    {{ config('app.name') }} &copy; {{ date("Y",strtotime(now())) }}
                                                </span>
                                            </b>
                                        </div>

                                        <ul class="nav col-md-6 justify-content-end d-flex">
                                            <span class="text-muted"><b><i>Server: </i> Dev</b>, <b><i>Release date: </i> 29-Jul-2022 </b>, <b><i>Version: </i>155529122022</b></span>
                                        </ul>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 d-flex align-items-center mb-1">
                                            <span class="mb-3 me-2 mb-md-0 text-muted lh-1 pe-none"><b><i>Develop by: </i>Seikh Md Tahmid Farzan</b></span>
                                        </div>

                                        <div class="col-md-6 justify-content-end d-flex">
                                            <span class="text-muted"><b><i>Developer link:</i></span>
                                            <ul class="nav list-unstyled">
                                                <li class="ms-3"><a class="text-muted text-decoration-none" href="https://www.facebook.com/tahmid.farzan007/"><i class="fa-brands fa-facebook"></i></li>
                                                <li class="ms-3"><a class="text-muted text-decoration-none" href="https://www.linkedin.com/in/seikh-md-tahmid-farzan-540546a3/"><i class="fa-brands fa-linkedin"></i></li>
                                                <li class="ms-3"><a class="text-muted text-decoration-none" href="https://www.instagram.com/tfarzan007/"><i class="fa-brands fa-instagram"></i></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </footer>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="logoutModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="logoutModalLabel">Log out confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Ready to Leave?
                                Select "Logout" below if you are ready to end your current session.
                            </div>
                            <div class="modal-footer">
                                <a type="button" action="" class="btn btn-success" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Stay Log in</a>
                                <a class="btn btn-danger" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </body>
</html>
