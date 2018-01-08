<?php

namespace PEtoDiscord;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\level\Level;
use pocketmine\Server;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\event\server\RemoteServerCommandEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;

class Main extends PluginBase implements Listener{

public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->saveDefaultConfig();
$this->getLogger()->info("PEtoDiscord on.");
}
public function onDisable(){
$this->getLogger()->info("PEtoDiscord off.");
}

public function discordPost($url, $message, $name, $avatar) {
if(isset($url) or $url != null){
$data = array("content" => $message, "username" => $name, "avatar_url" => $avatar);
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
return curl_exec($curl);
}
}

public function onJoin(PlayerJoinEvent $event){
$discord = $this->getConfig()->get("webhook1");
$username = $this->getConfig()->get("username1");
$avatar = $this->getConfig()->get("avatar1");

$this->discordPost($discord, "***" . $event->getPlayer()->getName() . " Joined.***", $username, $avatar);
}

public function onLeave(PlayerQuitEvent $event){
$discord = $this->getConfig()->get("webhook1");
$username = $this->getConfig()->get("username1");
$avatar = $this->getConfig()->get("avatar1");

$this->discordPost($discord, "***" . $event->getPlayer()->getName() . " Left.***", $username, $avatar);
}

public function onChat(PlayerChatEvent $event){
$discord = $this->getConfig()->get("webhook1");
$username = $this->getConfig()->get("username1");
$avatar = $this->getConfig()->get("avatar1");

$name = $event->getPlayer()->getName();
$this->discordPost($discord, $name . ": " . $event->getMessage(), $username, $avatar);
}

public function onEntDamage(EntityDamageEvent $event){
$discord = $this->getConfig()->get("webhook1");
$username = $this->getConfig()->get("username1");
$avatar = $this->getConfig()->get("avatar1");
$cause = $event->getCause();
$entity = $event->getEntity();
$player = $this->getServer()->getPlayer($entity);
if($cause == EntityDamageEvent::CAUSE_SUICIDE){
$this->discordPost($discord, "***" . $event->getEntity()->getName() . " commited suicide.***", $username, $avatar);
}
}

public function onDeath(PlayerDeathEvent $event){
$discord = $this->getConfig()->get("webhook1");
$username = $this->getConfig()->get("username1");
$avatar = $this->getConfig()->get("avatar1");
$entity = $event->getEntity();
$cause = $entity->getLastDamageCause();
if($cause instanceof EntityDamageByEntityEvent){
$killer = $cause->getDamager();
$this->discordPost($discord, "***" . $killer->getName()  . " Killed " . $entity->getName() . "***", $username, $avatar);
}
}

public function onPlayerCommand(PlayerCommandPreprocessEvent $event){
$discord = $this->getConfig()->get("webhook2");
$username = $this->getConfig()->get("username2");
$avatar = $this->getConfig()->get("avatar2");
$logs = $this->getConfig()->get("logs");
$name = $event->getPlayer()->getDisplayName();
$command = $event->getMessage();
if($logs == true){
if($command[0] == '/' or $command[0] == "." && $command[1] == '/'){
$this->discordPost($discord, $name . ": " . $command, $username, $avatar);
}
}
}

public function onServerCommand(ServerCommandEvent $event){
$discord = $this->getConfig()->get("webhook2");
$username = $this->getConfig()->get("username2");
$avatar = $this->getConfig()->get("avatar2");
$logs = $this->getConfig()->get("logs");
$name = "CONSOLE";
$command = $event->getCommand();
if($logs == true){
if($command != "list" or $command != "/list"){
$this->discordPost($discord, $name . ": /" . $command, $username, $avatar);
}
}
}

public function onRconCommand(RemoteServerCommandEvent $event){
$discord = $this->getConfig()->get("webhook2");
$username = $this->getConfig()->get("username2");
$avatar = $this->getConfig()->get("avatar2");
$logs = $this->getConfig()->get("logs");
$name = "Rcon";
$command = $event->getCommand();
if($logs == true){
$this->discordPost($discord, $name . ": /" . $command, $username, $avatar);
}
}

}
