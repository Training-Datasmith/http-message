<?php

declare(strict_types=1);

/**
 * Example: PSR-7 HTTP message immutability and the with*() pattern.
 *
 * PSR-7 objects are immutable. Every "mutating" method returns a new instance.
 * This allows middleware to modify requests/responses without affecting other
 * layers that hold a reference to the original object.
 */

use Psr\Http\Message\Request_Interface;
use Psr\Http\Message\Response_Interface;
use Psr\Http\Message\Server_Request_Interface;

// --- Middleware: add a correlation ID header to every outgoing response ---

final class Correlation_Id_Middleware
{
    public function process(
        Server_Request_Interface $request,
        callable $next,
    ): Response_Interface {
        // with_header() returns a NEW request — the original is unchanged.
        $correlation_id  = $request->get_header_line('X-Correlation-ID')
            ?: bin2hex(random_bytes(8));

        $tagged_request = $request->with_attribute('correlation_id', $correlation_id);

        /** @var Response_Interface $response */
        $response = $next($tagged_request);

        // Append the correlation ID to the response so the client can trace it.
        return $response->with_header('X-Correlation-ID', $correlation_id);
    }
}

// --- Working with headers ---

function describe_response(Response_Interface $response): void
{
    echo "Status: {$response->get_status_code()} {$response->get_reason_phrase()}\n";

    // get_headers() returns string[][] — name => array of values.
    foreach ($response->get_headers() as $name => $values) {
        echo "{$name}: " . implode(', ', $values) . "\n";
    }

    // get_header_line() collapses multi-value headers into one comma-joined string.
    $content_type = $response->get_header_line('Content-Type');
    echo "Content-Type (line): {$content_type}\n";
}

// --- Working with the URI ---

function build_paginated_uri(\Psr\Http\Message\Uri_Interface $base_uri, int $page): \Psr\Http\Message\Uri_Interface
{
    // Each with_*() call returns a new Uri — the original is unchanged.
    $query = http_build_query(['page' => $page, 'per_page' => 25]);
    return $base_uri->with_query($query);
}

// --- Server request attributes ---

function get_authenticated_user_id(Server_Request_Interface $request): ?int
{
    // Attributes are arbitrary values attached by middleware (e.g., JWT parser).
    $user_id = $request->get_attribute('authenticated_user_id');
    return is_int($user_id) ? $user_id : null;
}

// --- Stream reading ---

function read_json_body(Request_Interface $request): mixed
{
    $body = $request->get_body();

    if ($body->is_seekable()) {
        $body->rewind();
    }

    $content = $body->get_contents();
    return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
}
