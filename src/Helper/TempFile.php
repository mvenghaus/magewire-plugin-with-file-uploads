<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Helper;

class TempFile
{
    public static function generateHashNameWithOriginalNameEmbedded(string $file): string
    {
        $hash = uniqid();

        return $hash . '--meta--' . base64_encode($file);
    }

    public static function extractOriginalNameFromFilePath(string $file): string
    {
        $data = explode('--meta--', $file);

        return base64_decode(end($data));
    }
}

