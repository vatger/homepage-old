@extends('frontend.master')

@section('component')
        <!-- Start home-about Area -->
        <section class="home-about-area section-gap" style="background-image: url({{ asset('images/wing.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover;">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12">
                        <h1 class="text-white">Authentication</h1>
                        <p class="pb-20 text-white">
                            Authentication Failed
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
                        <h1>VATSIM SSO Service Authentication Failed</h1>
                        <p class="pb-20">
                            @lang('authentication.vatauth.failed')
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
                        <form action="{{ route('vatauth.local') }}" method="POST" class="form-area">
                            @csrf
                            <div class="row">
                                <div class="col-12 form-group">
                                    <input type="text" name="cid" placeholder="10000010" class="common-input form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <input type="password" name="lpwd" class="common-input form-control">
                                </div>
                            </div>
                            <div>
                                <div class="col-12 form-group">
                                    <input type="submit" value="Login" class="primary-btn primary circe">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
@endsection                