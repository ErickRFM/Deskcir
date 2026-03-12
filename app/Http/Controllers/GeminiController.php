<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class GeminiController extends Controller
{
    public function preguntar(Request $request)
    {
        if (! $request->user()) {
            return response()->json([
                'ok' => false,
                'message' => 'Necesitas iniciar sesion para usar el asistente.',
            ], 401);
        }

        $validated = $request->validate([
            'mensaje' => ['required', 'string', 'max:4000'],
            'contexto' => ['nullable', 'string', 'max:1000'],
            'historial' => ['nullable', 'array', 'max:12'],
            'historial.*.role' => ['required_with:historial', 'string', 'in:user,model'],
            'historial.*.text' => ['required_with:historial', 'string', 'max:4000'],
        ]);

        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-2.5-flash');
        $timeout = max((int) config('services.gemini.timeout', 20), 1);

        if (blank($apiKey)) {
            throw ValidationException::withMessages([
                'mensaje' => 'Gemini no esta configurado todavia en este entorno.',
            ]);
        }

        $user = $request->user();
        $role = $user?->role?->name ?? 'guest';
        $context = trim((string) ($validated['contexto'] ?? ''));
        $history = collect($validated['historial'] ?? [])
            ->filter(fn ($item) => in_array($item['role'] ?? null, ['user', 'model'], true) && filled($item['text'] ?? null))
            ->take(-10)
            ->values();

        $prompt = "Eres Deskcir AI, un asistente para soporte tecnico y compras de tecnologia. "
            . "Responde en espanol claro, preciso, profesional y con buena continuidad conversacional. "
            . "Debes recordar el hilo reciente enviado en el historial y usarlo para interpretar mensajes breves como 'mas detallado', 'comparalo', 'mejor respuesta', 'eso' o 'esa opcion'. "
            . "Si el usuario pide pasos, dalos en orden. Si pide comparacion, compara las opciones y recomienda la mas conveniente con criterios claros. "
            . "Prioriza respuestas mas precisas y utiles sobre respuestas genericas. Si falta informacion, haz una sola pregunta concreta para destrabar. "
            . "Puedes usar emojis ligeros y profesionales cuando ayuden a comunicar mejor, sin exagerar. Rol del usuario: {$role}.";

        if ($context !== '') {
            $prompt .= " Contexto adicional de la pantalla actual: {$context}.";
        }

        $contents = $history->map(function ($item) {
            return [
                'role' => $item['role'],
                'parts' => [
                    ['text' => trim((string) $item['text'])],
                ],
            ];
        })->all();

        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $validated['mensaje']],
            ],
        ];

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    [
                        'systemInstruction' => [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                        'contents' => $contents,
                    ]
                );
        } catch (\Throwable $exception) {
            return response()->json([
                'ok' => false,
                'message' => 'No fue posible conectar con Gemini desde el servidor.',
                'error' => $exception->getMessage(),
            ], 503);
        }

        if ($response->failed()) {
            $apiError = $response->json('error.message') ?? 'Error desconocido';
            $friendlyMessage = str_contains(strtolower($apiError), 'reported as leaked')
                ? 'La API key actual de Gemini fue bloqueada por Google por exposicion publica. Reemplazala por una nueva para reactivar el chat.'
                : 'Gemini rechazo la solicitud en este momento.';

            return response()->json([
                'ok' => false,
                'message' => $friendlyMessage,
                'error' => $apiError,
            ], $response->status());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        return response()->json([
            'ok' => true,
            'message' => $text ?: 'No llego contenido util desde Gemini.',
        ]);
    }
}

