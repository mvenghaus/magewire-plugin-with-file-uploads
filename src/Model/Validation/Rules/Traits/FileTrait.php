<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules\Traits;

trait FileTrait
{
    public function isUploadedFile($value): bool
    {
        return $this->isValueFromUploadedFiles($value);
    }
}