<?php

namespace Codememory\Components\Big\Interfaces;

use Closure;

/**
 * Interface DebugTemplateInterface
 * @package Codememory\Components\Big\Interfaces
 *
 * @author  Codememory
 */
interface DebugTemplateInterface
{

    /**
     * @param array $parameters
     *
     * @return DebugTemplateInterface
     */
    public function addParameters(array $parameters): DebugTemplateInterface;

    /**
     * @return string
     */
    public function getTemplatePath(): string;

    /**
     * @return bool
     */
    public function existTemplate(): bool;

    /**
     * @return Closure
     */
    public function getTemplate(): Closure;

}