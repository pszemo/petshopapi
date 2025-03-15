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

    public function create()
    {
        return view('pets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        try {
            $response = Http::post($this->apiUrl, [
                'name' => $request->name,
                'status' => $request->status,
            ]);
            if ($response->successful()) {
                return redirect()->route('pets.index')->with('success', 'Zwierzę zostało dodane');
            } else {
                Log::error('Błąd dodawania zwierzęcia: ' . $response->body());
                return redirect()->back()->with('error', 'Błąd dodawania zwierzęcia');
            }
        } catch (\Exception $e) {
            Log::error('Błąd dodawania zwierzęcia: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Wystąpił błąd podczas łączenia z API: ' . $e->getMessage());
        }
    }
}
