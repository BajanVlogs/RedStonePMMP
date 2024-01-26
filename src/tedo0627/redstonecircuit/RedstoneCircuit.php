<?php

namespace tedo0627\redstonecircuit;

use Closure;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockToolType;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use ReflectionMethod;
use tedo0627\redstonecircuit\block\BlockTable;
use tedo0627\redstonecircuit\block\entity\BlockEntityChest;
use tedo0627\redstonecircuit\block\entity\BlockEntityCommand;
use tedo0627\redstonecircuit\block\entity\BlockEntityDispenser;
use tedo0627\redstonecircuit\block\entity\BlockEntityDropper;
use tedo0627\redstonecircuit\block\entity\BlockEntityHopper;
use tedo0627\redstonecircuit\block\entity\BlockEntityMoving;
use tedo0627\redstonecircuit\block\entity\BlockEntityNote;
use tedo0627\redstonecircuit\block\entity\BlockEntityObserver;
use tedo0627\redstonecircuit\block\entity\BlockEntityPistonArm;
use tedo0627\redstonecircuit\block\entity\BlockEntitySkull;
use tedo0627\redstonecircuit\block\entity\BlockEntityTarget;
use tedo0627\redstonecircuit\block\mechanism\BlockActivatorRail;
use tedo0627\redstonecircuit\block\mechanism\BlockCommand;
use tedo0627\redstonecircuit\block\mechanism\BlockDispenser;
use tedo0627\redstonecircuit\block\mechanism\BlockDropper;
use tedo0627\redstonecircuit\block\mechanism\BlockFenceGate;
use tedo0627\redstonecircuit\block\mechanism\BlockHopper;
use tedo0627\redstonecircuit\block\mechanism\BlockIronDoor;
use tedo0627\redstonecircuit\block\mechanism\BlockIronTrapdoor;
use tedo0627\redstonecircuit\block\mechanism\BlockMoving;
use tedo0627\redstonecircuit\block\mechanism\BlockNote;
use tedo0627\redstonecircuit\block\mechanism\BlockPiston;
use tedo0627\redstonecircuit\block\mechanism\BlockPistonArmCollision;
use tedo0627\redstonecircuit\block\mechanism\BlockPoweredRail;
use tedo0627\redstonecircuit\block\mechanism\BlockRedstoneLamp;
use tedo0627\redstonecircuit\block\mechanism\BlockSkull;
use tedo0627\redstonecircuit\block\mechanism\BlockStickyPiston;
use tedo0627\redstonecircuit\block\mechanism\BlockStickyPistonArmCollision;
use tedo0627\redstonecircuit\block\mechanism\BlockTNT;
use tedo0627\redstonecircuit\block\mechanism\BlockWoodenDoor;
use tedo0627\redstonecircuit\block\mechanism\BlockWoodenTrapdoor;
use tedo0627\redstonecircuit\block\power\BlockDaylightSensor;
use tedo0627\redstonecircuit\block\power\BlockJukeBox;
use tedo0627\redstonecircuit\block\power\BlockLever;
use tedo0627\redstonecircuit\block\power\BlockObserver;
use tedo0627\redstonecircuit\block\power\BlockRedstone;
use tedo0627\redstonecircuit\block\power\BlockRedstoneTorch;
use tedo0627\redstonecircuit\block\power\BlockStoneButton;
use tedo0627\redstonecircuit\block\power\BlockStonePressurePlate;
use tedo0627\redstonecircuit\block\power\BlockTarget;
use tedo0627\redstonecircuit\block\power\BlockTrappedChest;
use tedo0627\redstonecircuit\block\power\BlockTripwire;
use tedo0627\redstonecircuit\block\power\BlockTripwireHook;
use tedo0627\redstonecircuit\block\power\BlockWeightedPressurePlateHeavy;
use tedo0627\redstonecircuit\block\power\BlockWeightedPressurePlateLight;
use tedo0627\redstonecircuit\block\power\BlockWoodenButton;
use tedo0627\redstonecircuit\block\power\BlockWoodenPressurePlate;
use tedo0627\redstonecircuit\block\transmission\BlockRedstoneComparator;
use tedo0627\redstonecircuit\block\transmission\BlockRedstoneRepeater;
use tedo0627\redstonecircuit\block\transmission\BlockRedstoneWire;
use tedo0627\redstonecircuit\listener\CommandBlockListener;
use tedo0627\redstonecircuit\listener\InventoryListener;
use tedo0627\redstonecircuit\listener\TargetBlockListener;
use tedo0627\redstonecircuit\loader\BlockEntityLoader;
use tedo0627\redstonecircuit\loader\BlockLoader;
use tedo0627\redstonecircuit\loader\ItemBlockLoader;
use tedo0627\redstonecircuit\loader\Loader;

class RedstoneCircuit extends PluginBase {

    private static bool $callEvent = false;

    /** @var Loader[] */
    private array $loader = [];

    public function onLoad(): void {
        // The provided code for the `onLoad` method in the question

        self::registerMappings();
        $this->getServer()->getAsyncPool()->addWorkerStartHook(function (int $worker): void {
            $this->getServer()->getAsyncPool()->submitTaskToWorker(new class extends AsyncTask {
                public function onRun(): void {
                    RedstoneCircuit::registerMappings();
                }
            }, $worker);
        });

        CreativeInventory::reset();
    }

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new CommandBlockListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new InventoryListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new TargetBlockListener(), $this);

        self::$callEvent = $this->getConfig()->get("event", false);
    }

    private function overrideBlock(string $name, int $id, Closure $creation, Closure $onDrop): void {
        $block = new BlockTable($id, $name);
        $block->setVanillaCreation($creation);
        $block->setVanillaOnDrop($onDrop);
        $this->getServer()->getRuntimeBlockMapping()->add(new RuntimeBlockMapping($block));
    }

    public function registerLoader(string $name, Loader $loader): void {
        $this->loader[$name] = $loader;
    }

    public function onLoadListeners(): void {
        foreach ($this->loader as $loader) {
            $loader->register($this);
        }
    }

    public function registerCommands(): void {
        // Code for registering commands
    }

    public static function callEvent(): bool {
        return self::$callEvent;
    }

    public static function registerMappings(): void {
        // Register mappings
    }

    public static function writeBlockRuntime(Block $block): void {
        // Write block runtime
    }

    public static function writeItemRuntime(ItemIdentifier $item): void {
        // Write item runtime
    }
}
