<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no">
    <meta name="lang" content="{{ app()->getLocale() }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- API Token -->
    <meta name="token" content="{{ $_account->api_token }}">
    <meta name="description" content="VATSIM Germany, VATSIM, Online Flying, Flight Simulation, Flugsimulation, Germany">
    <meta name="author" content="VATSIM Germany">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    {{-- PAGE TITLE --}}
    <title>{{ config('app.name', 'VATSIM Germany HQ') }}</title>

    <!-- Scripts -->
    <script type="text/javascript">
        window.appUser = {!! json_encode(['user' => $_account->id]) !!};
    </script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
    <div id="app" class="wrapper">
        @include('shared.navigation')

        <!-- Left Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-3">
            <!-- Logo -->
            <a class="brand-link" href="{{ route('home') }}">
                <img class="brand-image-xs logo-xl" src="{{ asset('images/vacc_logo_white.png') }}">
                <img class="brand-image-xs logo-xs" src="{{ asset('images/vacc_logo_white_small.png') }}">
                <span class="brand-test font-weight-light">&nbsp;</span>
            </a>

            <div class="sidebar">
                <!-- User Menu -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="{{ $_account->statsLink }}" target="_blank" class="d-block">{{ $_account->username }}</a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column nav-flat text-sm nav-compact" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <router-link to="/membership" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>@lang('navigation.dashboard')</p>
                            </router-link>
                        </li>
                        <li class="nav-header">
                            <p>@lang('navigation.pilots.pilots')</p>
                        </li>
                        <li class="nav-item">
                            <router-link to="/pilots/aerodromes" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-plane"></i>
                                <p>@lang('navigation.pilots.aerodromes')</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/pilots/weather" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-wind"></i>
                                <p>@lang('navigation.pilots.weather')</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/pilots/livemap" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-map"></i>
                                <p>@lang('navigation.pilots.livemap')</p>
                            </router-link>
                        </li>
                        <li class="nav-header">
                            <p>@lang('navigation.controllers.controllers')</p>
                        </li>
                        <li class="nav-item">
                            <router-link to="/membership/booking" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fa fa-wifi"></i>
                                <p>@lang('navigation.controllers.atcBooking')</p>
                            </router-link>
                        </li>
                        {{-- <li class="nav-item">
                            <router-link to="/membership/atd/training" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fa fa-wifi"></i>
                                <p>@lang('navigation.controllers.atd.training')</p>
                            </router-link>
                        </li> --}}
                        <li class="nav-header">
                            <p>@lang('navigation.regionalgroups.regionalgroups')</p>
                        </li>
                        <li class="nav-item">
                            <router-link to="/membership/regionalgrouprequests" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-question"></i>
                                <p>@lang('navigation.regionalgroups.requests')</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/membership/regionalgroups" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-users"></i>
                                <p>@lang('navigation.regionalgroups.mygroups')</p>
                            </router-link>
                        </li>
                        <li class="nav-header">
                            <p>@lang('event.event')</p>
                        </li>
                        <li class="nav-item">
                            <router-link to="/membership/events/routes" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-route"></i>
                                <p>@lang('event.eventroutes')</p>
                            </router-link>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        @yield('component')

        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Systemstatus</h5>
                <hr class="mb-2"></hr>
                <div id="sysMessages">
                    {{-- This is where status messages will be written --}}
                </div>
            </div>
        </aside>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                For Flight Simulation Use Only! <b>Version {{ config('app.version') }}</b> | <a href="{{ route('dataprotection.gdpr') }}">@lang('navigation.gdpr')</a> | <a href="{{ route('dataprotection.imprint') }}">@lang('navigation.imprint')</a>
            </div>
            <strong>Copyright &copy; {{ date("Y") }}, <a href="{{ route('home') }}">VATSIM Germany</a>.</strong> All rights reserved.
        </footer>
    </div>

    <script type="text/javascript" src="{{ route('assets.lang', app()->getLocale()) }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script type="text/javascript">
        if ('{{ app()->getLocale() }}' == 'de') {
            $.extend(true, $.fn.dataTable.defaults, {
                "language": {
                    "url": "/vendor/datatables/de_DE.json"
                }
            });
        };
    </script>
</body>
</html>
