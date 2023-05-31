@extends('frontend.master')

@section('component')
<!-- Start home-about Area -->
<section class="home-about-area section-gap"
    style="background-image: url({{ asset('images/wing.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover;">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-12">
                <h1 class="text-white">@lang('authentication.vatauth.emaildupe.header')</h1>
                <p class="pb-20 text-white">
                    @lang('authentication.vatauth.emaildupe.subheader')
                </p>
            </div>
        </div>
    </div>
</section>
<!-- End home-about Area -->

<!-- Start home-about Area -->
<section class="home-about-area section-gap">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-8 col-md-12">
                <h1>@lang('authentication.vatauth.emaildupe.subheader')</h1>
                <p class="pb-20">
                    @lang('authentication.vatauth.emaildupe.text')
                </p>
            </div>
        </div>
    </div>
</section>
<!-- End home-about Area -->
<section class="section-gap">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <br><br><br><br><br><br>
            </div>
        </div>
    </div>
</section>

@endsection
