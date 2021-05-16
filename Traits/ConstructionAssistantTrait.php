<?php

namespace Codememory\Components\Big\Traits;

use Codememory\Components\Big\Debugger\DebugTypes;
use Codememory\Components\Big\Interfaces\DebugMessagesInterface;

/**
 * Trait ConstructionAssistantTrait
 * @package Codememory\Components\Big\Traits
 *
 * @author  Codememory
 */
trait ConstructionAssistantTrait
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a regular expression to test the closed of a construct
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $closingSymbol
     *
     * @return string
     */
    protected function closedRegex(string $closingSymbol): string
    {

        return sprintf('/^(?<parameters>.*)%s/', $closingSymbol);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a boolean value by checking the closed of the string
     * construct, which is separated by the open character of the construct
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $constructionWithoutOpened
     * @param string $closingSymbol
     *
     * @return bool
     */
    protected function checkConstructionClosed(string $constructionWithoutOpened, string $closingSymbol): bool
    {

        if (preg_match($this->closedRegex($closingSymbol), $constructionWithoutOpened)) {
            return true;
        }

        return false;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a construct string without a construct open character
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $templateString
     * @param string $symbolOpenness
     *
     * @return array
     */
    protected function getConstructionWithoutOpened(string $templateString, string $symbolOpenness): array
    {

        $constructions = explode($symbolOpenness, $templateString);

        if (count($constructions) > 1) {
            unset($constructions[0]);

            return $constructions;
        }

        return [];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a string of design parameters that are between the
     * open and closed character of the design
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $constructionWithoutOpened
     * @param string $closingSymbol
     *
     * @return ?string
     */
    protected function getConstructionParameters(string $constructionWithoutOpened, string $closingSymbol): ?string
    {

        preg_match($this->closedRegex($closingSymbol), $constructionWithoutOpened, $match);

        return trim($match['parameters']) ?? null;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A debug is created if the compilation was not successful,
     * that is, the old template string is compared with the new
     * one to which the old string should be compiled, if they
     * are equal, the compilation failed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $oldTemplateString
     * @param string|null $newTemplateString
     * @param int         $lineNumber
     */
    protected function compilationState(?string $oldTemplateString, ?string $newTemplateString, int $lineNumber): void
    {

        if ($oldTemplateString === $newTemplateString) {
            $this->createDebug(DebugTypes::TRAGIC, $lineNumber, DebugMessagesInterface::ERROR_COMPILATION);
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a regular expression that is needed to find
     * and replace a construct
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $startBlock
     * @param string $endBlock
     * @param string $constructionParameters
     *
     * @return string
     */
    protected function createRegexForReplaceConstruction(string $startBlock, string $endBlock, string $constructionParameters): string
    {

        return $this->createRegex('/%s\s*%s\s*%s/', [$startBlock, $constructionParameters, $endBlock]);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Simplified preg_replace method
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string      $regex
     * @param string|null $replacement
     * @param string      $whereToChange
     *
     * @return bool
     */
    protected function replace(string $regex, ?string $replacement, string &$whereToChange): bool
    {

        $whereToChange = preg_replace($regex, $replacement, $whereToChange);

        return true;

    }

}