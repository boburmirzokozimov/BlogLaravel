<?php

namespace App\Domain\Blog\Entity;

use App\Domain\Blog\ValueObjects\Slug;
use App\Domain\Blog\ValueObjects\Title;
use App\Shared\ValueObjects\Id;

class Tag
{
    private Id $id;

    private Title $name;

    private Slug $slug;

    private function __construct()
    {
    }

    public static function create(Id $id, Title $name, Slug $slug): Tag{
        $tag = new self();
        $tag->id = $id;
        $tag->name = $name;
        $tag->slug = $slug;
        return $tag;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): Title
    {
        return $this->name;
    }

    public function getSlug(): Slug
    {
        return $this->slug;
    }
}
