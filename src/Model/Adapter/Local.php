<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Adapter;

use MVenghaus\MagewirePluginWithFileUploads\Helper\Directory;
use MVenghaus\MagewirePluginWithFileUploads\Helper\TempFile;

class Local extends AbstractAdapter
{
    public const NAME = 'local';

    public function stash(array $files): array
    {
        $paths = [];

        foreach ($files as $file) {
            $fileDirectory = Directory::getTmpDirectory();
            $name = TempFile::generateHashNameWithOriginalNameEmbedded($file->getOriginalFileName());

            $file->save($fileDirectory->getAbsolutePath(), $name);
            $paths[] = $file->getUploadedFileName();
        }

        return $paths;
    }

    public function store(array $paths, string $directory = null): array
    {
        $fileDirectoryTmp = Directory::getTmpDirectory();
        $fileDirectoryUpload = $directory ?? Directory::getUploadDirectory();

        return array_map(function ($tmp) use ($fileDirectoryTmp, $fileDirectoryUpload) {
            $path = $tmp;

            if ($fileDirectoryTmp->isFile($path)) {
                if ($fileDirectoryTmp->copyFile($path, $path, $fileDirectoryUpload)) {
                    return $fileDirectoryUpload->getRelativePath($path);
                }
            }

            return null;
        }, $paths);
    }
}
