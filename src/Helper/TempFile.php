<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Helper;

class TempFile
{
    public static function generateHashNameWithOriginalNameEmbedded(string $file): string
    {
        $hash = uniqid();
        $meta = str_replace('/', '_', '-meta' . base64_encode($file) . '-');
        $extension = '.' . pathinfo($file, PATHINFO_EXTENSION);

        return $hash . $meta . $extension;
    }

    public static function extractOriginalNameFromFilePath(string $file): string
    {
        return base64_decode(
            array_first(
                explode('-', array_last(explode('-meta', str_replace('_', '/', $file))))
            )
        );
    }
}
