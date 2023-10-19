<?php

namespace yaku;

use customiesdevs\customies\item\CustomiesItemFactory;
use yaku\item\PrePearl;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {
    use SingletonTrait;

    public function onEnable(): void {

        self::setInstance($this);
        $this->saveDefaultConfig();
        $customies = CustomiesItemFactory::getInstance();
        $customies->registerItem(PrePearl::class, "prepearl:prepearl_item", "PrePearl Item");
    }
}
