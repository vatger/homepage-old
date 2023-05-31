@extends('frontend.master')

@section('component')
        <!-- Start home-about Area -->
        <section class="home-about-area section-gap" style="background-image: url({{ asset('images/radar.png') }}); background-position: center; background-repeat: no-repeat; background-size: cover;">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12">
                        <h1 class="text-white">@lang('statistics.ats.header')</h1>
                        <p class="pb-20 text-white">
                            Statistik für {{ $searched }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- End home-about Area -->

        <section class="p-40">
            <div class="container-fluid">
                <div class="row align-items-center d-flex">
                    <nav id="nav-menu-container">
                        <ul class="nav-menu">
                            <li><a href="{{ route('statistics.home') }}">@lang('navigation.stats')</a></li>
                            <li><a href="{{ route('statistics.atc.home') }}">@lang('statistics.ats.header')</a></li>
                            <li><a href="{{ route('statistics.flight.home') }}">@lang('statistics.flight.header')</a></li>
                            <li><a href="{{ route('statistics.aerodrome.home') }}">@lang('statistics.aerodrome.header')</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>

        <section class="section-gap" id="aerodromes">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-hover table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>CID</th>
                                    <th>Callsign</th>
                                    <th>Connected</th>
                                    <th>Disconnected</th>
                                    <th>Duration (in Minutes)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($atcSessions as $as)
                                <tr>
                                    <td>{{ $as->account_id }}</td>
                                    <td>{{ $as->callsign }}</td>
                                    <td>{{ $as->connected_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ $as->disconnected_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ $as->minutes_online }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
@endsection