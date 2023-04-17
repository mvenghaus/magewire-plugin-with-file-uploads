<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Property;


class TemporaryUploadedFile
{
    public function __construct(
        public string $tmpFile
    ) {
    }

    public function canUnserialize(string $value): bool
    {
        if (is_string($value) && str_starts_with($value, 'magewire-file')) {
            return true;
        }

        return false;
    }

    public function serialize(): string
    {
    }

    public function unserialize(): PropertyInterface
    {

    }
}
