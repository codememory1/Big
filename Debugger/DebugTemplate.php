<?php

namespace Codememory\Components\Big\Debugger;

use Closure;
use Codememory\Components\Big\Exceptions\TemplateDebuggerNotExistException;
use Codememory\Components\Big\Interfaces\DebugTemplateInterface;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Support\Arr;

/**
 * Class DebugTemplate
 * @package Codememory\Components\Big\Debugger
 *
 * @author  Codememory
 */
class DebugTemplate implements DebugTemplateInterface
{

    /**
     * @var FileInterface
     */
    private FileInterface $filesystem;

    /**
     * @var array
     */
    private array $parameters = [];

    /**
     * DebugTemplate constructor.
     *
     * @param FileInterface $filesystem
     */
    public function __construct(FileInterface $filesystem)
    {

        $this->filesystem = $filesystem;

    }

    /**
     * @inheritdoc
     */
    public function addParameters(array $parameters): DebugTemplateInterface
    {

        Arr::merge($this->parameters, $parameters);

        return $this;

    }

    /**
     * @inheritdoc
     */
    public function getTemplatePath(): string
    {

        return sprintf('%s.php', trim(GlobalConfig::get('big.debugger.template'), '/'));

    }

    /**
     * @inheritdoc
     */
    public function existTemplate(): bool
    {

        return $this->filesystem->exist($this->getTemplatePath());

    }

    /**
     * @inheritdoc
     * @throws TemplateDebuggerNotExistException
     */
    public function getTemplate(): Closure
    {

        if (!$this->existTemplate()) {
            throw new TemplateDebuggerNotExistException($this->getTemplatePath());
        }

        return function () {
            return $this->filesystem->singleImport($this->getTemplatePath(), $this->parameters);
        };

    }

}