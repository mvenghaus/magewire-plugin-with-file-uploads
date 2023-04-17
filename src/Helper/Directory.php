<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class Directory
{
    public static function getTmpDirectory(): WriteInterface
    {
        $filesystem = ObjectManager::getInstance()->get(Filesystem::class);

        return $filesystem->getDirectoryWrite(DirectoryList::TMP);
    }

    public static function getUploadDirectory(): WriteInterface
    {
        $filesystem = ObjectManager::getInstance()->get(Filesystem::class);

        return $filesystem->getDirectoryWrite(DirectoryList::UPLOAD);
    }
}