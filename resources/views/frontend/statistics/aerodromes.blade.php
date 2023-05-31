@extends('frontend.master')

@section('component')
		<!-- Start home-about Area -->
		<section class="home-about-area section-gap" style="background-image: url({{ asset('images/splash/muc.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover;">
			<div class="container">
				<div class="row justify-content-center align-items-center">
					<div class="col-lg-8 col-md-12">
						<h1 class="text-white">@lang('statistics.aerodrome.header')</h1>
						<p class="pb-20 text-white">
							Wähle einen der untenstehenden Flugplätze aus, um dessen Daten anzuzeigen.
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
				<statistics-aerodromes-component></statistics-aerodromes-component>
			</div>
		</section>
@endsection