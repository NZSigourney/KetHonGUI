<?php

namespace GreenJajot\Marry;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;

class MarryListener {

	public function __construct($plugin, Inventory $inv){
		/**
		 * @param KitSystem $plugin
		 * @param Player $player
		 */
		 
		$this->plugin = $plugin;
		$this->inv = $inv;
	}

	public function onTransaction(Player $player, Item $itemClickedOn, Item $itemClickedWith){
	    if($itemClickedOn->getNamedTag()->hasTag("marry")){
	        $menu = $itemClickedOn->getNamedTag()->getString("marry");
	        if($menu == "request"){
	            $player->removeAllWindows();
			    $this->plugin->listform($player);
	        }
        }
    }
}