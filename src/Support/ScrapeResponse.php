<?php

namespace Eelcol\LaravelScrapers\Support;

use Illuminate\Http\Client\Response;

class ScrapeResponse
{
    protected ?array $decoded = null;

    public static function fromResponse(Response $response, bool $json = false): self
    {
        if ($json) {
            return new self(
                body: $response->json('body') ?? '',
                status: $response->json('status')
            );
        }

        return new self(
            body: $response->body(),
            status: $response->status()
        );
    }

    public static function create(
        string $body,
        int $status
    ): self
    {
        return new self (
            body: $body,
            status: $status
        );
    }

    public function __construct(
        protected string $body,
        protected int $status,
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
}
