<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Component\Form\Traits;

use Magento\Framework\App\ObjectManager;
use Magewirephp\Magewire\Model\Action\SyncInput;
use MVenghaus\MagewirePluginWithFileUploads\Api\UploadAdapterInterface;
use MVenghaus\MagewirePluginWithFileUploads\Model\Property\TemporaryUploadedFile;

trait WithFileUploads
{
    public function startUpload($name, $fileInfo, $isMultiple)
    {
        $adapter = ObjectManager::getInstance()->get(UploadAdapterInterface::class);

        $this->emit(
            $adapter->getGenerateSignedUploadUrlEvent(),
            $name,
            $adapter->generateSignedUploadUrl()
        )->self();
    }

    public function finishUpload($name, $tmpPath, $isMultiple): void
    {
        $syncInput = ObjectManager::getInstance()->get(SyncInput::class);

        if ($isMultiple) {
            $temporaryUploadedFiles = [];
            foreach ($tmpPath as $tmpFile) {
                $temporaryUploadedFiles[] = new TemporaryUploadedFile($tmpFile);
            }

            $this->emit('upload:finished', $name, $tmpPath)->self();
            $syncInput->handle($this, ['name' => $name, 'value' => $temporaryUploadedFiles]);
        } else {
            $temporaryUploadedFile = new TemporaryUploadedFile($tmpPath[0]);

            $this->emit('upload:finished', $name, $tmpPath)->self();
            $syncInput->handle($this, ['name' => $name, 'value' => $temporaryUploadedFile]);
        }
    }
}
