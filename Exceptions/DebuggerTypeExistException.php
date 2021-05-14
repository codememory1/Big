<?php

namespace Codememory\Components\Big\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class DebuggerTypeExistException
 * @package Codememory\Components\Big\Exceptions
 *
 * @author  Codememory
 */
class DebuggerTypeExistException extends DebuggerException
{

    /**
     * DebuggerTypeExistException constructor.
     *
     * @param string $type
     */
    #[Pure]
    public function __construct(string $type)
    {

        parent::__construct(sprintf(
            'It is not possible to create two of the same type. The %s type already exists in big debugger',
            $type
        ));

    }

}