<?php

namespace Codememory\Components\Big\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class TemplateDebuggerNotExistException
 * @package Codememory\Components\Big\Exceptions
 *
 * @author  Codememory
 */
class TemplateDebuggerNotExistException extends DebuggerException
{

    /**
     * TemplateDebuggerNotExistException constructor.
     *
     * @param string $templatePath
     */
    #[Pure]
    public function __construct(string $templatePath)
    {

        parent::__construct(sprintf(
            'Debug pattern on path %s does not exist',
            $templatePath
        ));

    }

}