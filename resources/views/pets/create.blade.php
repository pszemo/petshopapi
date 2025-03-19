@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2>Dodaj nowe zwierzę</h2>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('pets.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Imię</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="category_name">Kategoria (Gatunek)</label>
                                <select class="form-control" id="category_name" name="category_name" required>
                                    <option value="">Wybierz gatunek</option>
                                    <option value="dog" {{ old('category_name') == 'dog' ? 'selected' : '' }}>Pies</option>
                                    <option value="cat" {{ old('category_name') == 'cat' ? 'selected' : '' }}>Kot</option>
                                    <option value="bird" {{ old('category_name') == 'bird' ? 'selected' : '' }}>Ptak</option>
                                    <option value="rabbit" {{ old('category_name') == 'rabbit' ? 'selected' : '' }}>Królik</option>
                                    <option value="hamster" {{ old('category_name') == 'hamster' ? 'selected' : '' }}>Chomik</option>
                                    <option value="other" {{ old('category_name') == 'other' ? 'selected' : '' }}>Inny</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="breed">Rasa</label>
                                <input type="text" class="form-control" id="breed" name="breed" value="{{ old('breed') }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="age">Wiek (w latach)</label>
                                <input type="number" class="form-control" id="age" name="age" value="{{ old('age') }}" min="0" step="0.1">
                            </div>

                            <div class="form-group mb-3">
                                <label for="gender">Płeć</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">Wybierz płeć</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Samiec</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Samica</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="weight">Waga (kg)</label>
                                <input type="number" class="form-control" id="weight" name="weight" value="{{ old('weight') }}" min="0" step="0.01">
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Opis</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo">Zdjęcie</label>
                                <input type="file" class="form-control" id="photo" name="photo">
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="vaccinated" name="vaccinated" value="1" {{ old('vaccinated') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vaccinated">
                                        Zaszczepiony
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sterilized" name="sterilized" value="1" {{ old('sterilized') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sterilized">
                                        Wysterylizowany/Wykastrowany
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">Wybierz status</option>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Dostępny</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Oczekujący</option>
                                    <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sprzedany</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_url">URL Zdjęcia</label>
                                <input type="url" class="form-control" id="photo_url" name="photo_url" value="{{ old('photo_url') }}">
                                <small class="form-text text-muted">Opcjonalnie: podaj URL do zdjęcia zwierzęcia</small>
                            </div>

                            <div class="form-group mb-4">
                                <label for="medical_notes">Informacje medyczne</label>
                                <textarea class="form-control" id="medical_notes" name="medical_notes" rows="2">{{ old('medical_notes') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('pets.index') }}" class="btn btn-secondary">Anuluj</a>
                                <button type="submit" class="btn btn-primary">Dodaj zwierzę</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
