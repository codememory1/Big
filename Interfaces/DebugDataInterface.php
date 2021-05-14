<?php

namespace Codememory\Components\Big\Interfaces;

/**
 * Interface DebugDataInterface
 * @package Codememory\Components\Big\Interfaces
 *
 * @author  Codememory
 */
interface DebugDataInterface
{

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @return int|null
     */
    public function getLine(): ?int;

    /**
     * @return string
     */
    public function getFile(): string;

    /**
     * @return string|null
     */
    public function getMessage(): ?string;

}