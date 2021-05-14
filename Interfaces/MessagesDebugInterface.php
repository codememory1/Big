<?php

namespace Codememory\Components\Big\Interfaces;

/**
 * Interface MessagesDebugInterface
 * @package Codememory\Components\Big\Interfaces
 *
 * @author  Codememory
 */
interface MessagesDebugInterface
{

    public const NOT_CLOSED_OUTPUT = 'Unable to execute output due to syntax error. Not closed output ("%s")';
    public const VARIABLE_NOT_EXIST = 'The $%s variable is not passed or initialized to this template';
    public const ERROR_COMPILATION = 'A compilation error has occurred. Perhaps the specified value in the construction is incorrect or this construction not processed in engine template "big"';
    public const NOT_CLOSED_CONSTRUCTION = 'The %s construction is not closed. Close the construction with "%s"';

}