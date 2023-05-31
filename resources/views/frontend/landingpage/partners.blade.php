@extends('frontend.master')

@section('component')
		<!-- Start Partner Area -->
		<section class="service-area section-gap" id="service">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-md-12 pb-30 header-text text-center">
						<h1 class="mb-10">Partners</h1>
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
                                {{ $partner->description  }}
							</p>
						</div>
					</div>
                    @endforeach
				</div>
			</div>
		</section>
		<!-- End Partner Area -->
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
@endsection
