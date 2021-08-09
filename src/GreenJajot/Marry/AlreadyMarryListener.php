<?php

namespace GreenJajot\Marry;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};

class AlreadyMarryListener {

	public function __construct($plugin, $player2){
		/**
		 * @param KitSystem $plugin
		 * @param Player $player
		 */
		 
		$this->plugin = $plugin;
		$this->player2 = $player2;
	}

	public function onTransaction(Player $player, Item $itemClickedOn, Item $itemClickedWith){
	    if($itemClickedOn->getNamedTag()->hasTag("marry")){
	        $name1 = $player->getName();
			$name2 = $this->player2;
			$player1 = $this->plugin->getServer()->getPlayer($name1);
		    $menu = $itemClickedOn->getNamedTag()->getString("marry");
            if($menu == "tp"){
$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$name1.' pocketmine.command.teleport');
		    	$this->plugin->getServer()->getCommandMap()->dispatch($player1,'tp "'.$name2.'"');
		    	$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'unsetuperm '.$name1.' pocketmine.command.teleport');
		    	$player->removeAllWindows();
            }
            if($menu == "invsee"){
                $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$name1.' invsee.inventory');
                $player->removeAllWindows();
			     $this->plugin->getServer()->getCommandMap()->dispatch($player1,'invsee "'.$name2.'"');
			     $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'unsetuperm '.$name1.' invsee.inventory');
            }
            if($menu == "lyhon"){
                if($player2 = $this->plugin->getServer()->getPlayer($name2)){
                    $this->plugin->getServer()->broadcastMessage("§l§e".$name1."§f Và §e".$name2."§b Vừa Ly Hôn!!");
                    $ar = $this->plugin->marrylist->getAll();
	                foreach($ar as $arr=>$al){
	                    $arr1 = explode(" ❤ ", $arr);
	                    if($name2 == $arr1[0]){
	                        $rsuffix = $arr;
	                    }else if($name2 == $arr1[1]){
	                        $rsuffix = $arr;
	                    }else{
	                        return;
	                    }
	                }
	                $suffix = "$name1 ❤ $name2";
	                $suffix1 = "Thất Tình";
	                $suffix2 = "Thất Tình";
	                $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$name1.' pchat.command.setsuffix');
	                $this->plugin->getServer()->getCommandMap()->dispatch($player1,"setsuffix $suffix2");
	                $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'unsetuperm '.$name1.' pchat.command.setsuffix');
	                $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm '.$name2.' pchat.command.setsuffix');
	                $this->plugin->getServer()->dispatchCommand($player2 ,"setsuffix ".$suffix1);
			        $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(),'unsetuperm '.$name2.' pchat.command.setsuffix');
			        $this->plugin->marrylist->remove($rsuffix);
	      	        $this->plugin->marrylist->save();
                }
            }
	    }
	}
}