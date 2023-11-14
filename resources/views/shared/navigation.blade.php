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
                        <i class="fas fa-cogs"></i> @lang('navigation.administration')
                    </a>
                </li>
                @endcan
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
