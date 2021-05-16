<?php

namespace Codememory\Components\Big;

use Codememory\Components\Big\Compilers\ConditionalCompiler;
use Codememory\Components\Big\Compilers\OutputBigCompiler;
use Codememory\Components\Big\Debugger\Debugger;
use Codememory\Components\Big\Interfaces\BigConstructionInterface;
use Codememory\Components\Big\Interfaces\DebuggerInterface;
use Codememory\FileSystem\Interfaces\FileInterface;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

/**
 * Class Engine
 * @package Codememory\Components\Big
 *
 * @author  Codememory
 */
class Engine
{

    public const OUTPUT_BLOCK_START = '[[';
    public const OUTPUT_BLOCK_END = ']]';
    public const SHORTCUT_BLOCK_START = '[@';
    public const SHORTCUT_BLOCK_END = ']';

    /**
     * @var Template
     */
    private Template $template;

    /**
     * @var FileInterface
     */
    private FileInterface $filesystem;

    /**
     * @var Utils
     */
    private Utils $utils;

    /**
     * @var DebuggerInterface|null
     */
    private ?DebuggerInterface $debugger = null;

    /**
     * @var array|string[]
     */
    private array $compilers = [
        OutputBigCompiler::class,
        ConditionalCompiler::class
    ];

    /**
     * @var string|null
     */
    private ?string $templateText = null;

    /**
     * Engine constructor.
     *
     * @param Template      $template
     * @param FileInterface $filesystem
     * @param Utils         $utils
     */
    public function __construct(Template $template, FileInterface $filesystem, Utils $utils)
    {

        $this->template = $template;
        $this->filesystem = $filesystem;
        $this->utils = $utils;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add your own compiler that implements the BigConstructionInterface interface
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $construction
     *
     * @return $this
     * @throws ReflectionException
     */
    public function addCompiler(string $construction): Engine
    {

        $reflection = new ReflectionClass($construction);
        $parent = $reflection->getParentClass();

        if (!$parent || $parent->getName() !== BigConstructionInterface::class) {
            throw new RuntimeException(sprintf(
                'The "big" template editor construction must implement the %s interface',
                BigConstructionInterface::class
            ));
        }

        $this->compilers[] = $reflection->getName();

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get open template debugger
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return DebuggerInterface
     */
    public function getDebugger(): DebuggerInterface
    {

        if (!$this->debugger instanceof DebuggerInterface) {
            $this->debugger = new Debugger(
                $this->template->getFullPath($this->template->getTemplateName()),
                $this->template,
                $this->filesystem,
                $this->utils
            );
        }

        return $this->debugger;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Iteration of lines of the template in which the iteration over
     * the constructions of the compiler occurs
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return $this
     * @throws Exceptions\TemplateNotExistException
     */
    public function iterationTemplateLines(): Engine
    {

        $arrayLineTemplate = $this->template->getArrayLineTemplate();

        foreach ($arrayLineTemplate as $line => &$value) {
            $this->iterationCompilers(function (BigConstructionInterface $compiler, string $constructionName) use (&$value, $line) {
                $compiler
                    ->setTemplateString($value)
                    ->setLineNumber($line);

                $callCompilerConstruction = call_user_func([$compiler, $constructionName]);
                $value = $callCompilerConstruction;
            });
        }

        $this->templateText = implode(PHP_EOL, $arrayLineTemplate);

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get the compiled text of a template
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    public function getTemplateText(): ?string
    {

        return $this->templateText;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Iterating added compilers and calling iteration of compiler constructs
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $handler
     *
     * @return void
     */
    private function iterationCompilers(callable $handler): void
    {

        foreach ($this->compilers as $compiler) {
            $compiler = new $compiler($this->getDebugger(), $this->template);

            $this->iterationCompilerConstructions($compiler, $handler);
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Iterate Constructs of a Specific Compiler and Call a Handler Function
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param BigConstructionInterface $compilerObject
     * @param callable                 $handler
     *
     * @return void
     */
    private function iterationCompilerConstructions(BigConstructionInterface $compilerObject, callable $handler): void
    {

        foreach ($compilerObject->getConstructions() as $construction) {
            $fullConstructionName = sprintf('%s%s', $construction, $compilerObject->getSuffixConstructionMethod());

            call_user_func($handler, $compilerObject, $fullConstructionName);
        }

    }

}