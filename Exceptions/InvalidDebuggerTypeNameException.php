<?php

namespace Codememory\Components\Big\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class InvalidDebuggerTypeNameException
 * @package Codememory\Components\Big\Exceptions
 *
 * @author  Codememory
 */
class InvalidDebuggerTypeNameException extends DebuggerException
{

    /**
     * InvalidDebuggerTypeNameException constructor.
     */
    #[Pure] public function __construct()
    {

        parent::__construct('The debugger type name must match the regular expression [A-Z_-]+');

    }

}