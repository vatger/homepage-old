@extends('frontend.master')

@section('component')
            {{-- GDPR --}}
                <div id="gdpr" class="wrapper style1 fade-up">
                    <div class="container">
                        <header class="major">
                            <h2>GDPR</h2>
                            <p>Aktuelle Datenschutzerklärung der vACC Germany.</p>
                        </header>
                        <div class="row gtr-150">
                            <div class="col-12">
                                {!! $gdpr !!}
                            </div>
                        </div>
                    </div>
                </div>
@endsection