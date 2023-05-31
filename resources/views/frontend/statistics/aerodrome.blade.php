@extends('frontend.master')

@section('component')
        <!-- Start home-about Area -->
        <section class="home-about-area section-gap" style="background-image: url({{ asset('images/splash/dus.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover;">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-8 col-md-12">
                        <h1 class="text-white">{{ $aerodrome->name }}</h1>
                        <p class="pb-20 text-white">
                            Statistik für {{ $aerodrome->icao }}<br/>
                            {{ $from->format('d.m.Y') }} - {{ $till->format('d.m.Y') }}
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
                    <div class="col-md-4 col-sm-12">
                        <h4>Flugplatz Informationen</h4>
                        <table class="table table-responsive-sm">
                            <tr>
                                <td>ICAO / IATA</td>
                                <td>{{ $aerodrome->icao }} / {{ $aerodrome->iata }}</td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td>{{ $aerodrome->country }}</td>
                            </tr>
                            <tr>
                                <td>State</td>
                                <td>{{ $aerodrome->state }}</td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td>{{ $aerodrome->city }}</td>
                            </tr>
                            <tr>
                                <td>Military</td>
                                <td>{{ $aerodrome->military ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <td>Civilian</td>
                                <td>{{ $aerodrome->civilian ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <td>Lat / Lon</td>
                                <td>{{ $aerodrome->latitude }} / {{ $aerodrome->longitude }}</td>
                            </tr>
                            <tr>
                                <td>Elevation</td>
                                <td>{{ $aerodrome->elevation }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-8 col-sm-12">
                        <h4>ATC Live</h4>
                        <p>Alle Lotsenaktivitäten im Moment</p>
                        <table id="atcCurrent" class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Station</th>
                                    <th>Online Seit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($atcCurrent as $atc)
                                <tr>
                                    <td>{{ $atc->callsign }}</td>
                                    <td>{{ $atc->connected_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr class="mb-20">
                        <h4>ATC Historie</h4>
                        <p>Alle Lotsenaktivitäten der Vergangenheit</p>
                        <table id="atcHistory" class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Station</th>
                                    <th>Beginn</th>
                                    <th>Ende</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($atcHistory as $atc)
                                <tr>
                                    <td>{{ $atc->callsign }}</td>
                                    <td>{{ $atc->connected_at }}</td>
                                    <td>{{ $atc->disconnected_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-gap">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <h2>Departure Historie</h2>
                        <p>Alle Abflüge der Vergangenheit</p>
                        <table id="departureHistory" class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Flug</th>
                                    <th>Nach</th>
                                    <th>Abflug</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departureHistory as $pilot)
                                <tr>
                                    <td>{{ $pilot->callsign }}</td>
                                    <td>{{ $pilot->arrival_airport }}</td>
                                    <td>{{ $pilot->connected_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <h2>Departure Aktuell</h2>
                        <p>Alle Abflüge im Moment</p>
                        <table id="departureCurrent" class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Flug</th>
                                    <th>Nach</th>
                                    <th>Abflug</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departureCurrent as $pilot)
                                <tr>
                                    <td>{{ $pilot->callsign }}</td>
                                    <td>{{ $pilot->arrival_airport }}</td>
                                    <td>{{ $pilot->connected_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-gap">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <h2>Arrival Historie</h2>
                        <p>Alle Anflüge der Vergangenheit</p>
                        <table id="arrivalHistory" class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Flug</th>
                                    <th>Von</th>
                                    <th>Abflug</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($arrivalHistory as $pilot)
                                <tr>
                                    <td>{{ $pilot->callsign }}</td>
                                    <td>{{ $pilot->departure_airport }}</td>
                                    <td>{{ $pilot->connected_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <h2>Arrival Aktuell</h2>
                        <p>Alle Anflüge im Moment</p>
                        <table id="arrivalCurrent" class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Flug</th>
                                    <th>Von</th>
                                    <th>Abflug</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($arrivalCurrent as $pilot)
                                <tr>
                                    <td>{{ $pilot->callsign }}</td>
                                    <td>{{ $pilot->departure_airport }}</td>
                                    <td>{{ $pilot->connected_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-gap">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>Traffic Chart</h2>
                        <p>Kombinierte Traffic Chart</p>
                        <div id="chart" style="height: 300px;"></div>
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
        url: "@chart('traffic_chart', ['aerodrome' => $aerodrome->icao, 'from' => $from->format('d.m.Y'), 'till' => $till->format('d.m.Y')])",
        hooks: new ChartisanHooks()
            .legend()
            .colors(['#2b3089', '#e63329'])
            .datasets([{type: 'line', fill: false}, {type: 'line', fill: false}])
            .tooltip(),
      });
    </script>
@endpush