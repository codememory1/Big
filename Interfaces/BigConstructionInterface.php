<?php

namespace Codememory\Components\Big\Interfaces;

/**
 * Interface BigConstructionInterface
 * @package Codememory\Components\Big\Interfaces
 *
 * @author  Codememory
 */
interface BigConstructionInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Sets open template strings
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $templateString
     *
     * @return BigConstructionInterface
     */
    public function setTemplateString(?string $templateString): BigConstructionInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Sets the line number that is currently being compiled
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int $lineNumber
     *
     * @return BigConstructionInterface
     */
    public function setLineNumber(int $lineNumber): BigConstructionInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the suffix for construct method names
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function getSuffixConstructionMethod(): string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns an array of compiler constructs
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getConstructions(): array;

}