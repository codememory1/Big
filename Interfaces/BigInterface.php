<?php

namespace Codememory\Components\Big\Interfaces;

use Closure;
use Codememory\Components\Big\Engine;

/**
 * Interface BigInterface
 * @package Codememory\Components\Big\Interfaces
 *
 * @author  Codememory
 */
interface BigInterface
{

    /**
     * @param array $parameters
     *
     * @return BigInterface
     */
    public function setParameters(array $parameters): BigInterface;

    /**
     * @return Engine
     */
    public function getEngine(): Engine;

    /**
     * @return BigInterface
     */
    public function make(): BigInterface;

    /**
     * @return Closure
     */
    public function getCompiledTemplate(): Closure;


}