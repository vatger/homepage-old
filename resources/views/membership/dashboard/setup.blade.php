@extends('membership.master')

@section('component')

	<div class="content-wrapper">

        {{-- Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Membership - Setup</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">
                                <a href="{{ route('membership.home') }}">Membership</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="{{ route('membership.setup') }}">Setup</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        {{-- Content --}}
        <section class="content">
    		<setup></setup>
        </section>
    </div>

@endsection