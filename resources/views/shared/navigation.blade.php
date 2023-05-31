<!-- Navigation -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
            <!-- Left Side -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="pilotDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @lang('navigation.pilots.pilots')
                    </a>
                    <div class="dropdown-menu" aria-labelledby="pilotDropdown">
                        <a class="dropdown-item" href="{{ config('app.url') }}/pilots/aerodromes">@lang('navigation.pilots.aerodromes')</a>
                        <a class="dropdown-item" href="{{ config('app.url') }}/pilots/livemap">@lang('navigation.pilots.livemap')</a>
                        <a class="dropdown-item" href="{{ config('app.url') }}/pilots/weather">@lang('navigation.pilots.weather')</a>
                        <a class="dropdown-item" href="https://knowledgebase.vatsim-germany.org/books/pilot" target="_blank">@lang('navigation.pilots.firstSteps')</a>
                        <a class="dropdown-item" href="https://knowledgebase.vatsim-germany.org/books/ausbildungsubersicht-ptd" target="_blank">@lang('navigation.pilots.training')</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="controllerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @lang('navigation.controllers.controllers')
                    </a>
                    <div class="dropdown-menu" aria-labelledby="controllerDropdown">
                        <a class="dropdown-item" href="https://knowledgebase.vatsim-germany.org/books/atc" target="_blank">@lang('navigation.controllers.firstSteps')</a>
                        <a class="dropdown-item" href="https://knowledgebase.vatsim-germany.org/books/atc/page/gastlotsen-vatsim-germany" target="_blank">@lang('navigation.controllers.guests')</a>
                        <a class="dropdown-item" href="https://support.vatsim-germany.org/open.php?topicId=19" target="_blank">@lang('navigation.controllers.feedback')</a>
                        <a class="dropdown-item" href="https://knowledgebase.vatsim-germany.org/shelves/loa" target="_blank">@lang('navigation.controllers.loa')</a>
                        <a class="dropdown-item" href="https://knowledgebase.vatsim-germany.org/shelves/sops-airports" target="_blank">@lang('navigation.controllers.sop')</a>
                        <a class="dropdown-item" href="http://files.aero-nav.com/EDXX" target="_blank">@lang('navigation.controllers.sectorfiles')</a>
                        <a class="dropdown-item" href="{{ config('app.url') }}/controllers/atd/solos">@lang('navigation.controllers.atdsolos')</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="pilotDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @lang('navigation.community.community')
                    </a>
                    <div class="dropdown-menu" aria-labelledby="pilotDropdown">
                        <a class="dropdown-item" href="ts3server://ts3.vatsim-germany.org">@lang('navigation.community.teamSpeak')</a>
                        <a class="dropdown-item" href="https://community.vatsim.net" target="_blank">Discord</a>
                        <a class="dropdown-item" href="https://board.vatsim-germany.org" target="_blank">@lang('navigation.community.forum')</a>
                        <a class="dropdown-item" href="https://shop.spreadshirt.de/vatsim-germany" target="_blank">@lang('navigation.merchandise')</a>
                    </div>
                </li>
            </ul>
            <!-- Right Side -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="https://knowledgebase.vatsim-germany.org" target="_blank">@lang('navigation.wiki')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('statistics.home') }}">@lang('navigation.stats')</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="techDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @lang('navigation.tech.tech')
                    </a>
                    <div class="dropdown-menu" aria-labelledby="techDropdown">
                        {{--<a class="dropdown-item" href="https://tech.vatsim-germany.org" target="_blank">@lang('navigation.tech.blog')</a>
                        <a class="dropdown-item" href="https://status.vatsim-germany.org" target="_blank">@lang('navigation.tech.status')</a>--}}
                        <a class="dropdown-item" href="https://support.vatsim-germany.org" target="_blank">@lang('navigation.tech.support')</a>
                        <a class="dropdown-item" href="mailto:support@vatsim-germany.org" target="_blank">@lang('navigation.tech.mail')</a>
                        <a class="dropdown-item" href="https://knowledgebase.vatsim-germany.org/books/ansprechpartner" target="_blank">@lang('navigation.tech.staff')</a>
                    </div>
                </li>
                @can('administration')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('administration.home') }}">
                        <i class="fas fa-cogs"></i> @lang('navigation.administration')
                    </a>
                </li>
                @endcan
                <li class="nav-item dropdown">
                    <router-link to="/membership/notifications" :class="'nav-link'" exact>
                        <i class="far fa-bell"></i>
                        @if(count($_account->unreadNotifications) > 0)
                        <span class="badge badge-warning navbar-badge">{{ count($_account->unreadNotifications) }}</span>
                        @endif
                    </router-link>
                </li>
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
                @impersonating
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('impersonate.leave') }}">
                        <i class="fas fa-sign-out-alt"></i> @lang('navigation.impersonate.leave')
                    </a>
                </li>
                @endImpersonating

                @if(Auth::check())
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('membership.home') }}">
                        <i class="fas fa-tachometer-alt"></i> @lang('navigation.dashboard')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('vatauth.logout') }}">
                        <i class="fas fa-sign-out-alt"></i> @lang('navigation.auth.logout')
                    </a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('vatauth.login') }}">
                        <i class="fas fa-sign-out-alt"></i> @lang('navigation.auth.login')
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i class="fas fa-th-large"></i></a>
                </li>
            </ul>
        </nav>
