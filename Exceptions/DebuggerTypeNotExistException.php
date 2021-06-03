<?php

namespace Codememory\Components\Big\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class DebuggerTypeNotExistException
 * @package Codememory\Components\Big\Exceptions
 *
 * @author  Codememory
 */
class DebuggerTypeNotExistException extends DebuggerException
{

    /**
     * DebuggerTypeNotExistException constructor.
     *
     * @param string $type
     */
    #[Pure]
    public function __construct(string $type)
    {

        parent::__construct(sprintf(
            'Failed to create debug for the "big template engine" because the %s type does not exist',
            $type
        ));

    }

}