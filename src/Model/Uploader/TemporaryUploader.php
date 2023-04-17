<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Uploader;

use Magento\MediaStorage\Model\File\Uploader;

class TemporaryUploader extends Uploader
{
    public function getOriginalFileName(): string
    {
        return $this->_file['name'];
    }
}
