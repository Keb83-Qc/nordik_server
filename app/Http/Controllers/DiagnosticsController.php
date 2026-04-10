<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiagnosticsController extends Controller
{
    /**
     * Reçoit les erreurs JavaScript du front-end.
     * Protégé par CSRF + rate-limit (10 req/min/IP).
     */
    public function logJsError(Request $request): JsonResponse
    {
        $type    = Str::limit($request->input('type', 'js_error'), 50);
        $message = Str::limit($request->input('message', 'Unknown JS error'), 300);

        SystemLog::record('warning', "[JS] {$type}: {$message}", [
            'source'   => Str::limit($request->input('source', ''), 300),
            'line'     => $request->input('line', ''),
            'column'   => $request->input('column', ''),
            'stack'    => Str::limit($request->input('stack', ''), 600),
            'page_url' => Str::limit($request->input('url', ''), 300),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Reçoit les Web Vitals (LCP / CLS / INP / FCP / TTFB).
     * CSRF + rate-limit pour limiter l'abus.
     */
    public function logWebVitals(Request $request): JsonResponse
    {
        $metric = Str::limit((string) $request->input('metric', 'unknown'), 24);
        $value  = (float) $request->input('value', 0);
        $rating = Str::limit((string) $request->input('rating', 'unknown'), 16);

        SystemLog::record('info', "[WebVital] {$metric}", [
            'metric'   => $metric,
            'value'    => $value,
            'rating'   => $rating,
            'delta'    => (float) $request->input('delta', 0),
            'id'       => Str::limit((string) $request->input('id', ''), 120),
            'nav_type' => Str::limit((string) $request->input('navigationType', ''), 40),
            'url'      => Str::limit((string) $request->input('url', ''), 300),
        ]);

        return response()->json(['ok' => true]);
    }
}
