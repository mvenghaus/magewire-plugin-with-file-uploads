<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Adapter;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MVenghaus\MagewirePluginWithFileUploads\Helper\Security as SecurityHelper;
use MVenghaus\MagewirePluginWithFileUploads\Helper\TempFile as TempFileHelper;
use MVenghaus\MagewirePluginWithFileUploads\Model\Uploader\TemporaryUploader;

class Local extends AbstractAdapter
{
    public const NAME = 'local';

    public function __construct(
        private readonly Filesystem $fileSystem,
        private readonly TempFileHelper $tempFileHelper,
        DateTime $dateTime,
        SecurityHelper $securityHelper,
        FileDriver $fileDriver,
        RequestInterface $request
    ) {
        parent::__construct($dateTime, $securityHelper, $fileDriver, $request);
    }


    /**
     * @param array<TemporaryUploader> $files
     * @throws FileSystemException
     */
    public function stash(array $files): array
    {
        $paths = [];

        foreach ($files as $file) {
            $fileDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::TMP);
            $name = $this->tempFileHelper->generateHashNameWithOriginalNameEmbedded($file->getOriginalFileName());

            $file->save($fileDirectory->getAbsolutePath('magewire'), $name);
            $paths[] = $file->getUploadedFileName();
        }

        return $paths;
    }

    /**
     * @throws FileSystemException
     */
    public function store(array $paths, string $directory = null): array
    {
        $fileDirectoryTmp = $this->fileSystem->getDirectoryWrite(DirectoryList::TMP);
        $fileDirectoryUpload = $this->fileSystem->getDirectoryWrite(DirectoryList::UPLOAD);

        return array_map(function ($tmp) use ($fileDirectoryTmp, $fileDirectoryUpload) {
            $path = 'magewire' . DIRECTORY_SEPARATOR . $tmp;

            if ($fileDirectoryTmp->isFile($path)) {
                if ($fileDirectoryTmp->copyFile($path, $path, $fileDirectoryUpload)) {
                    return $fileDirectoryUpload->getRelativePath($path);
                }
            }

            return null;
        }, $paths);
    }
}
