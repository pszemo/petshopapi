@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1>Lista</h1>
        <a href="{{route('pets.create')}}" class="btn btn-primary">Nowe zwierzę</a>
    </div>
    <div class="card-body">
        @if(count($pets) >0)
        <div class="table-responsive">
            <table class="table table-strippe">
                <thead>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Kategoria</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </thead>
                <tbody>
                    @foreach($pets as $pet)
                    <tr>
                        <td>{{$pet['id']}}</td>
                        <td>{{$pet['name']}}</td>
                        <td>{{$pet->['category']['name'] ?? 'Brak kategorii' }}</td>
                        <td>
                            @if($pet['status'] == 'available')
                            <span class="badge badge-success">Dostępny</span>
                            @elseif($pet['status'] == 'pending')
                            <span class="badge badge-warning">Oczekujący</span>
                            @elseif ($pet['status'] == 'sold')
                            <span class="badge badge-danger">Sprzedany</span>
                            @else
                            <span class="badge badge-secondary">{{ $pet['status']}}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{route('pets.show', $pet->id)}}" class="btn btn-info">Pokaż</a>
                            <a href="{{route('pets.edit', $pet->id)}}" class="btn btn-warning">Edytuj</a>
                            <form action="{{route('pets.destroy', $pet->id)}}" method="POST" style="display: inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Usuń</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info">Brak zwierząt</div>
    </div>
</div>
