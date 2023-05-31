@extends('membership.master')

@section('component')

	<div class="content-wrapper">

        {{-- Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Membership - Banned</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">
                                <a href="{{ route('membership.home') }}">Membership</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Banned
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        {{-- Content --}}
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-danger">
                            <div class="card-header">
                                <h3 class="card-title">@lang('membership.banned')</h3>
                            </div>
                            <div class="card-body">
                                @lang('membership.banned_description')
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li>@lang('membership.banned_till'): {{ $_account->currentBan->permanent ? 'Indefinitely' : $_account->currentBan->banned_till }}</li>
                                    <li>@lang('membership.banned_reason'): {{ $_account->currentBan->reason }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection