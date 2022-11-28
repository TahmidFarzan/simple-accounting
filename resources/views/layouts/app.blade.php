<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset("jquery/jquery.min.js") }}" ></script>
    <script src="{{ asset("bootstrap/bootstrap.bundle.min.js") }}"></script>
    @auth <script src="{{ asset("bootstrap-dashboard/dashboard.js") }}"></script> @endauth
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
                        SA  {{-- {{ (strlen($setting["businessSetting"]["short_name"])==0) ? "GBA" : $setting["applicationSetting"]["short_name"] }} --}}
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
    </div>
</body>
</html>
