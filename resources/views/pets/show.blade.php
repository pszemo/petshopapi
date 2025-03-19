@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1>Szczegóły zwierzęcia</h1>
            <div>
                <a href="{{ route('pets.edit', $pet['id']) }}" class="btn btn-warning">Edytuj</a>
                <form action="{{ route('pets.destroy', $pet['id']) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć to zwierzę?')">Usuń</button>
                </form>
                <a href="{{ route('pets.index') }}" class="btn btn-secondary">Powrót do listy</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>ID:</th>
                            <td>{{ $pet['id'] }}</td>
                        </tr>
                        <tr>
                            <th>Nazwa:</th>
                            <td>{{ $pet['name'] }}</td>
                        </tr>
                        <tr>
                            <th>Kategoria:</th>
                            <td>{{ $pet['category']['name'] ?? 'Brak kategorii' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($pet['status'] == 'available')
                                    <span class="badge bg-success">Dostępny</span>
                                @elseif($pet['status'] == 'pending')
                                    <span class="badge bg-warning">Oczekujący</span>
                                @elseif($pet['status'] == 'sold')
                                    <span class="badge bg-danger">Sprzedany</span>
                                @else
                                    <span class="badge bg-secondary">{{ $pet['status'] }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @if(isset($pet['photoUrls']) && count($pet['photoUrls']) > 0 && $pet['photoUrls'][0])
                        <img src="{{ $pet['photoUrls'][0] }}" alt="{{ $pet['name'] }}" class="img-fluid">
                    @else
                        <div class="alert alert-info">Brak zdjęcia</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
