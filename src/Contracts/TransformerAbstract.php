<?php

namespace Imamuseum\Harvester2\Contracts;

use Imamuseum\Harvester2\Contracts\TransformerInterface;

/**
 * abstract TransformerAbstract
 * @package
 */
abstract class TransformerAbstract implements TransformerInterface
{
    /**
     * @var config
     */
    protected $config;

    /**
     * Constructor
     * @param $source_name  Name of config file
     * @author Daniel Keller
     */
    public function __construct($source_name)
    {
        $this->config = config($source_name);
        $this->config['source_name'] = $source_name;

        if (!$this->config) {
            throw new Exception("Failed to find config settings for $source_name.");
        }
    }

    public function getConfig()
    {
        return $this->config;
    }
}
