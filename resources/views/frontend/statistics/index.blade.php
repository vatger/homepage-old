@extends('frontend.master')

@section('component')
        <!-- start banner Area -->
        <section class="banner-area relative" style="background:url(/images/stats.jpg) center;" id="home">
            <div class="overlay overlay-bg"></div>
            <div class="container">
                <div class="row fullscreen d-flex align-items-center justify-content-center">
                    <div class="banner-content col-lg-12 col-md-12">
                        <h6 class="text-uppercase"></h6>
                        <h1>
                            {{ config('app.name') }}
                        </h1>
                        <p class="text-white">
                            Statistics
                        </p>
                        <a href="#go" class="primary-btn header-btn text-uppercase">Go Nerdy!</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- End banner Area -->

        <!-- Start home-about Area -->
        <section class="home-about-area section-gap" id="go">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-8 col-md-12">
                        <h1>vACC Germany Statistics-Center</h1>
                        <p class="pb-20">
                            @lang('statistics.welcome')
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- End home-about Area -->

        <!-- Start service Area -->
        <section class="service-area section-gap" id="service">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 pb-30 header-text text-center">
                        <h1 class="mb-10">
                            @lang('statistics.services.services')
                        </h1>
                        <p>
                            @lang('statistics.services.description')
                        </p>
                    </div>
                </div>                      
                <div class="row">
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <a href="{{ route('statistics.atc.home') }}"><img src="{{ asset('images/radar.png') }}" alt=""></a>
                            </div>
                            <h4>@lang('statistics.ats.header')</h4>
                            <p>
                                @lang('statistics.ats.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <a href="{{ route('statistics.flight.home') }}"><img src="{{ asset('images/splash/overhead.jpg') }}" alt=""></a>
                            </div>
                            <h4>@lang('statistics.flight.header')</h4>
                            <p>
                                @lang('statistics.flight.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <a href="{{ route('statistics.aerodrome.home') }}"><img src="{{ asset('images/splash/dus.jpg') }}" alt=""></a>
                            </div>
                            <h4>@lang('statistics.aerodrome.header')</h4>
                            <p>
                                @lang('statistics.aerodrome.description')
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End service Area -->
@endsection