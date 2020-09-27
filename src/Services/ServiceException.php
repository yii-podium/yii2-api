<?php

declare(strict_types=1);

namespace Podium\Api\Services;

use Throwable;
use yii\base\Exception;

class ServiceException extends Exception
{
    private array $errorList;

    public function __construct(array $errorList = [], $message = '', $code = 0, Throwable $previous = null)
    {
        $this->errorList = $errorList;
        parent::__construct($message, $code, $previous);
    }

    public function getName(): string
    {
        return 'Service Exception';
    }

    public function getErrorList(): array
    {
        return $this->errorList;
    }
}
