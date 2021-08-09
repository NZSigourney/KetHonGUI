<?php

namespace GreenJajot\Marry;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};

class RequestListener {

	public function __construct($plugin,$name2){
		/**
		 * @param KitSystem $plugin
		 * @param Player $player
		 */
		 
		$this->plugin = $plugin;
		$this->player2 = $name2;
	}

	public function onTransaction(Player $player, Item $itemClickedOn, Item $itemClickedWith){
	    if($itemClickedOn->getNamedTag()->hasTag("marry")){
	    	$menu = $itemClickedOn->getNamedTag()->getString("marry");
            if($menu == "yes"){
                $player->removeAllWindows();
			    $name1 = $player->getName();
			    $name2 = $this->player2->getName();
			    $player1 = $this->plugin->getServer()->getPlayer($name1);
			    $player2 = $this->player2;
			    $this->plugin->getServer()->broadcastMessage("§l§e".$name1."§f Và §e".$name2."§b Vừa Cưới Nhau Thành Công.§a Chúc bạn trăm năm hạnh phúc!!");
			    $suffix = "$name1 ❤ $name2";
			    $suffix1 = str_replace(" ","-",$name1);
			    $suffix2 = str_replace(" ","-",$name2);
			
			    $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$name1.' pchat.command.setsuffix');
			    $this->plugin->getServer()->getCommandMap()->dispatch($player1,"setsuffix $suffix2");
			    $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'unsetuperm '.$name1.' pchat.command.setsuffix');
			    $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$name2.' pchat.command.setsuffix');
			    $this->plugin->getServer()->dispatchCommand($player2 ,"setsuffix ".$suffix1);
		    	$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'unsetuperm '.$name2.' pchat.command.setsuffix');
		    	$this->plugin->marrylist->set($suffix, 0);
	         	$this->plugin->marrylist->save();
            }
            if($menu == "no"){
		    	$player->removeAllWindows();
            }
	    }
    }
}