<?php

namespace Yookou\PearlTeleporter\item;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\CustomiesItemFactory;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use Yookou\PearlTeleporter\Main;

class PrePearl extends Item implements ItemComponents {
    use ItemComponentsTrait {
        getComponents as _getComponents;
    }
    private array $cooldown = [];

	public function __construct(ItemIdentifier $identifier, string $name) {
		parent::__construct($identifier, $name);
		$this->initComponent("prepearlItem", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS));
		$this->setLore([Main::getInstance()->getConfig()->getNested("item.lore")]);
	}

	public function getMaxStackSize() : int {
		return Main::getInstance()->getConfig()->getNested("item.max-stack-size", 16);
	}

	public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult {
		$this->useItem($player);

		return ItemUseResult::SUCCESS();
	}

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, array &$returnedItems) : ItemUseResult {
		$this->useItem($player);

		return ItemUseResult::SUCCESS();
	}


    public function useItem(Player $player) : void {
        $playerName = $player->getName();
        if (!isset($this->cooldown[$playerName]) || $this->cooldown[$playerName] - time() <= 0) {
            $plugin = Main::getInstance();
            $this->cooldown[$playerName] = time() + $plugin->getConfig()->getNested("item.cooldown");

            $playerPosition = $player->getPosition();
            $player->sendPopup(str_replace("{time}", $plugin->getConfig()->get("teleport-time"), $plugin->getConfig()->get("prepearl-message")));

            $plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $playerPosition) : void {
                $player->teleport($playerPosition);
                $player->sendPopup(Main::getInstance()->getConfig()->get("teleported-message"));
            }), $plugin->getConfig()->get("teleport-time") * 20);

            $player->getInventory()->removeItem(CustomiesItemFactory::getInstance()->get("prepearl:prepearl_item")->setCount(1));
        } else {
            $time = $this->cooldown[$playerName] - time();
            $player->sendPopup(str_replace("{cooldown}", $time, Main::getInstance()->getConfig()->getNested("item.cooldown-message")));
        }
    }
}
