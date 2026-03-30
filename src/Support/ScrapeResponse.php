<?php

namespace Eelcol\LaravelScrapers\Support;

use Eelcol\LaravelScrapers\Exceptions\UnknownScraperError;
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

            if (is_null($response->json('status'))) {
                throw new UnknownScraperError($response);
            }

            return new self(
                body: $body,
                status: $response->json('status'),
                headers: $response->json('headers') ?? [],
                cookies: $response->json('cookies') ?? [],
            );
        }

        $headers = [];
        foreach ($response->headers() as $header_name => $header_value) {
            $headers[] = ['name' => $header_name, 'value' => $header_value[0]];
        }

        $cookies = [];
        foreach ($response->cookies() as $cookie) {
            $cookies[] = ['name' => $cookie->getName(), 'value' => $cookie->getValue(), 'domain' => $cookie->getDomain()]; 
        }

        return new self(
            body: $response->body(),
            status: $response->status(),
            headers: $headers,
            cookies: $cookies,
        );
    }

    public static function create(
        string $body,
        int $status,
        array $headers = [],
        array $cookies = [],
    ): self
    {
        return new self (
            body: $body,
            status: $status,
            headers: $headers,
            cookies: $cookies,
        );
    }

    /**
     * @param string $body
     * @param int $status
     * @param array<string, string> $headers Headers as key-value ["header1" => "value", "header2" => "value"].
     * @param array<int, array{name: string, value: string, domain: string}> $cookies List of cookies; each element has name, value and domain.
     */
    public function __construct(
        protected string $body,
        protected int $status,
        protected array $headers = [],
        protected array $cookies = [],
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

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getCookie(string $key): mixed
    {
        foreach ($this->cookies as $cookie) {
            if ($cookie['name'] == $key) {
                return $cookie['value'];
            }
        }
        return null;
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
