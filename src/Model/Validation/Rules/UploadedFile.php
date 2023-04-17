<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules;

class UploadedFile extends \Rakit\Validation\Rules\UploadedFile
{
    use \MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules\Traits\FileTrait;
}
