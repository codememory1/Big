<?php

namespace Codememory\Components\Big;

use Codememory\Components\Caching\Interfaces\CacheInterface;
use Codememory\FileSystem\Interfaces\FileInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * Class Cache
 * @package Codememory\Components\Big
 *
 * @author  Codememory
 */
class Cache
{

    private const TEMPLATE_CACHE_NAME_PREFIX = '__cdm-engine-big=%s';
    private const TYPE_CACHE = 'template';

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var string|null
     */
    private ?string $templateText;

    /**
     * Cache constructor.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {

        $this->cache = $cache;

    }

    /**
     * @param string|null $text
     *
     * @return $this
     */
    public function setTemplateText(?string $text): Cache
    {

        $this->templateText = $text;

        return $this;

    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getFullNameTemplateCache(string $templateName): string
    {

        return sprintf(self::TEMPLATE_CACHE_NAME_PREFIX, $templateName);

    }

    /**
     * @param string $templateName
     *
     * @return Cache
     */
    public function save(string $templateName): Cache
    {

        $this->cache->create(
            self::TYPE_CACHE,
            $this->getFullNameTemplateCache($templateName),
            $this->templateText,
            function (FileInterface $filesystem, string $fullPath, mixed $data, array &$history) use ($templateName) {
                $phpTemplateFilename = $fullPath . '.php';
                $filesystem->writer->open($phpTemplateFilename, 'r', true)->put($data);

                $history = $this->additionalHistoryData($templateName);
            }
        );

        return $this;

    }

    /**
     * @param string $templateName
     *
     * @return string|null
     */
    public function getTemplateFromCache(string $templateName): ?string
    {

        return $this->cache->get(self::TYPE_CACHE, $this->getFullNameTemplateCache($templateName));

    }

    /**
     * @param string $templateName
     *
     * @return array
     */
    #[Pure]
    #[ArrayShape(['cacheName' => "string", 'name' => "string", 'otherExtensions' => "string[]"])]
    private function additionalHistoryData(string $templateName): array
    {

        return [
            'cacheName'       => $this->getFullNameTemplateCache($templateName),
            'name'            => $templateName,
            'otherExtensions' => ['php']
        ];

    }

}