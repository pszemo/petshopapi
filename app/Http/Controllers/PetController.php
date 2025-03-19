<?php
// app/Http/Controllers/PetController.php

namespace App\Http\Controllers;

use App\Services\PetstoreService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PetController extends Controller
{
    private $apiUrl = 'https://petstore.swagger.io/v2';
    private $petstoreService;
    private $secureConnectionFailed = false;

    public function __construct(PetstoreService $petstoreService)
    {
        $this->petstoreService = $petstoreService;
    }

    /**
     * Wykonuje zapytanie HTTP z obsługą awaryjną dla SSL
     * @throws Exception
     */
    private function safeRequest($method, $url, $data = []): array
    {
        $this->secureConnectionFailed = false;

        try {
            // Najpierw próbujemy z włączonym SSL
            $response = Http::$method($url, $data);
            return ['response' => $response, 'secure' => true];
        } catch (Exception $e) {
            // Jeśli wystąpił błąd SSL, próbujemy ponownie z wyłączonym SSL
            if (str_contains($e->getMessage(), 'SSL certificate problem')) {
                Log::warning('SSL verification failed, retrying without SSL: ' . $e->getMessage());
                $this->secureConnectionFailed = true;

                $response = Http::withOptions([
                    'verify' => false
                ])->$method($url, $data);

                return ['response' => $response, 'secure' => false];
            }

            // Jeśli to inny błąd, przekazujemy go dalej
            throw $e;
        }
    }

    /**
     * Wyświetla listę zwierząt
     */
    public function index()
    {
        try {
            // Używamy bezpiecznego zapytania
            $result = $this->safeRequest('get', $this->apiUrl . '/pet/findByStatus', [
                'status' => 'available'
            ]);

            $response = $result['response'];
            $secure = $result['secure'];

            if ($response->successful()) {
                $pets = $response->json();

                // Przetwarzamy dane, aby upewnić się, że każdy rekord ma wszystkie potrzebne pola
                $processedPets = collect($pets)->map(function ($pet) {
                    return [
                        'id' => $pet['id'] ?? 0,
                        'name' => $pet['name'] ?? 'Brak nazwy',
                        'category' => [
                            'id' => $pet['category']['id'] ?? 0,
                            'name' => isset($pet['category']) ? ($pet['category']['name'] ?? 'Brak kategorii') : 'Brak kategorii'
                        ],
                        'status' => $pet['status'] ?? 'unknown',
                        'photoUrls' => $pet['photoUrls'] ?? []
                    ];
                })->toArray();

                return view('pets.index', [
                    'pets' => $processedPets,
                    'insecureConnection' => !$secure
                ]);
            } else {
                Log::error('Failed to fetch pets: ' . $response->body());
                return view('pets.index', [
                    'pets' => [],
                    'error' => 'Nie udało się pobrać listy zwierząt. Spróbuj ponownie później.',
                    'insecureConnection' => !$secure
                ]);
            }
        } catch (Exception $e) {
            Log::error('Exception while fetching pets: ' . $e->getMessage());
            return view('pets.index', [
                'pets' => [],
                'error' => 'Wystąpił błąd podczas komunikacji z API: ' . $e->getMessage(),
                'insecureConnection' => $this->secureConnectionFailed
            ]);
        }
    }

    /**
     * Wyświetla szczegóły zwierzęcia
     */
    public function show($id)
    {
        try {
            $result = $this->safeRequest('get', $this->apiUrl . '/pet/' . $id);

            $response = $result['response'];
            $secure = $result['secure'];

            if ($response->successful()) {
                $pet = $response->json();

                $processedPet = [
                    'id' => $pet['id'] ?? 0,
                    'name' => $pet['name'] ?? 'Brak nazwy',
                    'category' => [
                        'id' => $pet['category']['id'] ?? 0,
                        'name' => isset($pet['category']) ? ($pet['category']['name'] ?? 'Brak kategorii') : 'Brak kategorii'
                    ],
                    'status' => $pet['status'] ?? 'unknown',
                    'photoUrls' => $pet['photoUrls'] ?? [],
                    'tags' => $pet['tags'] ?? []
                ];

                return view('pets.show', [
                    'pet' => $processedPet,
                    'insecureConnection' => !$secure
                ]);
            } else {
                Log::error('Failed to fetch pet: ' . $response->body());

                $message = 'Nie znaleziono zwierzęcia o podanym ID.';
                if (!$secure) {
                    $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
                }

                return redirect()->route('pets.index')->with('error', $message);
            }
        } catch (Exception $e) {
            Log::error('Exception while fetching pet: ' . $e->getMessage());

            $message = 'Wystąpił błąd podczas komunikacji z API: ' . $e->getMessage();
            if ($this->secureConnectionFailed) {
                $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
            }

            return redirect()->route('pets.index')->with('error', $message);
        }
    }

    /**
     * Wyświetla formularz dodawania nowego zwierzęcia
     */
    public function create()
    {
        return view('pets.create');
    }

    /**
     * Zapisuje nowe zwierzę
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'status' => 'required|in:available,pending,sold',
            'photo_url' => 'nullable|url',
        ]);

        try {
            $result = $this->safeRequest('post', $this->apiUrl . '/pet', [
                'name' => $request->name,
                'category' => [
                    'id' => 0,
                    'name' => $request->category_name
                ],
                'photoUrls' => [$request->photo_url ?? ''],
                'tags' => [],
                'status' => $request->status
            ]);

            $response = $result['response'];
            $secure = $result['secure'];

            if ($response->successful()) {
                $message = 'Zwierzę zostało dodane pomyślnie.';
                if (!$secure) {
                    $message .= ' UWAGA: Operacja została wykonana przez niezabezpieczone połączenie.';
                }

                return redirect()->route('pets.index')->with('success', $message);
            } else {
                Log::error('Failed to create pet: ' . $response->body());

                $message = 'Nie udało się dodać zwierzęcia: ' . $response->body();
                if (!$secure) {
                    $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
                }

                return redirect()->route('pets.create')->with('error', $message)->withInput();
            }
        } catch (Exception $e) {
            Log::error('Exception while creating pet: ' . $e->getMessage());

            $message = 'Wystąpił błąd podczas komunikacji z API: ' . $e->getMessage();
            if ($this->secureConnectionFailed) {
                $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
            }

            return redirect()->route('pets.create')->with('error', $message)->withInput();
        }
    }

    /**
     * Wyświetla formularz edycji zwierzęcia
     */
    public function edit($id)
    {
        try {
            $result = $this->safeRequest('get', $this->apiUrl . '/pet/' . $id);

            $response = $result['response'];
            $secure = $result['secure'];

            if ($response->successful()) {
                $pet = $response->json();

                // Debugowanie surowych danych
                Log::debug('Raw pet data from API:', ['pet' => $pet]);

                // Przetwarzamy dane
                $processedPet = [
                    'id' => $pet['id'] ?? 0,
                    'name' => $pet['name'] ?? 'Brak nazwy',
                    'category' => [
                        'id' => $pet['category']['id'] ?? 0,
                        'name' => isset($pet['category']) ? ($pet['category']['name'] ?? 'Brak kategorii') : 'Brak kategorii'
                    ],
                    'status' => $pet['status'] ?? 'unknown',
                    'photoUrls' => $pet['photoUrls'] ?? [],
                    'tags' => $pet['tags'] ?? []
                ];

                // Debugowanie
                Log::debug('Processed pet data for view:', ['pet' => $processedPet]);

                return view('pets.edit', [
                    'pet' => $processedPet,
                    'insecureConnection' => !$secure
                ]);
            } else {
                Log::error('Failed to fetch pet for edit: ' . $response->body());

                $message = 'Nie znaleziono zwierzęcia o podanym ID.';
                if (!$secure) {
                    $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
                }

                return redirect()->route('pets.index')->with('error', $message);
            }
        } catch (Exception $e) {
            Log::error('Exception while fetching pet for edit: ' . $e->getMessage());

            $message = 'Wystąpił błąd podczas komunikacji z API: ' . $e->getMessage();
            if ($this->secureConnectionFailed) {
                $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
            }

            return redirect()->route('pets.index')->with('error', $message);
        }
    }

    /**
     * Aktualizuje dane zwierzęcia
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'status' => 'required|in:available,pending,sold',
            'photo_url' => 'nullable|url',
        ]);

        try {
            $result = $this->safeRequest('put', $this->apiUrl . '/pet', [
                'id' => (int)$id,
                'name' => $request->name,
                'category' => [
                    'id' => $request->category_id ?? 0,
                    'name' => $request->category_name
                ],
                'photoUrls' => [$request->photo_url ?? ''],
                'tags' => [],
                'status' => $request->status
            ]);

            $response = $result['response'];
            $secure = $result['secure'];

            if ($response->successful()) {
                $message = 'Dane zwierzęcia zostały zaktualizowane.';
                if (!$secure) {
                    $message .= ' UWAGA: Operacja została wykonana przez niezabezpieczone połączenie.';
                }

                return redirect()->route('pets.show', $id)->with('success', $message);
            } else {
                Log::error('Failed to update pet: ' . $response->body());

                $message = 'Nie udało się zaktualizować danych zwierzęcia: ' . $response->body();
                if (!$secure) {
                    $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
                }

                return redirect()->route('pets.edit', $id)->with('error', $message)->withInput();
            }
        } catch (Exception $e) {
            Log::error('Exception while updating pet: ' . $e->getMessage());

            $message = 'Wystąpił błąd podczas komunikacji z API: ' . $e->getMessage();
            if ($this->secureConnectionFailed) {
                $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
            }

            return redirect()->route('pets.edit', $id)->with('error', $message)->withInput();
        }
    }

    /**
     * Usuwa zwierzę
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        try {
            $result = $this->safeRequest('delete', $this->apiUrl . '/pet/' . $id);

            $response = $result['response'];
            $secure = $result['secure'];

            if ($response->successful()) {
                $message = 'Zwierzę zostało usunięte.';
                if (!$secure) {
                    $message .= ' UWAGA: Operacja została wykonana przez niezabezpieczone połączenie.';
                }

                return redirect()->route('pets.index')->with('success', $message);
            } else {
                Log::error('Failed to delete pet: ' . $response->body());

                $message = 'Nie udało się usunąć zwierzęcia: ' . $response->body();
                if (!$secure) {
                    $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
                }

                return redirect()->route('pets.index')->with('error', $message);
            }
        } catch (Exception $e) {
            Log::error('Exception while deleting pet: ' . $e->getMessage());

            $message = 'Wystąpił błąd podczas komunikacji z API: ' . $e->getMessage();
            if ($this->secureConnectionFailed) {
                $message .= ' UWAGA: Próba została wykonana przez niezabezpieczone połączenie.';
            }

            return redirect()->route('pets.index')->with('error', $message);
        }
    }
}
