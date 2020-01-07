<?php

declare(strict_types=1);

namespace Apathy\Discuss\DataObjects\Image;

final class CreateImageRequest
{
    public string $path;

    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->path = $data['path'];
        return $request;
    }
}
