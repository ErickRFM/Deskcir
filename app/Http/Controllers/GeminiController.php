<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{

    public function preguntar(Request $request)
    {

        $pregunta = $request->mensaje;

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=AIzaSyDptvTK_iF2Art3NA65mnF2xMNBK7ZwD3I",
            [
                "contents" => [
                    [
                        "parts" => [
                            [
                                "text" => $pregunta
                            ]
                        ]
                    ]
                ]
            ]
        );

        return response()->json($response->json());

    }

}