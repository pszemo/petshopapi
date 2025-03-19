@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1>Lista zwierząt</h1>
            <a href="{{ route('pets.create') }}" class="btn btn-primary">Dodaj nowe zwierzę</a>
        </div>
        <div class="card-body">
            @if(isset($error))
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endif
            @if(isset($insecureConnection) && $insecureConnection)
                <div class="alert alert-warning">
                    <strong>Uwaga!</strong> Połączenie z API nie jest zabezpieczone (SSL wyłączony). Dane mogą być
                    narażone na przechwycenie.
                </div>
            @endif

            @if(isset($pets) && count($pets) > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nazwa</th>
                            <th>Kategoria</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pets as $pet)
                            <tr>
                                <td>{{ $pet['id'] }}</td>
                                <td>{{ $pet['name'] }}</td>
                                <td>{{ $pet['category']['name'] ?? 'Brak kategorii' }}</td>
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
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('pets.show', $pet['id']) }}" class="btn btn-sm btn-info">Szczegóły</a>
                                        <a href="{{ route('pets.edit', $pet['id']) }}" class="btn btn-sm btn-warning">Edytuj</a>
                                        <form action="{{ route('pets.destroy', $pet['id']) }}" method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Czy na pewno chcesz usunąć to zwierzę?')">
                                                Usuń
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Brak zwierząt do wyświetlenia.
                </div>
            @endif
        </div>
    </div>
@endsection
