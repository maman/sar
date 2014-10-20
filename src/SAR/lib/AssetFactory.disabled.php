<?php

namespace SAR\lib;

use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Worker\CacheBustingWorker;

/**
* Asset Managements with Assetic
*/
class AssetFactory
{
    public $factory;
    public $manager;
    private static $instance;

    private function __construct() {
         $af = new AssetFactory(Config::read('path.assets'));
         $af->addWorker(new CacheBustingWorker(CacheBustingWorker::STRATEGY_CONTENT));
         $am = new LazyAssetManager($af);
         $this->factory = $af;
         $this->manager = $am;
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
}
