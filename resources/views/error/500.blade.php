@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1>Błąd serwera</h1>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <p>Przepraszamy, wystąpił błąd serwera. Spróbuj ponownie później.</p>
            </div>
            <a href="{{ route('pets.index') }}" class="btn btn-primary">Powrót do strony głównej</a>
        </div>
    </div>
@endsection
