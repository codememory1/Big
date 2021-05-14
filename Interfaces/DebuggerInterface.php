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
     * @param string $typeUppercase
     *
     * @return DebuggerInterface
     */
    public function addCustomType(string $typeUppercase): DebuggerInterface;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function existType(string $type): bool;

    /**
     * @param string $type
     * @param int    $line
     * @param string $message
     *
     * @return DebuggerInterface
     */
    public function createDebug(string $type, int $line, string $message): DebuggerInterface;

    /**
     * @return DebugDataInterface
     */
    public function getDebugData(): DebugDataInterface;

    /**
     * @return DebugTemplate
     */
    public function debugTemplate(): DebugTemplate;

    /**
     * @return void
     */
    public function execute(): void;

}