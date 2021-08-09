<?php

namespace GreenJajot\Marry;

use pocketmine\plugin\PluginBase;
use jojoe77777\FormAPI;
use muqsit\invmenu\inventories\BaseFakeInventory;
use pocketmine\nbt\tag\{CompoundTag, IntTag, StringTag, IntArrayTag};
use pocketmine\utils\Config;
use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};
use pocketmine\event\Event;
use pocketmine\event\player\PlayerJoinEvent;
use muqsit\invmenu\{InvMenu,InvMenuHandler};
use muqsit\invmenu\inventories\ChestInventory;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\scheduler\TaskScheduler;
class Main extends PluginBase implements Listener{
    public $marrylist;
	public function onEnable(){
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->joinlist = new Config($this->getDataFolder() . "joinlist.yml", Config::YAML);
$this->marrylist = new Config($this->getDataFolder() . "marrylist.yml", Config::YAML);
		$this->getLogger()->info("Marry enable");
		if (!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($this);
		}
	}
	
	public function onJoin(PlayerJoinEvent $ev){
		$p = $ev->getPlayer()->getName();
		if(!($this->joinlist->exists($p))){
		    $this->joinlist->set($p, 0);
		    $this->getServer()->dispatchCommand(new ConsoleCommandSender(),'setuperm "'.$p.'" pchat.command.setsuffix');
			$this->getServer()->getCommandMap()->dispatch($ev->getPlayer(),"setsuffix Độc-Thân");
			$this->getServer()->dispatchCommand(new ConsoleCommandSender(),'unsetuperm "'.$p.'" pchat.command.setsuffix');
	      	$this->joinlist->save();
		}
	}
	public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
		   switch($command->getName()){
               case "kethon":
                   $this->marrylist->load($this->getDataFolder() . "marrylist.yml", Config::YAML);
				   $this->marrylist = new Config($this->getDataFolder() . "marrylist.yml", Config::YAML);
			       $ar = $this->marrylist->getAll();
	               foreach($ar as $arr=>$al){
	                   $arr1 = explode(" ❤ ", $arr);
	                   if($sender->getName() == $arr1[0]){
	                       $player2 = $arr1[1];
	                       $this->Marry($sender,$player2);
	                       return true;
	                       
	                   }else if($sender->getName() == $arr1[1]){
	                       $player2 = $arr1[0];
	                       $this->Marry($sender,$player2);
	                       return true;
	                   }
	               }
	               $this->NoMarry($sender);
	               break;
		       
		   }
		   return true;
	}
    
    public function listform(Player $player){
        $lit = [];
        foreach($this->getServer()->getOnlinePlayers() as $player){
            $list[] = $player->getName();
            $this->playerList[$player->getName()] = $list;
        }
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createCustomForm(function(Player $player, $data){
            if($data == null)return;
            $index = $data[0];
            $name = $this->playerList[$player->getName()][$index];
                $player1 = $this->getServer()->getPlayer((string) $name);
                $player2 = $player;
                $name1 = $player1->getName();
                $name2 = $player2->getName();
                if($name1 == $name2){
                    $player->sendMessage("§aBạn Không Thể Cưới Chính Mình");
                    return;
                }
                $ar = $this->marrylist->getAll();
                foreach($ar as $arr=>$al){
                    $arr1 = explode(" ❤ ", $arr);
                    if($name1 == $arr1[0]){
                        $player->sendMessage("§aNgười Này Đã Kết Hôn Rồi");
                        return;
                        
                    }else if($name1 == $arr1[1]){
                        $player->sendMessage("§aNgười Này Đã Kết Hôn Rồi");
                        return;
                    }
                }
                    $this->request($player1,$player2);
                    $player->sendMessage("§l-> §bBạn Đã Gửi Thành Công Cho ".$name1." §f(§aHãy Đợi Người Kia Đồng Ý§f)");
        });
        $form->setTitle("§fKết Hôn");
        $form->addDropdown("§fdanh sách online\n", $this->playerList[$player->getName()]);
        $form->sendToPlayer($player);

    }

    public function request(Player $player1,Player $player2) {
		$name2 = $player2->getName();
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§l§c♥︎§b Yêu Cầu Kết Hôn§c ♥︎ Từ: §8".$name2);
		$menu->readonly(true);
		$minv = $menu->getInventory();
		$air = Item::get(Item::AIR);
		$ai = $air;
		$red = Item::get(360)->setCustomName("§e§l✚§d Từ chối");
		$request = Item::get(175)->setCustomName("§c§l❤§e Kết Hôn");
		$request->setNamedTagEntry(new StringTag("marry", "request"));
		$green = Item::get(79)->setCustomName("§b§l✏§a Đồng ý");
		$green->setNamedTagEntry(new StringTag("marry", "yes"));
		$red->setNamedTagEntry(new StringTag("marry", "no"));
		for($i = 0;$i <=53;$i++){
		    $minv->setItem($i, $air);
		}
		$minv->setItem(20, $green);
		$minv->setItem(24, $red);
		$menu->send($player1);
		$menu->setListener([new RequestListener($this, $player2),"onTransaction"]);
	}
	
	public function NoMarry(Player $player) {
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§l§b☘ §aBạn Đang Độc Thân§b ☘");
		$menu->readonly();
		$minv = $menu->getInventory();
		$air = Item::get(Item::AIR);
		$ai = Item::get(Item::AIR);
		$request = Item::get(175)->setCustomName("§c§l❤§e Kết Hôn");
		$request->setNamedTagEntry(new StringTag("marry", "request"));
		for($i = 0;$i <=53;$i++){
		    $minv->setItem($i, $air);
		}		
		$minv->setItem(22, $request);
		$menu->send($player);
		$menu->setListener([new MarryListener($this, $menu->getInventory()), "onTransaction"]);
	}
	
	public function Marry(Player $player, $player2) {
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§l§e★ §6Menu Kết Hôn§e ★");
		$menu->readonly();
		$minv = $menu->getInventory();
		$air = Item::get(Item::AIR);
		$ai = $air;
		$name2 = $player2;
		$thongtin = Item::get(339)->setCustomName("§l§c♥ Người Yêu Của Bạn:§l§e ".$name2);
		$inv = Item::get(54)->setCustomName("§l§e☘ Xem Hành Trang Người Yêu");
		$tele = Item::get(122)->setCustomName("§d§lϟ§6 Dịch Chuyến Tới Người Yêu");
		$tele->setNamedTagEntry(new StringTag("marry", "tp"));
		$lyhon = Item::get(397, 5)->setCustomName("§d§lϟ§6 Ly Hôn Người Yêu");
		$lyhon->setNamedTagEntry(new StringTag("marry", "lyhon"));
		$inv->setNamedTagEntry(new StringTag("marry", "invsee"));
		for($i = 0;$i <=53;$i++){
		    $minv->setItem($i, $air);
		}			
		$minv->setItem(22, $thongtin);
		$minv->setItem(29, $tele);
		$minv->setItem(33, $inv);
		$minv->setItem(53, $lyhon);
		$menu->send($player);
		$menu->setListener([new AlreadyMarryListener($this, $player2),"onTransaction"]);
	}
}