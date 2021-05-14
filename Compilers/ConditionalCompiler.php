<?php

namespace Codememory\Components\Big\Compilers;

use Codememory\Components\Big\Engine;

/**
 * Class ConditionalCompiler
 * @package Codememory\Components\Big\Constructions
 *
 * @author  Codememory
 */
class ConditionalCompiler extends BigCompilerAbstract
{

    /**
     * @var array|string[]
     */
    private array $constructions = [
        'if', 'elseif', 'else', 'endIf'
    ];

    public function ifConstruction(int $line, ?string $lineText)
    {

        $this->construction($line, $lineText, function (&$lineText, array $constructionData, string $constructionName, ?string $oldLineText) use ($line) {
            if($constructionName === 'if') {
                $constructionParams = trim($constructionData['constructionParams']);
                $regexpForReplace = sprintf('/%s%s/', preg_quote(Engine::VARIABLE_BLOCK_START), preg_quote($constructionData[0]));

                $this->replaceByRegex(
                    $regexpForReplace,
                    sprintf('<?php if(%s): ?>', $constructionParams),
                    $lineText
                );

                $this->compilationCheck($oldLineText, $lineText, $line);
            }
        });

        return $lineText;

    }

    public function elseifConstruction(int $line, ?string $lineText)
    {

        return $lineText;

    }

    public function elseConstruction(int $line, ?string $lineText)
    {

        return $lineText;

    }

    /**
     * @param int         $line
     * @param string|null $lineText
     *
     * @return string|null
     */
    public function endIfConstruction(int $line, ?string $lineText): ?string
    {

        $this->construction($line, $lineText, function (&$lineText, array $constructionData, string $constructionName, ?string $oldLineText) use ($line) {
            if($constructionName === 'endIf') {
                $regexpForReplace = sprintf('/%s%s/', preg_quote(Engine::VARIABLE_BLOCK_START), preg_quote($constructionData[0]));

                $this->replaceByRegex($regexpForReplace, '<?php endif; ?>', $lineText);

                $this->compilationCheck($oldLineText, $lineText, $line);
            }
        });

        return $lineText;

    }

    /**
     * @inheritDoc
     */
    public function getConstructions(): array
    {

        return $this->constructions;

    }
}