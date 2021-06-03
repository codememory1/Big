<?php

namespace Codememory\Components\Big\Compilers;

use Codememory\Components\Big\Debugger\DebugTypes;
use Codememory\Components\Big\Interfaces\BigConstructionInterface;
use Codememory\Components\Big\Interfaces\DebugMessagesInterface;

/**
 * Class ConditionalCompiler
 * @package Codememory\Components\Big\Constructions
 *
 * @author  Codememory
 */
class ConditionalCompiler extends BigCompilerAbstract implements BigConstructionInterface
{

    /**
     * @var array|string[]
     */
    private array $constructions = [
        'if', 'elseif', 'else', 'endIf'
    ];

    /**
     * @var string|null
     */
    private ?string $templateString = null;

    /**
     * @var int
     */
    private int $lineNumber = 1;

    /**
     * @inheritdoc
     */
    public function setTemplateString(?string $templateString): BigConstructionInterface
    {

        $this->templateString = $templateString;

        return $this;

    }

    /**
     * @inheritdoc
     */
    public function setLineNumber(int $lineNumber): BigConstructionInterface
    {

        $this->lineNumber = $lineNumber;

        return $this;

    }


    /**
     * @inheritDoc
     */
    public function getSuffixConstructionMethod(): string
    {

        return 'Construction';

    }

    /**
     * @inheritDoc
     */
    public function getConstructions(): array
    {

        return $this->constructions;

    }

    /**
     * @return string|null
     */
    public function ifConstruction(): ?string
    {

        return $this->generalConditionalConstruction('if', function (?string $constructionParameters, string $regexForReplaceConstruction) {
            if (empty($constructionParameters)) {
                $this->createDebug(DebugTypes::TRAGIC, $this->lineNumber, sprintf(DebugMessagesInterface::SHORTCUT_PARAMETERS, 'if', 1));
            }

            $this->replace($regexForReplaceConstruction, sprintf('<?php if(%s): ?>', $constructionParameters), $this->templateString);
        });

    }

    /**
     * @return string|null
     */
    public function elseifConstruction(): ?string
    {

        return $this->generalConditionalConstruction('elseIf', function (?string $constructionParameters, string $regexForReplaceConstruction) {
            if (empty($constructionParameters)) {
                $this->createDebug(DebugTypes::TRAGIC, $this->lineNumber, sprintf(DebugMessagesInterface::SHORTCUT_PARAMETERS, 'elseIf', 1));
            }

            $this->replace($regexForReplaceConstruction, sprintf('<?php elseIf(%s): ?>', $constructionParameters), $this->templateString);
        });

    }

    /**
     * @return string|null
     */
    public function elseConstruction(): ?string
    {

        return $this->generalConditionalConstruction('else', function (?string $constructionParameters, string $regexForReplaceConstruction) {
            if (!empty($constructionParameters)) {
                $this->createDebug(DebugTypes::TRAGIC, $this->lineNumber, sprintf(DebugMessagesInterface::SHORTCUT_PARAMETERS, 'else', 0));
            }

            $this->replace($regexForReplaceConstruction, '<?php else: ?>', $this->templateString);
        });

    }

    /**
     * @return string|null
     */
    public function endIfConstruction(): ?string
    {

        return $this->generalConditionalConstruction('endIf', function (?string $constructionParameters, string $regexForReplaceConstruction) {
            if (!empty($constructionParameters)) {
                $this->createDebug(DebugTypes::TRAGIC, $this->lineNumber, sprintf(DebugMessagesInterface::SHORTCUT_PARAMETERS, 'endIf', 0));
            }

            $this->replace($regexForReplaceConstruction, '<?php endif; ?>', $this->templateString);
        });

    }

    /**
     * @param string   $shortcut
     * @param callable $handler
     *
     * @return string|null
     */
    private function generalConditionalConstruction(string $shortcut, callable $handler): ?string
    {

        $this->shortcutBlock($this->templateString, $this->lineNumber, function (?string $constructionParameters,
                                                                                 string $regexForReplaceConstruction,
                                                                                 ?string $shortcutName,
                                                                                 ?string $oldTemplateString) use ($shortcut, $handler) {

            if ($shortcut === $shortcutName) {
                call_user_func_array($handler, [$constructionParameters, $regexForReplaceConstruction]);

                $this->compilationState($oldTemplateString, $this->templateString, $this->lineNumber);
            }
        });

        return $this->templateString;

    }

}