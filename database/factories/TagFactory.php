<?php

namespace Database\Factories;

use App\Domain\Blog\ValueObjects\Slug;
use App\Infrastructure\Blog\EloquentTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<EloquentTag>
 */
class TagFactory extends Factory
{
    protected $model = EloquentTag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'id' => Uuid::uuid4()->toString(),
            'name' => ucwords($name),
            'slug' => Slug::fromTitle($name)->value(),
        ];
    }
}
