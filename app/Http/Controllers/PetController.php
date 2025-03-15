<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PetController extends Controller
{
    //
    private $apiUrl = 'https://petstore.swagger.io/v2/pet';

    public function index()
    {
        try {
            $response = Http::get($this->apiUrl . '/pet/findPetsByStatus', [
                'status' => 'available'
            ]);
            if ($response->successful()) {
                $pets = $response->json();
                return view('pets.index', compact('pets'));
            } else {
                Log::error('Błąd pobierania zwierząd: ' . $response->body());
                return redirect()->back()->with('error', 'Błąd pobierania zwierząt');
            }
            $pets = $response->json();
            return view('pets.index', ['pets' => $pets]);
        } catch (\Exception $e) {
            Log::error('Błąd pobierania zwierząt: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Wystąpił błąd podczas łączenia z API: ' . $e->getMessage());
        }
    }
}
