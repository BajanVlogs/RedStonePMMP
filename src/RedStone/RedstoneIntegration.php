<?php

namespace RedStone;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

class RedstoneIntegration extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onRedstonePlace(BlockPlaceEvent $event) {
        // Handle redstone block placement here
        $placedBlock = $event->getBlock(); // Use getBlock() directly

        // Your custom redstone placement logic goes here

        // Example: Activate adjacent redstone components
        $this->activateAdjacentRedstone($placedBlock);

        // Broadcast a message
        $event->getPlayer()->sendMessage("Custom Redstone Block placed!");
    }

    private function activateAdjacentRedstone(Block $block) {
        $level = $block->getLevel();
        $x = $block->getX();
        $y = $block->getY();
        $z = $block->getZ();

        // Activate adjacent redstone components using getBlockAgainst
        $this->activateRedstoneAt($level, $x + 1, $y, $z);
        $this->activateRedstoneAt($level, $x - 1, $y, $z);
        $this->activateRedstoneAt($level, $x, $y + 1, $z);
        $this->activateRedstoneAt($level, $x, $y - 1, $z);
        $this->activateRedstoneAt($level, $x, $y, $z + 1);
        $this->activateRedstoneAt($level, $x, $y, $z - 1);
    }

    private function activateRedstoneAt(Level $level, $x, $y, $z) {
        $block = $level->getBlockAgainst(new Vector3($x, $y, $z));

        // Check if the block is redstone-related (e.g., redstone dust, torch, repeater)
        if ($block->getId() === Block::REDSTONE_WIRE ||
            $block->getId() === Block::REDSTONE_TORCH_OFF ||
            $block->getId() === Block::REDSTONE_TORCH_ON ||
            $block->getId() === Block::REDSTONE_REPEATER_OFF ||
            $block->getId() === Block::REDSTONE_REPEATER_ON ||
            $block->getId() === Block::REDSTONE_BLOCK) {

            // Activate the redstone component
            $level->setBlock(new Vector3($x, $y, $z), $block->setActivated(true));
        }
    }
}

