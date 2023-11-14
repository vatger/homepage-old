<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="no-js">
    <head>
        <meta name="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no">
        <meta name="lang" content="{{ app()->getLocale() }}">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        <meta name="description" content="VATSIM Germany, VATSIM, Online Flying, Flight Simulation, Flugsimulation, Germany">
        <meta name="author" content="VATSIM Germany">
        <link rel="icon" href="{{ asset('images/favicon.ico') }}">
        {{-- PAGE TITLE --}}
        <title>{{ config('app.name', 'VATSIM Germany HQ') }}</title>

        <meta name="token" content="{{ $_account ?? $_account->api_token }}">

        <!-- Scripts -->
        <script type="text/javascript">
            window.appUser = {!! json_encode(['user' => $_account ?? $_account->id]) !!};
        </script>

        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/industry/css/linearicons.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/industry/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/industry/css/magnific-popup.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/industry/css/animate.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/industry/css/main.css') }}">
        @stack('styles')
    </head>
    <body>
        @include('membership.dashboard.warning')

        <div id="app">
            <header id="header" id="home">
                <div class="container-fluid main-menu">
                    <div class="row align-items-center justify-content-between d-flex">
                      <div id="logo">
                        <a href="{{ route('home') }}"><img src="{{ asset('images/vacc_logo.png') }}" alt="" title="" height="40" /></a>
                      </div>
                      <nav id="nav-menu-container">

                        <ul class="nav-menu">
                            <li>
                                @if(Session::has('language') && Session::get('language') != 'en')
                                <a href="{{ route('language.change', ['language' => 'en']) }}">
                                    <img src="{{ asset('images/united-kingdom.svg') }}" height="20" />
                                </a>
                                @endif
                                @if(Session::has('language') && Session::get('language') != 'de')
                                <a href="{{ route('language.change', ['language' => 'de']) }}">
                                    <img src="{{ asset('images/germany.svg') }}" height="20" />
                                </a>
                                @endif
                            </li>
                            @if(Auth::check())
                            <li><a href="{{ route('membership.home') }}">@lang('navigation.dashboard')</a></li>
                            @else
                            <li><a href="{{ route('vatauth.login') }}">@lang('navigation.auth.login')</a></li>
                            @endif
                        </ul>

                      </nav>
                    </div>
                </div>
            </header>



            @yield('component')

            <footer class="footer-area">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <p>
                                <a href="https://vatsim.net" target="_blank">
                                    <img src="{{ asset('images/vatsim/500w/VATSIM_Logo_Official_500px.png') }}" class="logo_spacer">
                                </a>

                                <a href="https://vateud.net" target="_blank">
                                    <img src="{{ asset('images/vateud.png') }}" class="logo_spacer">
                                </a>
                            </p>
                            <p>
                                Copyright &copy; {{ date('Y') }}. For Flight Simulation Use Only.
                                <span class="float-right text-right">
                                    <a href="https://vatsim-germany.org/gdpr">@lang('navigation.gdpr')</a> | <a href="https://vatsim-germany.org/imprint">@lang('navigation.imprint')</a>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <script type="text/javascript" src="{{ route('assets.lang', app()->getLocale()) }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('vendor/industry/js/superfish.min.js') }}"></script>
        <script src="{{ asset('vendor/industry/js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('vendor/industry/js/jquery.counterup.min.js') }}"></script>
        <script src="{{ asset('vendor/industry/js/main.js') }}"></script>

        @stack('scripts')
    </body>
</html>
