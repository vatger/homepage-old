@extends('frontend.master')

@section('component')
		<!-- start banner Area -->
		<section class="banner-area relative" style="background:url(/images/radar.png) center;" id="home">
			<div class="overlay overlay-bg"></div>
			<div class="container">
				<div class="row fullscreen d-flex align-items-center justify-content-center">
					<div class="banner-content col-lg-12 col-md-12">
						<h6 class="text-uppercase"></h6>
						<h1>
							{{ config('app.name') }}
						</h1>
						<p class="text-white">
							Controlling The Virtual German Airspace With Passion!
						</p>
						<a href="https://knowledgebase.vatsim-germany.org/shelves/welcome-willkommen" class="primary-btn header-btn text-uppercase mb-5">Get Started</a>

                        <div class="align-bottom mt-5"><a class="btn text-white" href="#about"><i class="fa fa-chevron-down fa-5x" aria-hidden="true"></i></a></div>
					</div>
				</div>
			</div>
		</section>
		<!-- End banner Area -->

		<!-- Start home-about Area -->
		<section class="home-about-area section-gap" id="about">
			<div class="container">
				<div class="row justify-content-center align-items-center">
					<div class="col-lg-8 col-md-12">
						@lang('landing.welcome')
						<a class="primary-btn" href="https://knowledgebase.vatsim-germany.org/shelves/welcome-willkommen">Get Started Now</a>
					</div>
				</div>
			</div>
		</section>
		<!-- End home-about Area -->

		<!-- Start cat Area -->
		<section class="cat-area section-gap" id="feature">
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-sm-12">
						<atc-bookings-component :dashboard="false"></atc-bookings-component>
					</div>
					<div class="col-md-6 col-sm-12">
						<atc-live-component :dashboard="false"></atc-live-component>
					</div>
				</div>
			</div>
		</section>
		<!-- End cat Area -->

        <!-- Start Partner Area -->
        @if(count($partners))
            <section class="service-area section-gap" id="service">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12 pb-30 header-text text-center">
                            <a href="{{ route("partners") }}">
                                <h1 class="mb-10">Partners</h1>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        @foreach($partners as $partner)
                            <div class="col-lg-4">
                                <div class="single-service">
                                    <div class="thumb">
                                        <a href="{{ $partner->link_url }}">
                                            <img src="{{ $partner->logo_url }}" alt="">
                                        </a>
                                    </div>
                                    <a href="{{ $partner->link_url }}">
                                        <h4>{{ $partner->name }}</h4>
                                    </a>
                                    <p>
                                        {{ $partner->description }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        <!-- End Partner Area -->

		<!-- Start blog Area -->
		<section class="blog-area section-gap" id="blog">
			<news-feed></news-feed>
		</section>
		<!-- end blog Area -->

        <!-- Start service Area -->
        <section class="service-area section-gap" id="service">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 pb-30 header-text text-center">
                        <h1 class="mb-10">@lang('landing.services.header')</h1>
                        <p>
                            @lang('landing.services.description')
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <img src="{{ asset('images/encyclopedia.jpg') }}" alt="">
                            </div>
                            <h4>@lang('landing.services.wiki.header')</h4>
                            <p>
                                @lang('landing.services.wiki.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <img src="{{ asset('images/splash/muc.jpg') }}" alt="">
                            </div>
                            <h4>@lang('landing.services.community.header')</h4>
                            <p>
                                @lang('landing.services.community.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <img src="{{ asset('images/stats.jpg') }}" alt="">
                            </div>
                            <h4>@lang('landing.services.statistics.header')</h4>
                            <p>
                                @lang('landing.services.statistics.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <img src="{{ asset('images/wing.jpg') }}" alt="">
                            </div>
                            <h4>@lang('landing.services.flying.header')</h4>
                            <p>
                                @lang('landing.services.flying.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <img src="{{ asset('images/dashboard.jpg') }}" alt="">
                            </div>
                            <h4>@lang('landing.services.material.header')</h4>
                            <p>
                                @lang('landing.services.material.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="single-service">
                            <div class="thumb">
                                <img src="{{ asset('images/splash/overhead.jpg') }}" alt="">
                            </div>
                            <h4>@lang('landing.services.gdpr.header')</h4>
                            <p>
                                @lang('landing.services.gdpr.description')
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End service Area -->
@endsection
