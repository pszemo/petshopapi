<?php
// app/Services/PetstoreService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PetstoreService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = 'https://petstore.swagger.io/v2';
    }

    /**
     * Pobiera zwierzęta według statusu
     */
    public function getPetsByStatus($status = 'available'): array
    {
        try {
            $response = Http::get($this->apiUrl . '/pet/findByStatus', [
                'status' => $status
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Nie udało się pobrać zwierząt',
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception in getPetsByStatus: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas komunikacji z API',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Pobiera szczegóły zwierzęcia po ID
     */
    public function getPetById($id): array
    {
        try {
            $response = Http::get($this->apiUrl . '/pet/' . $id);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Nie znaleziono zwierzęcia o podanym ID',
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception in getPetById: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas komunikacji z API',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Dodaje nowe zwierzę
     */
    public function createPet($data): array
    {
        try {
            $response = Http::post($this->apiUrl . '/pet', $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Nie udało się dodać zwierzęcia',
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception in createPet: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas komunikacji z API',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Aktualizuje dane zwierzęcia
     */
    public function updatePet($data)
    {
        try {
            $response = Http::put($this->apiUrl . '/pet', $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Nie udało się zaktualizować danych zwierzęcia',
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception in updatePet: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas komunikacji z API',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Usuwa zwierzę
     */
    public function deletePet($id)
    {
        try {
            $response = Http::delete($this->apiUrl . '/pet/' . $id);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Zwierzę zostało usunięte'
                ];
            }

            return [
                'success' => false,
                'message' => 'Nie udało się usunąć zwierzęcia',
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception in deletePet: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Wystąpił błąd podczas komunikacji z API',
                'error' => $e->getMessage()
            ];
        }
    }
}
