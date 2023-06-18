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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.2/b-colvis-1.6.2/b-html5-1.6.2/b-print-1.6.2/r-2.2.4/datatables.min.css"/>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
    <div id="app" class="wrapper">
        <!-- Navigation -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
            <!-- Left Side -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <!-- Right Side -->
            <ul class="navbar-nav ml-auto">
                @can('administration')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('administration.home') }}">
                        <i class="fas fa-cogs"></i> Administration
                    </a>
                </li>
                @endcan
                @if(count($_account->unreadNotifications) > 0)
                <li class="nav-item dropdown">
                    <router-link to="/membership/notifications" :class="'nav-link'" exact>
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">{{ count($_account->unreadNotifications) }}</span>
                    </router-link>
                </li>
                @endif
                <li class="nav-item">
                    @if(Session::has('language') && Session::get('language') != 'en')
                    <a class="nav-link" rel="tooltip" title="English" data-placement="bottom" href="{{ route('language.change', ['language' => 'en']) }}">
                        <img src="{{ asset('images/united-kingdom.svg') }}" height="20" />
                    </a>
                    @endif
                    @if(Session::has('language') && Session::get('language') != 'de')
                    <a class="nav-link" rel="tooltip" title="Deutsch" data-placement="bottom" href="{{ route('language.change', ['language' => 'de']) }}">
                        <img src="{{ asset('images/germany.svg') }}" height="20" />
                    </a>
                    @endif
                </li>
                <li class="nav-item">
                    <a href="{{ route('membership.home') }}" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>Verlassen
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('vatauth.logout') }}">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i class="fas fa-th-large"></i></a>
                </li>
            </ul>
        </nav>

        <!-- Left Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-3">
            <!-- Logo -->
            <a class="brand-link" href="{{ route('home') }}">
                <img class="brand-image-xs logo-xl" src="{{ asset('images/vacc_logo_white.png') }}">
                <img class="brand-image-xs logo-xs" src="{{ asset('images/vacc_logo_white_small.png') }}">
                <span class="brand-test font-weight-light">&nbsp;</span>
            </a>

            <div class="sidebar">
                <nav class="mt-3">
                    <ul class="nav nav-pills nav-sidebar flex-column nav-flat text-sm nav-compact" data-widget="treeview" role="menu" data-accordion="false">
                        @can('administration.membership')
                        <li class="nav-header">
                            Mitgliederverwaltung
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/membership" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-user"></i>
                                <p>Mitgliederverwaltung</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/group" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-users"></i>
                                <p>Gruppenverwaltung</p>
                            </router-link>
                        </li>
                        @endcan
                        @can('administration.forumgroups')
                        <li class="nav-header">
                            Forum
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/forum/group" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-users"></i>
                                <p>Forengruppenverwaltung</p>
                            </router-link>
                        </li>
                        @endcan
                        @can('administration.atd')
                        <li class="nav-header">
                            ATD Verwaltung
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/atd" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-chalkboard-teacher"></i>
                                <p>ATD Dashboard</p>
                            </router-link>
                        </li>
                        @endcan

                        <li class="nav-header">
                            Events
                        </li>
                        {{-- <li class="nav-item">
                            <router-link to="/administration" :class="'nav-link disabled'" active-class="" exact>
                                <i class="nav-icon fas fa-box"></i>
                                <p><del>Sammelbuchungen</del></p>
                            </router-link>
                        </li> --}}
                        @can('administration.event.routes')
                        <li class="nav-item">
                            <router-link to="/administration/events/routes" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-route"></i>
                                <p>Eventroutes</p>
                            </router-link>
                        </li>
                        @endcan
                        <li class="nav-header">
                            EuroScope
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/euroscope" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fab fa-avianex"></i>
                                <p>Scenario Generator</p>
                            </router-link>
                        </li>
                        @anypermission('administration.navigation', 'administration.navigation.rg')
                        <li class="nav-header">
                            Navigationsdepartement
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/station" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-wifi"></i>
                                <p>ATS Stationsverwaltung</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/aerodrome" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fab fa-avianex"></i>
                                <p>Aerodromeverwaltung</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/chart" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-map"></i>
                                <p>Chartverwaltung</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/navaids" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-map"></i>
                                <p>Navaidverwaltung</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/glg" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-map"></i>
                                <p>Groundlayout Generator</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/aip" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-map"></i>
                                <p>AIP Generator</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/sectorcombine" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-map"></i>
                                <p>SCT Combiner</p>
                            </router-link>
                        </li>
                        @endanypermission
                        @anypermission('administration.images.upload', 'administration.images.manage')
                        <li class="nav-header">
                            Bildverwaltung
                        </li>
                        @can('administration.images.manage')
                        <li class="nav-item">
                            <router-link to="/administration/images/manager" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-users"></i>
                                <p>Bildverwaltung</p>
                            </router-link>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <router-link to="/administration/images/uploader" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-users"></i>
                                <p>Bilder Hochladen</p>
                            </router-link>
                        </li>
                        @endanypermission
                        @anypermission('administration.regionalgroup', 'administration.regionalgroup.rg')
                        <li class="nav-header">
                            Regionalgruppenverwaltung
                        </li>
                        <li class="nav-item">
                            <router-link to="/administration/regionalgroups" :class="'nav-link'" active-class="active" exact>
                                <i class="nav-icon fas fa-users"></i>
                                <p>Regionalgruppen</p>
                            </router-link>
                        </li>
                        @endanypermission
                        @can('administration')
                        <li class="nav-header">
                            User Services
                        </li>
                            @can('administration.services.gitlab')
                                    <li class="nav-item">
                                        <router-link to="/administration/services/gitlab" :class="'nav-link'" active-class="active" exact>
                                            <i class="nav-icon fas fa-users"></i>
                                            <p>Gitlab</p>
                                        </router-link>
                                    </li>
                            @endcan
                        @endcan
                        @can('administration')
                        <li class="nav-header">
                            Sprachverwaltung
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('languages.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-globe"></i>
                                <p>Übersetzungen</p>
                            </a>
                        </li>
                        @endcan
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
            <div class="float-right d-none d-sm-block">For Flight Simulation Use Only! <b>Version {{ config('app.version') }}</b></div>
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


    @stack('scripts')
</body>
</html>
