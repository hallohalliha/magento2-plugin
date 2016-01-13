<?php

namespace Tinify\Magento\Model;

use Magento\Catalog\Model\Product\Media\ConfigInterface as MediaConfig;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ProductMetadataInterface as MagentoInfo;
use Magento\Framework\Filesystem;
use Magento\Swatches\Model\Swatch;

class Config
{
    const KEY_PATH = "tinify_compress_images/general/key";
    const TYPES_PATH = "tinify_compress_images/types";

    protected $magentoInfo;
    protected $config;
    protected $mediaConfig;
    protected $mediaDirectory;

    public function __construct(
        MagentoInfo $magentoInfo,
        ScopeConfig $config,
        MediaConfig $mediaConfig,
        Filesystem $filesystem
    ) {
        $this->magentoInfo = $magentoInfo;
        $this->config = $config;
        $this->mediaConfig = $mediaConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function isOptimizableType($type)
    {
        switch (strtolower($type)) {
            case "thumbnail":
                $type = "thumbnail";
                break;
            case "small_image":
                $type = "small";
                break;
            case "swatch_thumb":
            case "swatch_image":
                $type = "swatch";
                break;
            case "image":
            default:
                $type = "base";
        }

        return $this->config->isSetFlag(self::TYPES_PATH . "/" . $type);
    }

    public function getKey()
    {
        return trim($this->config->getValue(self::KEY_PATH));
    }

    public function getMagentoId()
    {
        $name = $this->magentoInfo->getName();
        $version = $this->magentoInfo->getVersion();
        $edition = $this->magentoInfo->getEdition();
        return "{$name}/{$version} ({$edition})";
    }

    public function getPathPrefix()
    {
        return $this->mediaConfig->getBaseMediaPath() . "/optimized";
    }

    public function getMediaUrl($path)
    {
        /* Remove catalog/product prefix, it's re-added by getMediaUrl(). */
        $prefix = $this->mediaConfig->getBaseMediaPath() . "/";
        if (substr($path, 0, strlen($prefix)) == $prefix) {
            $path = substr($path, strlen($prefix));
        }
        return $this->mediaConfig->getMediaUrl($path);
    }

    public function getMediaPath($path)
    {
        return $this->mediaDirectory->getAbsolutePath($path);
    }

    public function getMediaDirectory()
    {
        return $this->mediaDirectory;
    }
}
