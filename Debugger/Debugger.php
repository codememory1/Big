<?php

namespace Codememory\Components\Big\Debugger;

use Closure;
use Codememory\Components\Big\Debugger\Utils as DebugUtils;
use Codememory\Components\Big\Exceptions\DebuggerTypeExistException;
use Codememory\Components\Big\Exceptions\DebuggerTypeNotExistException;
use Codememory\Components\Big\Exceptions\InvalidDebuggerTypeNameException;
use Codememory\Components\Big\Exceptions\TemplateNotExistException;
use Codememory\Components\Big\Interfaces\DebugDataInterface;
use Codememory\Components\Big\Interfaces\DebuggerInterface;
use Codememory\Components\Big\Interfaces\DebugTemplateInterface;
use Codememory\Components\Big\Template;
use Codememory\Components\Big\Utils;
use Codememory\FileSystem\Interfaces\FileInterface;
use JetBrains\PhpStorm\Pure;

/**
 * Class Debugger
 * @package Codememory\Components\Big\Debugger
 *
 * @author  Codememory
 */
class Debugger implements DebuggerInterface
{

    /**
     * @var string
     */
    private string $templatePath;

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
     * @var DebugUtils
     */
    private DebugUtils $debugUtils;

    /**
     * @var array
     */
    private array $errorTypes;

    /**
     * @var array
     */
    private array $debugData = [];

    /**
     * @var DebugTemplateInterface|null
     */
    private ?DebugTemplateInterface $debugTemplate = null;

    /**
     * Debugger constructor.
     *
     * @param string        $fullTemplatePath
     * @param Template      $template
     * @param FileInterface $filesystem
     * @param Utils         $utils
     */
    public function __construct(string $fullTemplatePath, Template $template, FileInterface $filesystem, Utils $utils)
    {

        $this->templatePath = $fullTemplatePath;
        $this->template = $template;
        $this->filesystem = $filesystem;
        $this->utils = $utils;
        $this->debugUtils = new DebugUtils($this->utils);
        $this->errorTypes = DebugTypes::getAllTypesErrors();

    }

    /**
     * @inheritDoc
     * @throws DebuggerTypeExistException
     * @throws InvalidDebuggerTypeNameException
     */
    public function addCustomType(string $typeUppercase): DebuggerInterface
    {

        if ($this->existType($typeUppercase)) {
            throw new DebuggerTypeExistException($typeUppercase);
        } else if (!preg_match('/^[A-Z_-]+$/', $typeUppercase)) {
            throw new InvalidDebuggerTypeNameException();
        }

        $this->errorTypes[] = $typeUppercase;

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function existType(string $type): bool
    {

        return in_array($type, $this->errorTypes);

    }

    /**
     * @inheritDoc
     * @throws DebuggerTypeNotExistException
     */
    public function createDebug(string $type, int $line, string $message): DebuggerInterface
    {

        if (!$this->existType($type)) {
            throw new DebuggerTypeNotExistException($type);
        }

        $this->debugData = [
            'type'    => $type,
            'line'    => $line,
            'message' => $message
        ];

        return $this;

    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getDebugData(): DebugDataInterface
    {

        return new DebugData($this->templatePath, $this->debugData);

    }

    /**
     * @inheritDoc
     */
    public function debugTemplate(): DebugTemplate
    {

        if (!$this->debugTemplate instanceof DebugTemplateInterface) {
            $this->debugTemplate = new DebugTemplate($this->filesystem);
        }

        return $this->debugTemplate;

    }

    /**
     * @inheritDoc
     * @throws TemplateNotExistException
     */
    public function execute(): void
    {

        $availableErrorTypes = $this->utils->getTypesError();

        if($this->utils->displayErrors()) {
            if (in_array($this->getDebugData()->getType(), $availableErrorTypes)
                || in_array(DebugTypes::ALL, $availableErrorTypes)) {
                $this->collectDebugTemplate()->__invoke();

                $this->interrupt();
            }
        }

    }

    /**
     * @return void
     */
    private function interrupt(): void
    {

        if ($this->utils->interruptFollowingCode()) {
            die;
        }

    }

    /**
     * @return Closure
     * @throws TemplateNotExistException
     */
    private function collectDebugTemplate(): Closure
    {

        $numberLineWithError = $this->getDebugData()->getLine();
        $arrayLinesTemplate = $this->template->getArrayLineTemplate();
        $startPositionFromCenter = $this->debugUtils->startPositionFromCenter($numberLineWithError);
        $endPositionFromCenter = $this->debugUtils->endPositionFromCenter($numberLineWithError, count($arrayLinesTemplate));

        return $this
            ->debugTemplate()
            ->addParameters([
                'linesTemplate' => $this->debugUtils->getLinesTemplate(
                    $startPositionFromCenter,
                    $endPositionFromCenter,
                    $arrayLinesTemplate
                ),
                'data'          => $this->getDebugData()
            ])
            ->getTemplate();

    }

}