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
						<a href="{{ route('vatauth.login') }}" class="primary-btn header-btn text-uppercase mb-5">Get Started</a>

                        <div class="align-bottom mt-5"><a class="btn text-white" href="#about"><i class="fa fa-chevron-down fa-5x" aria-hidden="true"></i></a></div>
					</div>
				</div>
			</div>
		</section>
		<!-- End banner Area -->

		<!-- Start home-about Area -->
		<!-- End home-about Area -->

		<!-- Start cat Area -->

		<!-- End cat Area -->

        <!-- Start Partner Area -->

		<!-- Start blog Area -->
		<!-- end blog Area -->

        <!-- Start service Area -->
        <!-- End service Area -->
@endsection
