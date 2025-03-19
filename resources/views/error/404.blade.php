@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1>Błąd 404</h1>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <p>Strona, której szukasz, nie istnieje.</p>
            </div>
            <a href="{{ route('pets.index') }}" class="btn btn-primary">Powrót do strony głównej</a>
        </div>
    </div>
@endsection
