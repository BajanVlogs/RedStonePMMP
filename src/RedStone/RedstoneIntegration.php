<?php

namespace RedStone;

use pocketmine\block\Block;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

class RedstoneIntegration extends PluginBase {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onRedstonePlace(BlockPlaceEvent $event): void {
        // Handle redstone block placement here
        $placedBlock = $event->getBlock(); // Get the placed block from the event

        // Your custom redstone placement logic goes here

        // Example: Activate adjacent redstone components
        $this->activateAdjacentRedstone($placedBlock);

        // Broadcast a message
        $event->getPlayer()->sendMessage("Custom Redstone Block placed!");
    }

    private function activateAdjacentRedstone(Block $block): void {
        $level = $block->getLevel();
        $x = $block->getX();
        $y = $block->getY();
        $z = $block->getZ();

        // Activate adjacent redstone components
        $this->activateRedstoneAt($level, $x + 1, $y, $z);
        $this->activateRedstoneAt($level, $x - 1, $y, $z);
        $this->activateRedstoneAt($level, $x, $y + 1, $z);
        $this->activateRedstoneAt($level, $x, $y - 1, $z);
        $this->activateRedstoneAt($level, $x, $y, $z + 1);
        $this->activateRedstoneAt($level, $x, $y, $z - 1);
    }

    private function activateRedstoneAt($level, $x, $y, $z): void {
        $block = $level->getBlock(new Vector3($x, $y, $z)); // Get the block at the specified coordinates

        // Check if the block is redstone-related (e.g., redstone dust, torch, repeater)
        if (in_array($block->getId(), [
            Block::REDSTONE_WIRE,
            Block::REDSTONE_TORCH,
            Block::REDSTONE_REPEATER,
            Block::REDSTONE_BLOCK
        ])) {
            // Activate the redstone component
            $block->setActivated(true);
            $level->setBlock(new Vector3($x, $y, $z), $block);
        }
    }
}
