<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Plugin\Magewirephp\Magewire\Model\Hydrator;

use Magewirephp\Magewire\Component;
use Magewirephp\Magewire\Model\Hydrator\Property;
use Magewirephp\Magewire\Model\RequestInterface;
use Magewirephp\Magewire\Model\ResponseInterface;
use MVenghaus\MagewirePluginWithFileUploads\Model\Property\TemporaryUploadedFile;

class PropertyPlugin
{
    public function afterHydrate(Property $subject, $result, Component $component, RequestInterface $request): void
    {
        foreach ($component->getPublicProperties() as $propertyName => $propertyValue) {
            if ($this->canUnserialize($propertyValue)) {
                $component->{$propertyName} = $this->unserializeFromRequest($propertyValue);
            }

            if ($this->canUnserializeMultiple($propertyValue)) {
                $component->{$propertyName} = $this->unserializeMultipleFromRequest($propertyValue);
            }
        }
    }

    public function beforeDehydrate(Property $subject, Component $component, ResponseInterface $response): array
    {
        foreach ($component->getPublicProperties(true) as $propertyName => $propertyValue) {
            if ($this->canSerialize($propertyValue)) {
                $component->{$propertyName} = $this->serializeForResponse($propertyValue);
            }

            if ($this->canSerializeMultiple($propertyValue)) {
                $component->{$propertyName} = $this->serializeMultipleForResponse($propertyValue);
            }
        }

        return [$component, $response];
    }

    private function canUnserialize($value): bool
    {
        return (is_string($value) && str_starts_with($value, 'magewire-file:'));
    }

    private function canUnserializeMultiple($value): bool
    {
        return (is_string($value) && str_starts_with($value, 'magewire-files:'));
    }

    private function canSerialize($propertyValue): bool
    {
        return $propertyValue instanceof TemporaryUploadedFile;
    }

    private function canSerializeMultiple($propertyValue): bool
    {
        if (is_array($propertyValue)) {
            foreach ($propertyValue as $value) {
                if (!($value instanceof TemporaryUploadedFile)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    private function unserializeFromRequest(string $value): TemporaryUploadedFile
    {
        $value = str_replace('magewire-file:', '', $value);

        return new TemporaryUploadedFile($value);
    }

    /**
     * @return array<TemporaryUploadedFile>
     */
    private function unserializeMultipleFromRequest(string $value): array
    {
        $value = str_replace('magewire-files:', '', $value);

        $tmpFiles = json_decode($value, true);

        $temporaryUploadedFiles = [];
        foreach ($tmpFiles as $tmpFile) {
            $temporaryUploadedFiles[] = new TemporaryUploadedFile($tmpFile);
        }

        return $temporaryUploadedFiles;
    }

    private function serializeForResponse(TemporaryUploadedFile $temporaryUploadedFile): string
    {
        return 'magewire-file:' . $temporaryUploadedFile->tmpFile;
    }

    /**
     * @param array<TemporaryUploadedFile> $temporaryUploadedFiles
     */
    private function serializeMultipleForResponse(array $temporaryUploadedFiles): string
    {
        $tmpFiles = array_map(
            fn(TemporaryUploadedFile $temporaryUploadedFile) => $temporaryUploadedFile->tmpFile,
            $temporaryUploadedFiles
        );

        return 'magewire-files:' . json_encode($tmpFiles);
    }
}
