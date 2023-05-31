@extends('frontend.master')

@section('component')
            {{-- Imprint --}}
                <div id="imprint" class="wrapper style1 fade-up">
                    <div class="container">
                        <header class="major">
                            <h2>Imprint</h2>
                            <p>Aktuelles Impressum der vACC Germany.</p>
                        </header>
                        <div class="row gtr-150">
                            <div class="col-12">
                                {!! $imprint !!}
                            </div>
                        </div>
                    </div>
                </div>
@endsection
