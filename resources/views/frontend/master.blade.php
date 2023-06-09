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
        <div id="app">
            <header id="header" id="home">
                <div class="container-fluid main-menu">
                    <div class="row align-items-center justify-content-between d-flex">
                      <div id="logo">
                        <a href="{{ route('home') }}"><img src="{{ asset('images/vacc_logo.png') }}" alt="" title="" height="40" /></a>
                      </div>
                      <nav id="nav-menu-container">
                        <ul class="nav-menu">
                            <li class="menu-has-children">
                                <a href="{{ route('pilot.home') }}">@lang('navigation.pilots.pilots')</a>
                                <ul>
                                    <li><a href="{{ config('app.url') }}/pilots/aerodromes">@lang('navigation.pilots.aerodromes')</a></li>
                                    <li><a href="https://knowledgebase.vatsim-germany.org/books/pilot" target="_blank">@lang('navigation.pilots.firstSteps')</a></li>
                                    <li><a href="https://knowledgebase.vatsim-germany.org/books/ausbildungsubersicht-ptd" target="_blank">@lang('navigation.pilots.training')</a></li>
                                </ul>
                            </li>
                            <li class="menu-has-children">
                                <a href="{{ route('controller.home') }}">@lang('navigation.controllers.controllers')</a>
                                <ul>
                                    <li><a href="https://knowledgebase.vatsim-germany.org/books/atc" target="_blank">@lang('navigation.controllers.firstSteps')</a></li>
                                    <li><a href="https://knowledgebase.vatsim-germany.org/books/atc/page/gastlotsen-vatsim-germany" target="_blank">@lang('navigation.controllers.guests')</a></li>
                                    <li><a href="https://support.vatsim-germany.org/open.php?topicId=19" target="_blank">@lang('navigation.controllers.feedback')</a></li>
                                    <li><a href="https://knowledgebase.vatsim-germany.org/shelves/loa" target="_blank">@lang('navigation.controllers.loa')</a></li>
                                    <li><a href="https://knowledgebase.vatsim-germany.org/shelves/sop" target="_blank">@lang('navigation.controllers.sop')</a></li>
                                    <li><a href="http://files.aero-nav.com/EDXX" target="_blank">@lang('navigation.controllers.sectorfiles')</a></li>
                                    <li><a href="{{ config('app.url') }}/controllers/atd/solos">@lang('navigation.controllers.atdsolos')</a></li>
                                </ul>
                            </li>
                            <li class="menu-has-children">
                                <a href="#">@lang('navigation.community.community')</a>
                                <ul>
                                    <li><a href="ts3server://ts3.vatsim-germany.org" target="_blank">@lang('navigation.community.teamSpeak')</a></li>
                                    <li><a href="https://community.vatsim.net" target="_blank">Discord</a></li>
                                    <li><a href="https://board.vatsim-germany.org" target="_blank">@lang('navigation.community.forum')</a></li>
                                    <li><a href="https://shop.spreadshirt.de/vatsim-germany" target="_blank">@lang('navigation.merchandise')</a></li>
                                </ul>
                            </li>
                            <li><a href="https://knowledgebase.vatsim-germany.org" target="_blank">@lang('navigation.wiki')</a></li>
                            <li><a href="{{ route('statistics.home') }}">@lang('navigation.stats')</a></li>
                            <li class="menu-has-children">
                                <a href="#">@lang('navigation.tech.tech')</a>
                                <ul>
                                    {{--<li><a href="https://tech.vatsim-germany.org" target="_blank">@lang('navigation.tech.blog')</a></li>
                                    <li><a href="https://status.vatsim-germany.org" target="_blank">@lang('navigation.tech.status')</a></li>--}}
                                    <li><a href="https://support.vatsim-germany.org" target="_blank">@lang('navigation.tech.support')</a></li>
                                    <li><a href="mailto:support@vatsim-germany.org" target="_blank">@lang('navigation.tech.mail')</a></li>
                                    <li><a href="https://knowledgebase.vatsim-germany.org/books/ansprechpartner" target="_blank">@lang('navigation.tech.staff')</a></li>
                                </ul>
                            </li>
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
                                    <a href="{{ route('dataprotection.gdpr') }}">@lang('navigation.gdpr')</a> | <a href="{{ route('dataprotection.imprint') }}">@lang('navigation.imprint')</a>
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
