<?php

namespace Codememory\Components\Big\Compilers;

use Codememory\Components\Big\Debugger\DebugTypes;
use Codememory\Components\Big\Interfaces\BigConstructionInterface;
use Codememory\Components\Big\Interfaces\DebugMessagesInterface;
use Codememory\Support\Arr;

/**
 * Class OutputBigCompiler
 * @package Codememory\Components\Big\Constructions
 *
 * @author  Codememory
 */
final class OutputBigCompiler extends BigCompilerAbstract implements BigConstructionInterface
{

    /**
     * @var array|string[]
     */
    private array $constructions = [
        'output'
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
    public function outputConstruction(): ?string
    {

        $this->outputBlock($this->templateString, $this->lineNumber, function (?string $parametersConstruction, string $regexForReplaceConstruction, string $oldTemplateString) {
            if (empty($parametersConstruction)) {
                $this->createDebug(DebugTypes::TRAGIC, $this->lineNumber, DebugMessagesInterface::OUTPUT_NOT_PARAMETERS);
            }

            $this->processingOutputIfParameterVariable($parametersConstruction);

            $this->replace($regexForReplaceConstruction, sprintf('<?php echo %s; ?>', $parametersConstruction), $this->templateString);

            $this->compilationState($oldTemplateString, $this->templateString, $this->lineNumber);
        });

        return $this->templateString;

    }

    /**
     * @param string $variableName
     *
     * @return bool
     */
    private function existVariable(string $variableName): bool
    {

        return Arr::exists($this->template->getParameters(), $variableName);

    }

    /**
     * @param string|null $parametersConstruction
     */
    private function processingOutputIfParameterVariable(?string $parametersConstruction): void
    {

        if (preg_match('/^\$(?<variableName>\w+)/', $parametersConstruction, $match)) {
            $variableName = $match['variableName'];

            if (!$this->existVariable($variableName)) {
                $this->createDebug(DebugTypes::WARNING, $this->lineNumber, sprintf(DebugMessagesInterface::VARIABLE_NOT_EXIST, $variableName));
            }

            if (isDev()) {
                $this->addComment()->html(sprintf('The output of the %s variable', $variableName), $this->templateString);
            }
        }

    }

}