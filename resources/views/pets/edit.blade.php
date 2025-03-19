@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1>Edytuj zwierzę</h1>
        </div>
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white" id="debugHeader">
                <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="collapse" data-bs-target="#debugData" aria-expanded="false" aria-controls="debugData" style="text-decoration: none;">
                    <i class="fas fa-code me-2"></i> Dane debugowania <i class="fas fa-chevron-down ms-2"></i>
                </button>
            </div>
            <div id="debugData" class="collapse">
                <div class="card-body bg-light">
                    <pre style="max-height: 400px; overflow-y: auto;">{{ print_r($pet, true) }}</pre>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('pets.update', $pet['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nazwa</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pet['name']) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_name" class="form-label">Kategoria</label>
                    <input type="text" class="form-control @error('category_name') is-invalid @enderror" id="category_name" name="category_name" value="{{ old('category_name', $pet['category']['name'] ?? '') }}" required>
                    <input type="hidden" name="category_id" value="{{ $pet['category']['id'] ?? 0 }}">
                    @error('category_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="">Wybierz status</option>
                        <option value="available" {{ (isset($pet['status']) && $pet['status'] == 'available') ? 'selected' : '' }}>Dostępny</option>
                        <option value="pending" {{ (isset($pet['status']) && $pet['status'] == 'pending') ? 'selected' : '' }}>Oczekujący</option>
                        <option value="sold" {{ (isset($pet['status']) && $pet['status'] == 'sold') ? 'selected' : '' }}>Sprzedany</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="photo_url" class="form-label">URL zdjęcia</label>
                    <input type="url" class="form-control @error('photo_url') is-invalid @enderror" id="photo_url" name="photo_url" value="{{ old('photo_url', $pet['photoUrls'][0] ?? '') }}">
                    @error('photo_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('pets.show', $pet['id']) }}" class="btn btn-secondary">Anuluj</a>
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                </div>
            </form>
        </div>
    </div>
@endsection
