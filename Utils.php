<?php

namespace Codememory\Components\Big;

use Codememory\Components\Big\Debugger\DebugTypes;
use Codememory\Components\Configuration\Config;
use Codememory\Components\Configuration\Exceptions\ConfigNotFoundException;
use Codememory\Components\Configuration\Interfaces\ConfigInterface;
use Codememory\Components\Environment\Exceptions\EnvironmentVariableNotFoundException;
use Codememory\Components\Environment\Exceptions\IncorrectPathToEnviException;
use Codememory\Components\Environment\Exceptions\ParsingErrorException;
use Codememory\Components\Environment\Exceptions\VariableParsingErrorException;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\FileSystem\Interfaces\FileInterface;

/**
 * Class Utils
 * @package Codememory\Components\Big
 *
 * @author  Codememory
 */
class Utils
{

    private const INTERRUPT_FOLLOWING_CODE = true;
    private const DEFAULT_TYPE_ERROR = 'E_BIG_ALL';
    public const EXTENSION = '.big';

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Utils constructor.
     *
     * @param FileInterface $filesystem
     *
     * @throws ConfigNotFoundException
     * @throws EnvironmentVariableNotFoundException
     * @throws IncorrectPathToEnviException
     * @throws ParsingErrorException
     * @throws VariableParsingErrorException
     */
    public function __construct(FileInterface $filesystem)
    {

        $config = new Config($filesystem);

        $this->config = $config->open(GlobalConfig::get('big.configName'));

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get the status of the debug
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function displayErrors(): bool
    {

        return (bool) $this->getCertainValue(
            $this->config->get('displayErrors'),
            false
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get the path where all templates are stored
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function getPathWithTemplates(): string
    {

        return (string) $this->getCertainValue(
            $this->config->get('pathToTemplates'),
            GlobalConfig::get('big.defaultPathWithTemplates')
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get Cache Usage Status
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function useCache(): bool
    {

        return (string) $this->getCertainValue(
            $this->config->get('cache'),
            GlobalConfig::get('big.useCache')
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get the number of lines to be displayed in the debug
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return int
     */
    public function getNumberLinesCode(): int
    {

        return (int) $this->getCertainValue(
            $this->config->get('debugger.maxLines'),
            GlobalConfig::get('big.debugger.lines')
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get the interrupt status of the following code if an error
     * occurs from the template engine itself
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function interruptFollowingCode(): bool
    {

        return (bool) $this->getCertainValue(
            $this->config->get('debugger.interruptFollowingCode'),
            self::INTERRUPT_FOLLOWING_CODE
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get an array of all types of errors that can be displayed
     * in debug at compile time
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getTypesError(): array
    {

        $errorTypes = $this->config->get('debugger.errorTypes');
        $types = [];

        if (empty($errorTypes) || !is_array($errorTypes)) {
            $types = [self::DEFAULT_TYPE_ERROR];
        } else {
            foreach ($errorTypes as $errorType) {
                if (in_array($errorType, DebugTypes::getAllTypesErrors())) {
                    $types[] = $errorType;
                }
            }
        }

        return [] === $types ? [self::DEFAULT_TYPE_ERROR] : $types;

    }

    /**
     * @param mixed $value
     * @param mixed $default
     *
     * @return mixed
     */
    private function getCertainValue(mixed $value, mixed $default): mixed
    {

        return $value ?? $default;

    }

}