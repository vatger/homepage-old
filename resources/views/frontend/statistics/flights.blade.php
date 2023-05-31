@extends('frontend.master')

@section('component')
        <!-- Start home-about Area -->
        <section class="home-about-area section-gap" style="background-image: url({{ asset('images/radar.png') }}); background-position: center; background-repeat: no-repeat; background-size: cover;">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12">
                        <h1 class="text-white">@lang('statistics.flight.header')</h1>
                        <p class="pb-20 text-white">
                            Ergebnis für Suchanfrage nach: {{ $searchString }}
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

        <section class="section-gap">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>Activity Chart</h2>
                        <p>Kombinierte Traffic Chart</p>
                        <div id="chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-gap" id="aerodromes">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4>@lang('statistics.flight.header')</h4>
                        <table class="table table-hover table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Callsign</th>
                                    <th>CID</th>
                                    <th>Departure</th>
                                    <th>Arrival</th>
                                    <th>Departed</th>
                                    <th>Duration (in Minutes)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flightData as $fd)
                                <tr>
                                    <td>{{ $fd->callsign }}</td>
                                    <td>{{ $fd->account_id }}</td>
                                    <td>{{ $fd->departure_airport }}</td>
                                    <td>{{ $fd->arrival_airport }}</td>
                                    <td>{{ $fd->connected_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ $fd->minutes_online }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
@endsection

@push('styles')
@endpush

@push('scripts')
    <!-- Charting library -->
    <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script>
    <!-- Chartisan -->
    <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
    <!-- Your application script -->
    <script>
      const chart = new Chartisan({
        el: '#chart',
        url: "@chart('activity_chart', ['searchString' => $searchString])",
        hooks: new ChartisanHooks()
            .legend()
            .colors(['#2b3089', '#e63329'])
            .datasets(['line', 'line'])
            .tooltip(),
      });
    </script>
@endpush