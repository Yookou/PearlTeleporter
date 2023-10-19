<?php

namespace yaku\item;

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
use yaku\Main;
use yaku\task\PrePearlTask;

class PrePearl extends Item implements ItemComponents {
    use ItemComponentsTrait;
    private static array $cooldown = [];

    public function __construct(ItemIdentifier $identifier, string $name = 'Unknown') {
        parent::__construct($identifier, $name);
        $this->initComponent("prepearlItem", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS));
        $this->setLore([Main::getInstance()->getConfig()->get("lore")]);
    }

    public function getMaxStackSize(): int {
        return Main::getInstance()->getConfig()->get("max-stack-size", 16);
    }

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult {
        $this->useItem($player);
        return ItemUseResult::SUCCESS();
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, array &$returnedItems): ItemUseResult {
        $this->useItem($player);
        return ItemUseResult::SUCCESS();
    }

    public function useItem(Player $player) : void {
        if (!isset(self::$cooldown[$player->getName()]) || self::$cooldown[$player->getName()] - time() <= 0) {
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new PrePearlTask($player), 10);
            self::$cooldown[$player->getName()] = time() + Main::getInstance()->getConfig()->get("cooldown");
            $player->getInventory()->removeItem(CustomiesItemFactory::getInstance()->get("prepearl:prepearl_item")->setCount(1));
        } else {
            $timeRestant = self::$cooldown[$player->getName()] - time();
            $player->sendPopup(str_replace("{cooldown}", $timeRestant, Main::getInstance()->getConfig()->get("cooldown-message")));
        }
    }
}
