<?php

namespace App\Shared\Exceptions;

use RuntimeException;
use Throwable;

abstract class DomainException extends RuntimeException
{
    protected string $translationKey;
    protected array $translationParams = [];

    public function __construct(
        string $translationKey,
        array $translationParams = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->translationKey = $translationKey;
        $this->translationParams = $translationParams;
        
        // Use English message as fallback
        $message = __($translationKey, $translationParams, 'en');
        parent::__construct($message, $code, $previous);
    }

    /** HTTP status code for transport layers */
    public function status(): int
    {
        return 422; // default Unprocessable Entity
    }

    /** Get the translation key for this exception */
    public function translationKey(): string
    {
        return $this->translationKey;
    }

    /** Get translation parameters */
    public function translationParams(): array
    {
        return $this->translationParams;
    }

    /** Get multi-language error messages */
    public function getMultiLangMessages(): array
    {
        return [
            'en' => __($this->translationKey, $this->translationParams, 'en'),
            'ru' => __($this->translationKey, $this->translationParams, 'ru'),
        ];
    }

    /** Standardized error response array */
    public function toArray(): array
    {
        $response = [
            'error' => [
                'code' => $this->errorCode(),
                'message' => $this->getMultiLangMessages(),
            ],
        ];

        $context = $this->context();
        if (!empty($context)) {
            $response['error']['context'] = $context;
        }

        return $response;
    }

    abstract public function errorCode(): string;

    /** Key/value context for logs/debugging (no secrets) */
    public function context(): array
    {
        return [];
    }
}
