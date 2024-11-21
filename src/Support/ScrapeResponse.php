<?php

namespace Eelcol\LaravelScrapers\Support;

use Illuminate\Http\Client\Response;

class ScrapeResponse
{
    protected ?array $decoded = null;

    public static function fromResponse(Response $response, bool $json = false): self
    {
        if ($json) {
            $body = $response->json('body') ?? '';
            if ($response->json('base64')) {
                $body = base64_decode($body);
            }

            return new self(
                body: $body,
                status: $response->json('status'),
                headers: $response->json('headers') ?? [],
            );
        }

        $headers = [];
        foreach ($response->headers() as $header_name => $header_value) {
            $headers[] = ['name' => $header_name, 'value' => $header_value[0]];
        }

        return new self(
            body: $response->body(),
            status: $response->status(),
            headers: $headers,
        );
    }

    public static function create(
        string $body,
        int $status,
        array $headers = [],
    ): self
    {
        return new self (
            body: $body,
            status: $status,
            headers: $headers,
        );
    }

    public function __construct(
        protected string $body,
        protected int $status,
        protected array $headers = [],
    ) {
        //
    }

    public function body(): string
    {
        return $this->body;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function json($key = null, $default = null)
    {
        if (! $this->decoded) {
            $this->decoded = json_decode($this->body(), true);
        }

        if (is_null($key)) {
            return $this->decoded;
        }

        return data_get($this->decoded, $key, $default);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $key): mixed
    {
        foreach ($this->headers as $header) {
            if ($header['name'] == $key) {
                return $header['value'];
            }
        }

        return null;
    }
}
