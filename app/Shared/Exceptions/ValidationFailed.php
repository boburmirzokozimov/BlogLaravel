<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Throwable;

final class ValidationFailed extends DomainException
{
    /**
     * @var string[]
     */
    private array $errors;

    /**
     * @param string[] $errors Array of field => [translation_key => params] or field => [messages]
     * Example: ['email' => ['errors.validation.email.invalid' => [], 'errors.validation.email.required' => []]]
     * Or simple: ['email' => ['errors.validation.email.required']]
     */
    public function __construct(
        array $errors = [],
        ?Throwable $previous = null
    ) {
        $this->errors = $errors;

        parent::__construct(
            'errors.validation_failed',
            [],
            0,
            $previous
        );
    }

    public function errorCode(): string
    {
        return 'VALIDATION_FAILED';
    }

    public function status(): int
    {
        return 422;
    }

    public function context(): array
    {
        return $this->getTranslatedErrors();
    }

    /**
     * Get errors with multi-language translations.
     */
    private function getTranslatedErrors(): array
    {
        $translated = [];

        foreach ($this->errors as $field => $messages) {
            $translated[$field] = [];

            foreach ($messages as $keyOrMessage => $params) {
                // If it's a translation key format (e.g., 'errors.validation.email.required')
                if (is_string($keyOrMessage) && str_starts_with($keyOrMessage, 'errors.validation.')) {
                    $translated[$field][] = [
                        'en' => __(is_array($params) ? $keyOrMessage : $params, is_array($params) ? $params : [], 'en'),
                        'ru' => __(is_array($params) ? $keyOrMessage : $params, is_array($params) ? $params : [], 'ru'),
                    ];
                } // If params is numeric index (simple array of translation keys)
                elseif (is_numeric($keyOrMessage) && is_string($params)) {
                    $translated[$field][] = [
                        'en' => __($params, [], 'en'),
                        'ru' => __($params, [], 'ru'),
                    ];
                } // Fallback: treat as plain message
                else {
                    $translated[$field][] = [
                        'en' => is_string($params) ? $params : $keyOrMessage,
                        'ru' => is_string($params) ? $params : $keyOrMessage,
                    ];
                }
            }
        }

        return $translated;
    }
}
