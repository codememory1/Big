<?php

namespace Codememory\Components\Big\Debugger;

use Codememory\Components\Big\Interfaces\DebugDataInterface;
use Codememory\Support\Arr;
use JetBrains\PhpStorm\Pure;

/**
 * Class DebugData
 * @package Codememory\Components\Big\Debugger
 *
 * @author  Codememory
 */
class DebugData implements DebugDataInterface
{

    /**
     * @var string
     */
    private string $templatePath;

    /**
     * @var array
     */
    private array $data;

    /**
     * DebugData constructor.
     *
     * @param string $fullTemplatePath
     * @param array  $data
     */
    public function __construct(string $fullTemplatePath, array $data)
    {

        $this->templatePath = $fullTemplatePath;
        $this->data = $data;

    }

    /**
     * @inheritDoc
     */
    public function getType(): ?string
    {

        return Arr::set($this->data)::get('type');

    }

    /**
     * @inheritDoc
     */
    public function getLine(): ?int
    {

        return Arr::set($this->data)::get('line');

    }

    /**
     * @inheritDoc
     */
    public function getFile(): string
    {

        return $this->templatePath;

    }

    /**
     * @inheritDoc
     */
    public function getMessage(): ?string
    {

        return Arr::set($this->data)::get('message');

    }
}