<?php

namespace Codememory\Components\Big\Traits;

use Codememory\Components\Big\Engine;
use Codememory\Components\Big\Interfaces\MessagesDebugInterface;

/**
 * Trait ConstructionsCompilerTrait
 * @package Codememory\Components\Big\Traits
 *
 * @author  Codememory
 */
trait ConstructionsCompilerTrait
{

    /**
     * @param string|null $oldLineText
     * @param string|null $lineText
     * @param array       $lexers
     * @param int         $line
     * @param callable    $additionalHandler
     */
    private function iterationLexersConstruction(?string $oldLineText, ?string &$lineText, array $lexers, int $line, callable $additionalHandler)
    {

        foreach ($lexers as $lexer) {
            $constructionName = $this->getConstructionName($lexer);
            $closedConstructionRegexp = '/^(?<constructionName>\w+)(?<constructionParams>.*)\]/';
            $debugMessage = sprintf(MessagesDebugInterface::NOT_CLOSED_CONSTRUCTION, $constructionName, Engine::VARIABLE_BLOCK_END);
            $lexerData = $this->mathClosedConstruction($closedConstructionRegexp, $lexer, $debugMessage, $line);

            call_user_func_array($additionalHandler, [&$lineText, $lexerData, $constructionName, $oldLineText]);
        }

    }

    /**
     * @param string $lexer
     *
     * @return ?string
     */
    private function getConstructionName(string $lexer): ?string
    {

        preg_match('/(?<name>\w+).*/', $lexer, $match);

        return $match['name'] ?? null;

    }

}