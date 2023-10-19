<?php

namespace yaku\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use yaku\Main;

class PrePearlTask extends Task {
    public int $time;
    public $coordsx;
    public $coordsy;
    public $coordsz;

    public function __construct(public Player $sender) {
    }
    public function onRun() : void {
        if (!isset($this->time)) {
            $this->time = Main::getInstance()->getConfig()->get("teleport-time");
            $this->coordsx = $this->sender->getPosition()->getX();
            $this->coordsy = $this->sender->getPosition()->getY();
            $this->coordsz = $this->sender->getPosition()->getZ();
            $this->sender->sendPopup(Main::getInstance()->getConfig()->get("prepearl-message"));
        } elseif ($this->time > 0) {
            $this->time--;
        } else {
            $this->sender->teleport(new Position($this->coordsx, $this->coordsy, $this->coordsz, $this->sender->getWorld()));
            $this->sender->sendPopup(Main::getInstance()->getConfig()->get("teleported-message"));
            $this->getHandler()->cancel();
        }
    }


}
