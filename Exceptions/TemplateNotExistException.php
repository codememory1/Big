<?php

namespace Codememory\Components\Big\Exceptions;

use Codememory\Support\Str;
use JetBrains\PhpStorm\Pure;

/**
 * Class TemplateNotExistException
 * @package Codememory\Components\Big\Exceptions
 *
 * @author  Codememory
 */
class TemplateNotExistException extends BigExceptions
{

    /**
     * TemplateNotExistException constructor.
     *
     * @param string $path
     */
    #[Pure]
    public function __construct(string $path)
    {

        parent::__construct(sprintf(
            'Template %s on path %s does not exist',
            Str::trimAfterSymbol(basename($path), '.'),
            dirname($path)
        ));

    }

}