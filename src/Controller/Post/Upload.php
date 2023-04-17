<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Controller\Post;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use MVenghaus\MagewirePluginWithFileUploads\Api\UploadAdapterInterface;
use MVenghaus\MagewirePluginWithFileUploads\Model\Uploader\TemporaryUploaderFactory;

class Upload implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly JsonFactory $resultJsonFactory,
        private readonly UploadAdapterInterface $uploadAdapter,
        private readonly TemporaryUploaderFactory $temporaryUploaderFactory
    ) {
    }

    public function execute(): Json
    {
        // CLEAN UP THE TMP DIR FIRST...

        $result = $this->resultJsonFactory->create();

        if (!$this->uploadAdapter->hasCorrectSignature() || $this->uploadAdapter->signatureHasNotExpired()) {
            return $result->setStatusHeader(401);
        }

        try {
            $files = $this->request->getFiles('files', []);
            $targets = [];

            foreach (array_keys($files) as $file) {
                $target = $this->temporaryUploaderFactory->create(['fileId' => 'files[' . $file . ']']);

                $target->setAllowCreateFolders(false);
                $target->setAllowRenameFiles(true);
                $target->setFilenamesCaseSensitivity(false);

                $target->validateFile();

                $targets[] = $target;
            }

            $paths = $this->uploadAdapter->stash($targets);

            return $result->setData([
                'paths' => $paths
            ]);
        } catch (Exception $exception) {
            return $result->setData([
                'message' => $exception->getMessage(),
                'code' => 422
            ]);
        }
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): bool
    {
        return true;
    }
}
