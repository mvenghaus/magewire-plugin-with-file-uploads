<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules;

class Mimes extends \Rakit\Validation\Rules\Mimes
{
    use \MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules\Traits\FileTrait;
}