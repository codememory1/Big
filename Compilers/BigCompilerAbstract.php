<?php

namespace Codememory\Components\Big\Compilers;

use Codememory\Components\Big\Comments;
use Codememory\Components\Big\Debugger\DebugTypes;
use Codememory\Components\Big\Engine;
use Codememory\Components\Big\Interfaces\BigConstructionInterface;
use Codememory\Components\Big\Interfaces\DebuggerInterface;
use Codememory\Components\Big\Interfaces\MessagesDebugInterface;
use Codememory\Components\Big\Template;
use Codememory\Components\Big\Traits\ConstructionsCompilerTrait;
use Codememory\Components\Big\Traits\OutputCompilerTrait;

/**
 * Class BigCompilerAbstract
 * @package Codememory\Components\Big\Constructions
 *
 * @author  Codememory
 */
abstract class BigCompilerAbstract implements BigConstructionInterface
{

    use OutputCompilerTrait;
    use ConstructionsCompilerTrait;

    /**
     * @var DebuggerInterface
     */
    protected DebuggerInterface $debugger;

    /**
     * @var Template
     */
    protected Template $template;

    /**
     * @var null|Comments
     */
    private ?Comments $comments = null;

    /**
     * BigCompilerAbstract constructor.
     *
     * @param DebuggerInterface $debugger
     * @param Template          $template
     */
    public function __construct(DebuggerInterface $debugger, Template $template)
    {

        $this->debugger = $debugger;
        $this->template = $template;

    }

    /**
     * @param string $type
     * @param int    $line
     * @param string $message
     *
     * @return $this
     */
    protected function createDebug(string $type, int $line, string $message): BigCompilerAbstract
    {

        $this->debugger->createDebug($type, $line, $message)->execute();

        return $this;

    }

    /**
     * @param string          $regex
     * @param string|int|null $replacedBy
     * @param string          $where
     *
     * @return $this
     */
    protected function replaceByRegex(string $regex, null|string|int $replacedBy, string &$where): BigCompilerAbstract
    {

        $where = preg_replace_callback($regex, function () use ($replacedBy) {
            return $replacedBy;
        }, $where);

        return $this;

    }

    /**
     * @param string $regex
     * @param string $dummy
     * @param bool   $escapeDummy
     *
     * @return string
     */
    protected function regexp(string $regex, string $dummy, bool $escapeDummy = true): string
    {

        $dummy = $escapeDummy ? preg_quote($dummy) : $dummy;

        return sprintf('/%s/', sprintf($regex, $dummy));

    }

    /**
     * @return Comments
     */
    protected function addComment(): Comments
    {

        if (!$this->comments instanceof Comments) {
            $this->comments = new Comments();
        }

        return $this->comments;

    }

    /**
     * @param int         $line
     * @param string|null $lineText
     * @param callable    $additionalHandler
     *
     * @return string|null
     */
    protected function output(int $line, ?string &$lineText, callable $additionalHandler): ?string
    {

        $oldLineText = $lineText;

        $this->defaultIterationLexers(
            Engine::TAG_BLOCK_START,
            $lineText,
            function (array $lexers) use ($oldLineText, $line, $additionalHandler, &$lineText) {
                $this->outputIterationLexers($lexers, $oldLineText, $lineText, $line, $additionalHandler);
            }
        );

        return $lineText;

    }

    protected function construction(int $line, ?string &$lineText, callable $additionalHandler)
    {

        $oldLineText = $lineText;

        $this->defaultIterationLexers(
            Engine::VARIABLE_BLOCK_START,
            $lineText,
            function (array $lexers) use ($oldLineText, $line, $additionalHandler, &$lineText) {
                $this->iterationLexersConstruction($oldLineText, $lineText, $lexers, $line, $additionalHandler);
            }
        );



    }

    /**
     * @param string $closedRegexp
     * @param string $lexer
     * @param        $debugMessage
     * @param int    $line
     *
     * @return bool|array
     */
    private function mathClosedConstruction(string $closedRegexp, string $lexer, $debugMessage, int $line): bool|array
    {

        $pregMatch = preg_match($closedRegexp, $lexer, $match);

        if (!$pregMatch) {
            $this->createDebug(DebugTypes::SYNTAX, $line, $debugMessage);

            return false;
        }

        return $match;

    }

    /**
     * @param string      $startTag
     * @param string|null $lineText
     * @param callable    $handler
     */
    private function defaultIterationLexers(string $startTag, ?string $lineText, callable $handler): void
    {

        $lexers = explode($startTag, $lineText);

        if(count($lexers) > 1) {
            unset($lexers[0]);

            foreach ($lexers as $lexer) {
                call_user_func_array($handler, [$lexers, $lexer]);
            }
        }

    }

    /**
     * @param string|null $oldLineText
     * @param string|null $lineText
     * @param int         $line
     */
    protected function compilationCheck(?string $oldLineText, ?string $lineText, int $line): void
    {

        if ($oldLineText === $lineText) {
            $this->createDebug(DebugTypes::SYNTAX, $line, MessagesDebugInterface::ERROR_COMPILATION);
        }

    }

}