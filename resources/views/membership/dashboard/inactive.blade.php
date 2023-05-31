@extends('membership.master')

@section('component')

	<div class="content-wrapper">

        {{-- Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Membership - Inactive</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">
                                <a href="{{ route('membership.home') }}">Membership</a>
                            </li>
                            <li class="breadcrumb-item active">
                                VATSIM Account Inactive
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
                                <h3 class="card-title">@lang('membership.inactive')</h3>
                            </div>
                            <div class="card-body">
                                @lang('membership.inactive_description')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
