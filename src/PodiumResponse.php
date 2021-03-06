<?php

declare(strict_types=1);

namespace Podium\Api;

final class PodiumResponse
{
    private bool $result;
    private array $errors;
    private array $data;

    private function __construct(bool $result, array $errors = [], array $data = [])
    {
        $this->result = $result;
        $this->errors = $errors;
        $this->data = $data;
    }

    /**
     * Returns successful response.
     */
    public static function success(array $data = []): PodiumResponse
    {
        return new self(true, [], $data);
    }

    /**
     * Returns erroneous response.
     */
    public static function error(array $errors = []): PodiumResponse
    {
        return new self(false, $errors);
    }

    public function getResult(): bool
    {
        return $this->result;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
