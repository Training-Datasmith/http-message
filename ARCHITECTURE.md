# Architecture: psr/http-message (PSR-7)

## Purpose

This package defines PSR-7: HTTP Message Interfaces. It provides a complete set
of value-object contracts for HTTP request and response messages, enabling
interoperability between HTTP clients, servers, and middleware without coupling
to any specific implementation.

## PSR Standard

**PSR-7** — https://www.php-fig.org/psr/psr-7/

## Directory Structure

```
src/
  Message_Interface.php          — Base interface; shared request/response methods
  Request_Interface.php          — Outgoing client-side request (extends Message)
  Response_Interface.php         — Incoming/outgoing response (extends Message)
  Server_Request_Interface.php   — Incoming server-side request (extends Request)
  Stream_Interface.php           — Wraps a PHP stream resource
  Uploaded_File_Interface.php    — Represents a file upload from multipart form data
  Uri_Interface.php              — RFC 3986 URI value object
```

## Key Design Decisions

### Immutability (with*() pattern)
All message objects are immutable. Methods that would change state are prefixed
with `with_` and return a new instance containing the modification, leaving the
original unchanged. This makes it safe to pass messages between middleware layers
without defensive cloning.

### Message/Request/ServerRequest hierarchy
- `Message_Interface` — protocol version, headers, body stream.
- `Request_Interface` extends it — adds method, URI, request target.
- `Server_Request_Interface` extends that — adds server params, cookies, query
  params, uploaded files, parsed body, and arbitrary attributes.

The hierarchy reflects that a server request is a specialised request that carries
additional SAPI-derived data not present in outgoing client requests.

### Body as a stream
The body is always a `Stream_Interface`, not a string. This enables streaming of
large payloads (file uploads, video) without loading them entirely into memory.
Callers must check `is_readable()` and `is_seekable()` before reading.

### Header case-insensitivity
Header names are case-insensitive per the HTTP specification. get_header('content-type')
and get_header('Content-Type') MUST return the same value. However, the original
casing used at set time is preserved when iterating headers.

### URI as a value object
`Uri_Interface` models an RFC 3986 URI with typed accessors for each component.
Immutability is maintained via `with_*()` methods, same as messages.

## Extension Points

- Implement any interface to build a custom PSR-7 library.
- Decorate `Server_Request_Interface` in middleware to add derived attributes
  (e.g., parsed JWT claims, route parameters).
- Wrap `Stream_Interface` to add filtering, compression, or encryption on the fly.

## Dependency Flow

```
HTTP layer (web server / client)
    └── Server_Request_Interface  (created from SAPI superglobals)
            └── Application / Middleware
                    └── Response_Interface  (returned to the HTTP layer)
```

All components communicate exclusively through these interfaces, making the
middleware stack implementation-agnostic.
