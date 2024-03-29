<?php

namespace RedStone;

use pocketmine\block\Block;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;

class RedstoneIntegration extends PluginBase implements Listener {

    private $plugin;

    public function __construct(RedstoneIntegration $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $this->plugin->handleBlockPlacement($event);
    }

    /**
     * Handle redstone activation and adjacent activation.
     *
     * @param Block $block
     */
    public function handleRedstoneActivation(Block $block): void {
        $world = $block->getWorld();
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
