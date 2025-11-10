<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Create token resource from token string.
     */
    public static function fromToken(string $token, ?int $expiresIn = null): self
    {
        return new self([
            'token' => $token,
            'expires_in' => $expiresIn ?? auth('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->resource['token'],
            'token_type' => 'bearer',
            'expires_in' => $this->resource['expires_in'],
        ];
    }
}
