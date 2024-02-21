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

    /**
     * Activate redstone at given coordinates.
     *
     * @param mixed $level
     * @param int   $x
     * @param int   $y
     * @param int   $z
     */
    private function activateRedstoneAt($level, int $x, int $y, int $z): void {
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
