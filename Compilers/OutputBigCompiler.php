<?php

namespace Codememory\Components\Big\Compilers;

use Codememory\Components\Big\Debugger\DebugTypes;
use Codememory\Components\Big\Interfaces\MessagesDebugInterface;
use Codememory\Support\Arr;

/**
 * Class OutputBigCompiler
 * @package Codememory\Components\Big\Constructions
 *
 * @author  Codememory
 */
final class OutputBigCompiler extends BigCompilerAbstract
{

    /**
     * @var array|string[]
     */
    private array $constructions = [
        'output'
    ];

    /**
     * @param int         $line
     * @param string|null $lineText
     *
     * @return string|null
     */
    public function outputConstruction(int $line, ?string $lineText): ?string
    {

        $this->output($line, $lineText, function (string &$lineText, string $value) use ($line) {
            $regexReplace = '\[\[\s*%s\s*]]';

            if (preg_match('/^\$(?<var>\w+)/', $value, $match)) {
                $variable = sprintf('$%s', $match['var']);

                $this
                    ->outputVariable(
                        $this->regexp($regexReplace, $variable),
                        $value,
                        $lineText,
                        $variable
                    )
                    ->outputExistVariable($line, $match);
            } else {
                $this->replaceByRegex(
                    $this->regexp($regexReplace, $value),
                    sprintf('<?php echo %s; ?>', $value),
                    $lineText
                );
            }
        });

        return $lineText;

    }

    /**
     * @param string $regexReplace
     * @param string $value
     * @param string $lineText
     * @param string $variable
     *
     * @return OutputBigCompiler
     */
    private function outputVariable(string $regexReplace, string $value, string &$lineText, string $variable): OutputBigCompiler
    {

        $readyString = sprintf("<?php echo %s; ?>", $variable);

        if (isDev()) {
            $this
                ->addComment()
                ->html(sprintf('The output of the %s variable', $variable), $readyString);
        }

        $this->replaceByRegex($regexReplace, $readyString, $lineText);

        return $this;

    }

    /**
     * @param int   $line
     * @param array $match
     *
     * @return OutputBigCompiler
     */
    private function outputExistVariable(int $line, array $match): OutputBigCompiler
    {

        $variableName = $match['var'];

        if (!Arr::exists($this->template->getParameters(), $variableName)) {
            $this->createDebug(
                DebugTypes::WARNING,
                $line,
                sprintf(MessagesDebugInterface::VARIABLE_NOT_EXIST, $variableName)
            );
        }

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function getConstructions(): array
    {

        return $this->constructions;

    }

}