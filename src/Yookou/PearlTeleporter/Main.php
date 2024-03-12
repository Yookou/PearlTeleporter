<?php

namespace Yookou\PearlTeleporter;

use customiesdevs\customies\item\CustomiesItemFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Yookou\PearlTeleporter\item\PrePearl;

class Main extends PluginBase {
	use SingletonTrait;

	protected function onLoad() : void {
		self::setInstance($this);
		$this->saveDefaultConfig();
	}

	protected function onEnable() : void {
		CustomiesItemFactory::getInstance()->registerItem(PrePearl::class, "prepearl:prepearl_item", "PrePearl Item");
	}
}
