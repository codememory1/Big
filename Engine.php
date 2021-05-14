<?php

namespace Codememory\Components\Big;

use Codememory\Components\Big\Compilers\BigCompilerAbstract;
use Codememory\Components\Big\Compilers\ConditionalCompiler;
use Codememory\Components\Big\Compilers\OutputBigCompiler;
use Codememory\Components\Big\Debugger\Debugger;
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

    public const TAG_BLOCK_START = '[[';
    public const TAG_BLOCK_END = ']]';
    public const COMMENT_TAG_START = '[[#';
    public const COMMENT_TAG_END = '#]]';
    public const VARIABLE_BLOCK_START = '[@';
    public const VARIABLE_BLOCK_END = ']';

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
     * @param string $construction
     *
     * @return $this
     * @throws ReflectionException
     */
    public function addCompiler(string $construction): Engine
    {

        $reflection = new ReflectionClass($construction);
        $parent = $reflection->getParentClass();

        if (!$parent || $parent->getName() !== BigCompilerAbstract::class) {
            throw new RuntimeException(sprintf(
                'Big construct must inherit from parent class %s',
                BigCompilerAbstract::class
            ));
        }

        $this->compilers[] = $reflection->getName();

        return $this;

    }

    /**
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
     * @return $this
     * @throws Exceptions\TemplateNotExistException
     */
    public function iterationTemplateLines(): Engine
    {


        $arrayLineTemplate = $this->template->getArrayLineTemplate();

        foreach ($arrayLineTemplate as $line => &$value) {
           $this->iterationCompilers(function (BigCompilerAbstract $compiler, string $constructionName) use (&$value, $line) {
                $callCompilerConstruction = call_user_func_array([$compiler, $constructionName], [$line, $value]);
                $value = $callCompilerConstruction;
            });
        }

        $this->templateText = implode(PHP_EOL, $arrayLineTemplate);

        return $this;

    }

    /**
     * @return string|null
     */
    public function getTemplateText(): ?string
    {

        return $this->templateText;

    }

    /**
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
     * @param BigCompilerAbstract $compilerObject
     * @param callable            $handler
     *
     * @return void
     */
    private function iterationCompilerConstructions(BigCompilerAbstract $compilerObject, callable $handler): void
    {

        foreach ($compilerObject->getConstructions() as $construction) {
            $fullConstructionName = sprintf('%sConstruction', $construction);

            call_user_func($handler, $compilerObject, $fullConstructionName);
        }

    }

}