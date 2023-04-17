<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Property;

use MVenghaus\MagewirePluginWithFileUploads\Helper\TempFile;

class TemporaryUploadedFile
{
    public function __construct(
        private string $tmpFile
    ) {
    }

    public function getTemporaryFileName(): string
    {
        return $this->tmpFile;
    }

    public function getOriginalFileName(): string
    {
        return TempFile::extractOriginalNameFromFilePath($this->tmpFile);
    }

    public function getMimeType(): string
    {
        return mime_content_type($this->tmpFile);
    }

    public function getFileSize(): int
    {
        return filesize($this->tmpFile);
    }
}
