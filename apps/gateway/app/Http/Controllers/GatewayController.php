<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GatewayController extends Controller
{
    private const REQUEST_HEADERS = [
        'accept',
        'accept-language',
        'authorization',
        'content-type',
        'idempotency-key',
        'x-correlation-id',
        'x-request-id',
    ];

    private const RESPONSE_HEADERS = [
        'cache-control',
        'content-disposition',
        'content-type',
        'retry-after',
        'x-correlation-id',
        'x-request-id',
    ];

    public function __invoke(Request $request, string ...$routeParameters): Response
    {
        $path = preg_replace('#^api/v1/?#', '', $request->path());
        $upstream = $this->upstreamFor($path);

        if ($upstream === null) {
            return $this->problemResponse($request, 404, 'Route introuvable', 'Aucune route Gateway ne correspond à cette requête.');
        }

        $requestId = $request->header('x-request-id') ?: (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);
        $headers = [];

        foreach (self::REQUEST_HEADERS as $header) {
            if ($value = $request->header($header)) {
                $headers[$header] = $value;
            }
        }
        $headers['x-request-id'] = $requestId;

        try {
            $upstreamResponse = Http::timeout(config('gateway.timeout'))
                ->withHeaders($headers)
                ->send($request->method(), rtrim(config("gateway.upstreams.{$upstream}"), '/').'/api/v1/'.$path, [
                    'allow_redirects' => false,
                    'body' => $request->getContent(),
                    'query' => $request->query(),
                ]);
        } catch (ConnectionException) {
            return $this->problemResponse(
                $request,
                503,
                'Service indisponible',
                'Le service demandé est temporairement indisponible.',
            );
        }

        $responseHeaders = [];

        foreach (self::RESPONSE_HEADERS as $header) {
            if ($value = $upstreamResponse->header($header)) {
                $responseHeaders[$header] = $value;
            }
        }
        $responseHeaders['x-request-id'] ??= $requestId;

        return response($upstreamResponse->body(), $upstreamResponse->status(), $responseHeaders);
    }

    private function upstreamFor(string $path): ?string
    {
        return match (true) {
            Str::startsWith($path, 'auth/') => 'auth',
            Str::startsWith($path, ['assets', 'movements/', 'movements', 'inventory-campaigns/', 'inventory-campaigns']) => 'asset',
            $path === 'history' => 'history',
            Str::startsWith($path, 'reports/') => 'report',
            default => null,
        };
    }

    private function problemResponse(Request $request, int $status, string $title, string $detail): Response
    {
        $requestId = $request->attributes->get('request_id')
            ?: $request->header('x-request-id')
            ?: (string) Str::uuid();

        return response()->json([
            'type' => 'about:blank',
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'request_id' => $requestId,
        ], $status, [
            'Content-Type' => 'application/problem+json',
            'X-Request-Id' => $requestId,
        ]);
    }
}