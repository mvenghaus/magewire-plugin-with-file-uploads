<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Adapter;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MVenghaus\MagewirePluginWithFileUploads\Helper\Security as SecurityHelper;
use MVenghaus\MagewirePluginWithFileUploads\Api\UploadAdapterInterface;

abstract class AbstractAdapter implements UploadAdapterInterface
{
    public const NAME = '';

    public function __construct(
        protected readonly DateTime $dateTime,
        protected readonly SecurityHelper $securityHelper,
        protected readonly FileDriver $fileDriver,
        protected readonly RequestInterface $request
    ) {
    }

    public function generateSignedUploadUrl(): string
    {
        return $this->securityHelper->generateRouteSignatureUrl($this->getRoute(), [
            UploadAdapterInterface::QUERY_PARAM_EXPIRES => $this->dateTime->gmtTimestamp() + 1900,
            UploadAdapterInterface::QUERY_PARAM_ADAPTER => $this->getName()
        ]);
    }

    public function getGenerateSignedUploadUrlEvent(): string
    {
        return 'upload:generatedSignedUrl';
    }

    public function getDriver(): DriverInterface
    {
        return $this->fileDriver;
    }

    public function getName(): string
    {
        return $this::NAME;
    }

    public function getRoute(): string
    {
        return 'magewire-with-file-uploads/post/upload';
    }

    /**
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function hasCorrectSignature(): bool
    {
        $signature = $this->securityHelper->generateRouteSignature($this->getRoute(), [
            UploadAdapterInterface::QUERY_PARAM_EXPIRES => $this->request->getUserParam(UploadAdapterInterface::QUERY_PARAM_EXPIRES, 0),
            UploadAdapterInterface::QUERY_PARAM_ADAPTER => $this->getName()
        ]);

        return $this->request->getUserParam(UploadAdapterInterface::QUERY_PARAM_SIGNATURE) === $signature;
    }

    public function signatureHasNotExpired(): bool
    {
        $timestamp = $this->dateTime->gmtTimestamp();
        return $timestamp > (int) $this->request->getUserParam(UploadAdapterInterface::QUERY_PARAM_EXPIRES, $timestamp);
    }
}
