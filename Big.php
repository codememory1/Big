<?php

namespace Codememory\Components\Big;

use Closure;
use Codememory\Components\Big\Cache as BigCache;
use Codememory\Components\Big\Exceptions\TemplateNotExistException;
use Codememory\Components\Big\Interfaces\BigInterface;
use Codememory\Components\Caching\Cache;
use Codememory\Components\Caching\Exceptions\ConfigPathNotExistException;
use Codememory\Components\Configuration\Exceptions\ConfigNotFoundException;
use Codememory\Components\Environment\Exceptions\EnvironmentVariableNotFoundException;
use Codememory\Components\Environment\Exceptions\IncorrectPathToEnviException;
use Codememory\Components\Environment\Exceptions\ParsingErrorException;
use Codememory\Components\Environment\Exceptions\VariableParsingErrorException;
use Codememory\Components\Markup\Types\YamlType;
use Codememory\FileSystem\Interfaces\FileInterface;

/**
 * Class Big
 * @package Codememory\Components\Big
 *
 * @author  Codememory
 */
class Big implements BigInterface
{

    /**
     * @var FileInterface
     */
    private FileInterface $filesystem;

    /**
     * @var Utils
     */
    private Utils $utils;

    /**
     * @var Template
     */
    private Template $template;

    /**
     * @var bool
     */
    private bool $useCache;

    /**
     * @var Engine|null
     */
    private ?Engine $engine = null;

    /**
     * @var mixed
     */
    private mixed $compiledTemplate = null;

    /**
     * Big constructor.
     *
     * @param string        $template
     * @param FileInterface $filesystem
     *
     * @throws ConfigNotFoundException
     * @throws EnvironmentVariableNotFoundException
     * @throws IncorrectPathToEnviException
     * @throws ParsingErrorException
     * @throws VariableParsingErrorException
     */
    public function __construct(string $template, FileInterface $filesystem)
    {

        $this->filesystem = $filesystem;
        $this->utils = new Utils($this->filesystem);
        $this->template = new Template($template, $this->filesystem, $this->utils);
        $this->useCache = $this->utils->useCache();

    }

    /**
     * @inheritDoc
     */
    public function useCache(bool $use): BigInterface
    {
        
        $this->useCache = $use;
        
        return $this;
        
    }
    
    /**
     * @inheritdoc
     */
    public function setParameters(array $parameters): BigInterface
    {

        $this->template->setParameters($parameters);

        return $this;

    }

    /**
     * @inheritdoc
     */
    public function getEngine(): Engine
    {

        if ($this->engine instanceof Engine) {
            return $this->engine;
        }

        return $this->engine = new Engine($this->template, $this->filesystem, $this->utils);

    }

    /**
     * @inheritdoc
     * @return BigInterface
     * @throws ConfigPathNotExistException
     * @throws TemplateNotExistException
     */
    public function make(): BigInterface
    {

        $templateText = $this->getEngine()->iterationTemplateLines()->getTemplateText();

        if ($this->useCache) {
            $bigCache = $this->saveToCache($templateText);

            $this->compiledTemplate = $this->assessment(
                $this->template->getParameters(),
                $bigCache->getTemplateFromCache($this->template->getTemplateName())
            );
        } else {
            $this->compiledTemplate = $this->assessment($this->template->getParameters(), $templateText);
        }

        return $this;

    }

    /**
     * @inheritdoc
     */
    public function getCompiledTemplate(): Closure
    {

        return $this->compiledTemplate;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the template passed through the eval function
     * which runs php strings
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array       $parameters
     * @param string|null $templateText
     *
     * @return Closure
     */
    private function assessment(array $parameters, ?string $templateText): Closure
    {

        return function () use ($parameters, $templateText) {
            extract($parameters);

            return eval(sprintf('?>%s', $templateText));
        };

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Creates or saves a template cache in the event that the
     * template cache does not match the open template
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $templateText
     *
     * @return BigCache
     * @throws ConfigPathNotExistException
     */
    private function saveToCache(?string $templateText): BigCache
    {

        $bigCache = new BigCache(new Cache(new YamlType(), $this->filesystem));

        if (serialize($bigCache->getTemplateFromCache($this->template->getTemplateName())) !== serialize($templateText)) {
            $bigCache
                ->setTemplateText($templateText)
                ->save($this->template->getTemplateName());
        }

        return $bigCache;

    }

}