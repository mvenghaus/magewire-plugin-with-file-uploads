<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules;

class Required extends \Rakit\Validation\Rules\Required
{
    use \MVenghaus\MagewirePluginWithFileUploads\Model\Validation\Rules\Traits\FileTrait;
}