<?php

namespace Codememory\Components\Big\Debugger;

use ReflectionClass;

/**
 * Class DebugTypes
 * @package Codememory\Components\Big\Debugger
 *
 * @author  Codememory
 */
class DebugTypes
{

    public const ALL = 'E_BIG_ALL';
    public const WARNING = 'E_BIG_WARNING';
    public const TRAGIC = 'E_BIG_TRAGIC';
    public const SYNTAX = 'E_BIG_SYNTAX';

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of all existing reserved debug error types
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public static function getAllTypesErrors(): array
    {

        $reflection = new ReflectionClass(new self());

        return $reflection->getConstants();

    }

}