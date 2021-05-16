<?php

namespace Codememory\Components\Big\Compilers;

use Codememory\Components\Big\Comments;
use Codememory\Components\Big\Debugger\DebugTypes;
use Codememory\Components\Big\Engine;
use Codememory\Components\Big\Interfaces\DebuggerInterface;
use Codememory\Components\Big\Interfaces\DebugMessagesInterface;
use Codememory\Components\Big\Template;
use Codememory\Components\Big\Traits\ConstructionAssistantTrait;
use Codememory\Support\Arr;

/**
 * Class BigCompilerAbstract
 * @package Codememory\Components\Big\Constructions
 *
 * @author  Codememory
 */
abstract class BigCompilerAbstract
{

    use ConstructionAssistantTrait;

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
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Simplified method of creating and executing debugs
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
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
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns a Comments class object for adding comments to a line
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
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
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Simplified method of writing regex using sprintf
     * and parameter escaping
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $regex
     * @param array  $parameters
     * @param bool   $escapingParameters
     *
     * @return string
     */
    protected function createRegex(string $regex, array $parameters = [], bool $escapingParameters = true): string
    {

        if ($escapingParameters) {
            Arr::map($parameters, function (mixed $key, mixed $value) {
                return [preg_quote($value)];
            });
        }

        return sprintf($regex, ...$parameters);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A simplified output compiler block that performs basic
     * checks and invokes an additional processing callback
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $templateString
     * @param int         $lineNumber
     * @param callable    $handler
     *
     * @return string|null
     */
    protected function outputBlock(?string $templateString, int $lineNumber, callable $handler): ?string
    {

        $complementary = function (string $constructionWithoutOpened,
                                   string $constructionParameters,
                                   string $regexForReplaceConstruction,
                                   string $oldTemplateString
        ) use ($handler) {
            call_user_func_array($handler, [$constructionParameters, $regexForReplaceConstruction, $oldTemplateString]);
        };

        return $this->commonHandlerBlockConstruction(
            Engine::OUTPUT_BLOCK_START,
            Engine::OUTPUT_BLOCK_END,
            $templateString,
            $lineNumber,
            $complementary
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A simplified shortcut compiler block that performs basic actions
     * and calls an additional callback by passing parameters
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $templateString
     * @param int         $lineNumber
     * @param callable    $handler
     *
     * @return string|null
     */
    protected function shortcutBlock(?string $templateString, int $lineNumber, callable $handler): ?string
    {

        $complementary = function (string $constructionWithoutOpened,
                                   string $constructionParameters,
                                   string $regexForReplaceConstruction,
                                   string $oldTemplateString
        ) use ($handler) {
            preg_match('/(?<shortcutName>\w+)((\s+)(?<parameters>.*))?/', $constructionParameters, $match);
            $parametersAndShortcutName = $match['shortcutName'] ?? null;

            call_user_func_array($handler, [$match['parameters'] ?? null, $regexForReplaceConstruction, $parametersAndShortcutName, $oldTemplateString]);
        };

        return $this->commonHandlerBlockConstruction(
            Engine::SHORTCUT_BLOCK_START,
            Engine::SHORTCUT_BLOCK_END,
            $templateString,
            $lineNumber,
            $complementary
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The main compiler processing unit, which parses the template string lexers.
     * It is checked for a closed structure; if it is not a debug is created
     * and an additional construct handler is also called
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string      $startBlock
     * @param string      $endBlock
     * @param string|null $templateString
     * @param int         $lineNumber
     * @param callable    $complementary
     *
     * @return string|null
     */
    private function commonHandlerBlockConstruction(string $startBlock, string $endBlock, ?string &$templateString, int $lineNumber, callable $complementary): ?string
    {

        $oldTemplateString = $templateString;
        $constructionsShortcutBlock = $this->getConstructionWithoutOpened($templateString, $startBlock);

        $iterationConstructionHandler = function (string $constructionWithoutOpened) use (&$templateString, $lineNumber, $oldTemplateString, $complementary, $startBlock, $endBlock) {
            if (!$this->checkConstructionClosed($constructionWithoutOpened, $endBlock)) {
                $debugMessageNotClosedConstruction = sprintf(DebugMessagesInterface::NOT_CLOSED_OUTPUT, $endBlock);

                $this->makeDebugNonClosedConstruction($lineNumber, $debugMessageNotClosedConstruction);
            }

            $constructionParameters = $this->getConstructionParameters($constructionWithoutOpened, $endBlock);
            $regexForReplaceConstruction = $this->createRegexForReplaceConstruction($startBlock, $endBlock, $constructionParameters);

            $templateString = call_user_func_array($complementary, [$constructionWithoutOpened, $constructionParameters, $regexForReplaceConstruction, $oldTemplateString]);

        };

        $this->iterationConstructions($constructionsShortcutBlock, $iterationConstructionHandler);

        return $templateString;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create and debug if the construct is not closed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int    $lineNumber
     * @param string $debugMessage
     */
    private function makeDebugNonClosedConstruction(int $lineNumber, string $debugMessage): void
    {

        $this->createDebug(DebugTypes::SYNTAX, $lineNumber, $debugMessage);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Iterating over constructs from a string and calling a handler
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array    $constructions
     * @param callable $handler
     */
    private function iterationConstructions(array $constructions, callable $handler): void
    {

        if ([] !== $constructions) {
            foreach ($constructions as $construction) {
                call_user_func($handler, $construction);
            }
        }

    }

}