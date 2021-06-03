<?php

namespace Codememory\Components\Big\Interfaces;

use Codememory\Components\Big\Debugger\DebugTemplate;

/**
 * Interface DebuggerInterface
 * @package Codememory\Components\Big\Interfaces
 *
 * @author  Codememory
 */
interface DebuggerInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add your own error type for debugger
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $typeUppercase
     *
     * @return DebuggerInterface
     */
    public function addCustomType(string $typeUppercase): DebuggerInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check for the existence of a debugger error type
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     *
     * @return bool
     */
    public function existType(string $type): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create debug as the first argument, the type of error is passed, the second line
     * number from the open template, and the third argument the text of the error
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param int    $line
     * @param string $message
     *
     * @return DebuggerInterface
     */
    public function createDebug(string $type, int $line, string $message): DebuggerInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the data object of the generated debug
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return DebugDataInterface
     */
    public function getDebugData(): DebugDataInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the debug template object that is displayed when the debug occurs
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return DebugTemplate
     */
    public function debugTemplate(): DebugTemplate;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * If the debug display is enabled and the error type of the generated
     * debug is activated, the debug will be executed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    public function execute(): void;

}