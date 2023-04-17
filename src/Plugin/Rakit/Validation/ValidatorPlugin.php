<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Plugin\Rakit\Validation;

use MVenghaus\MagewirePluginWithFileUploads\Model\Property\TemporaryUploadedFile;
use Rakit\Validation\Validator;

class ValidatorPlugin
{
    public function beforeValidate(Validator $subject, array $inputs, array $rules, array $messages = []): array
    {
        $subject->setValidator('required', new \MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules\Required);
        $subject->setValidator('mimes', new \MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules\Mimes);
        $subject->setValidator('uploaded_file', new \MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules\UploadedFile);

        foreach ($inputs as $key => $input) {
            if ($input instanceof TemporaryUploadedFile) {
                $inputs[$key] = $this->convertToFilesArray($input);
            }
            if (is_array($input)) {
                foreach ($input as $k => $i) {
                    if ($i instanceof TemporaryUploadedFile) {
                        $inputs[$key][$k] = $this->convertToFilesArray($i);
                    }
                }
            }
        }

        return [$inputs, $rules, $messages];
    }

    private function convertToFilesArray(TemporaryUploadedFile $input): array
    {
        return [
            'name' => $input->getOriginalFileName(),
            'type' => $input->getMimeType(),
            'tmp_name' => $input->getTemporaryFileName(),
            'size' => $input->getFileSize(),
            'error' => 0
        ];
    }

    public function around__invoke(Validator $subject, callable $proceed, ...$args) {
        return $proceed(...$args);
    }
}
