<?php

namespace Codememory\Components\Big\Traits;

use Codememory\Components\Big\Engine;
use Codememory\Components\Big\Interfaces\MessagesDebugInterface;

/**
 * Trait OutputCompilerTrait
 * @package Codememory\Components\Big\Traits
 *
 * @author  Codememory
 */
trait OutputCompilerTrait
{


    /**
     * @param int    $line
     * @param string $lexer
     * @param mixed  $value
     *
     * @return bool
     */
    private function outputCheckClosed(int $line, string $lexer, mixed &$value): bool
    {

        $regexp = sprintf('/^(?<value>.+)\s*%1$s/', Engine::TAG_BLOCK_END);
        $debugMessage = sprintf(MessagesDebugInterface::NOT_CLOSED_OUTPUT, Engine::TAG_BLOCK_START);
        $match = $this->mathClosedConstruction($regexp, $lexer, $debugMessage, $line);

        $value = trim($match['value']);

        return true;

    }

    /**
     * @param array       $lexers
     * @param string|null $oldLineText
     * @param string|null $lineText
     * @param int         $line
     * @param callable    $additionalHandler
     */
    private function outputIterationLexers(array $lexers, ?string $oldLineText, ?string &$lineText, int $line, callable $additionalHandler): void
    {

        foreach ($lexers as $lexer) {
            $valueOutput = null;

            if ($this->outputCheckClosed($line, $lexer, $valueOutput)) {
                call_user_func_array($additionalHandler, [&$lineText, $valueOutput]);

                $this->compilationCheck($oldLineText, $lineText, $line);
            }
        }

    }

}