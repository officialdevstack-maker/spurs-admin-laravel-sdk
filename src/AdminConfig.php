<?php

namespace SpursCloud\Admin;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

/**
 * Client for the Spurs admin config API. Fetches this app's effective
 * configuration ({ KEY: value }) from the control plane and caches it, so an
 * app can read credentials from admin instead of scattering them across .env.
 */
class AdminConfig
{
    private array $config;

    public function __construct(
        private readonly string $url,
        private readonly ?string $app,
        private readonly ?string $secret,
        private readonly int $cacheTtl = 300,
        private readonly bool $fallback = true,
    ) {
    }

    /** All key/value pairs admin holds for this app. */
    public function all(): array
    {
        return $this->config ??= $this->fetch();
    }

    /** A single key, with an optional default (falls back to env when missing). */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->all()[$key] ?? null;
        if ($value !== null) {
            return $value;
        }

        return $this->fallback ? (env($key, $default) ?? $default) : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }

    /** Bypass the cache and refetch (e.g. after rotating a credential). */
    public function refresh(): array
    {
        Cache::forget($this->cacheKey());

        return $this->config = $this->fetch();
    }

    private function fetch(): array
    {
        if (! $this->app || ! $this->secret) {
            return [];
        }

        $loader = function (): array {
            try {
                $res = (new Client(['timeout' => 4]))->get(rtrim($this->url, '/').'/api/config', [
                    'query' => ['app' => $this->app],
                    'headers' => ['x-admin-bootstrap' => $this->secret, 'Accept' => 'application/json'],
                    'http_errors' => false,
                ]);

                if ($res->getStatusCode() === 200) {
                    $body = json_decode((string) $res->getBody(), true);

                    return is_array($body['config'] ?? null) ? $body['config'] : [];
                }
            } catch (\Throwable) {
                // fall through — admin unreachable
            }

            return [];
        };

        if ($this->cacheTtl <= 0) {
            return $loader();
        }

        return Cache::remember($this->cacheKey(), $this->cacheTtl, $loader);
    }

    private function cacheKey(): string
    {
        return 'spurs-admin:config:'.$this->app;
    }
}
