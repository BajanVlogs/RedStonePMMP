<?php

namespace RedStone;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;

class RedstoneIntegration extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $this->handleRedstoneActivation($event->getBlock());
    }

    /**
     * Handle redstone activation and adjacent activation.
     *
     * @param Block $block
     */
    public function handleRedstoneActivation(Block $block): void {
        $world = $block->getLevel();
        $x = $block->getX();
        $y = $block->getY();
        $z = $block->getZ();

        // Activate adjacent redstone components
        $this->activateRedstoneAt($world, $x + 1, $y, $z);
        $this->activateRedstoneAt($world, $x - 1, $y, $z);
        $this->activateRedstoneAt($world, $x, $y + 1, $z);
        $this->activateRedstoneAt($world, $x, $y - 1, $z);
        $this->activateRedstoneAt($world, $x, $y, $z + 1);
        $this->activateRedstoneAt($world, $x, $y, $z - 1);
    }

    /**
     * Activate redstone at given coordinates.
     *
     * @param mixed $world
     * @param int   $x
     * @param int   $y
     * @param int   $z
     */
    private function activateRedstoneAt($world, int $x, int $y, int $z): void {
        $block = $world->getBlock(new Vector3($x, $y, $z)); // Get the block at the specified coordinates

        // Check if the block is redstone-related (e.g., redstone dust, torch, repeater)
        if (in_array($block->getId(), [
            Block::REDSTONE_WIRE,
            Block::REDSTONE_TORCH,
            Block::REDSTONE_REPEATER,
            Block::REDSTONE_BLOCK
        ])) {
            // Activate the redstone component
            $block->setActivated(true);
            $world->setBlock(new Vector3($x, $y, $z), $block);
        }
    }
}
