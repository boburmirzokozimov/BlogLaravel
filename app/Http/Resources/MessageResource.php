<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // If resource is already an array with both languages, return it
        if (is_array($this->resource) && isset($this->resource['en'])) {
            return [
                'message' => $this->resource,
            ];
        }

        // Otherwise, return as is
        return [
            'message' => $this->resource,
        ];
    }

    /**
     * Create a bilingual message resource from translation key.
     */
    public static function trans(string $key, array $replace = []): static
    {
        $translations = [];
        
        foreach (['en', 'ru'] as $locale) {
            $filePath = lang_path("{$locale}/messages.php");
            if (file_exists($filePath)) {
                $messages = include $filePath;
                $translations[$locale] = $messages[$key] ?? $key;
                
                // Apply replacements
                foreach ($replace as $search => $value) {
                    $translations[$locale] = str_replace(":{$search}", (string) $value, $translations[$locale]);
                }
            } else {
                $translations[$locale] = $key;
            }
        }
        
        return new static($translations);
    }

    /**
     * Create a new message resource (deprecated - use trans instead).
     */
    public static function success(string $message): static
    {
        return new static($message);
    }
}

