@extends('frontend.master')

@section('component')
        <!-- Start home-about Area -->
        <section class="home-about-area section-gap" style="background-image: url({{ asset('images/radar.png') }}); background-position: center; background-repeat: no-repeat; background-size: cover;">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12">
                        <h1 class="text-white">@lang('statistics.ats.header')</h1>
                        <p class="pb-20 text-white">
                            Tippe bitte den Station-Identifier in das Feld ein.
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
                        <form action="{{ route('statistics.atc.search') }}" method="POST" class="form-area">
                            @csrf
                            <div class="row">
                                <div class="col-12 form-group">
                                    <input type="text" name="searchString" placeholder="10000010, 131.325, EDDH_E_APP" class="common-input form-control">
                                </div>
                                <div class="col-12 form-group">
                                    <label>From:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <input type="date" name="from" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask="" im-insert="false">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="col-12 form-group">
                                    <label>Till:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <input type="date" name="till" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask="" im-insert="false">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <div class="col-12 form-group">
                                    <input type="submit" value="Suchen" class="primary-btn primary circe">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
@endsection