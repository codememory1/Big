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
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Override useCache parameter from configuration
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param bool $use
     *
     * @return BigInterface
     */
    public function useCache(bool $use): BigInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Pass parameters to open template
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $parameters
     *
     * @return BigInterface
     */
    public function setParameters(array $parameters): BigInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get the object of the main engine of the template engine, with
     * which you can add your own additional compiler
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Engine
     */
    public function getEngine(): Engine;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method starts compilation of the open template
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return BigInterface
     */
    public function make(): BigInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the compiled template text
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Closure
     */
    public function getCompiledTemplate(): Closure;


}