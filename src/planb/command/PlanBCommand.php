<?php

namespace planb\command;

use planb\PlanB;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class PlanBCommand extends Command{
    /** @var PlanB */
    private $plugin;
    /**
     * @param PlanB $plugin
     */
    public function __construct(PlanB $plugin){
        parent::__construct("planb", "Shows all PlanB commands", null, ["pb"]);
        $this->setPermission("planb.command.planb");
        $this->plugin = $plugin;
    }
    /** 
     * @param CommandSender $sender 
     */
    private function sendCommandHelp(CommandSender $sender){
        $commands = [
            "help" => "Shows all PlanB commands",
            "list" => "Lists all backup players",
            "restore" => "Restores OP status of all online players listed in backup.txt"
        ];
        $sender->sendMessage("PlanB commands:");
        foreach($commands as $name => $description){
            $sender->sendMessage("/planb ".$name.": ".$description);
        }
    }
    /**
     * @param CommandSender $sender
     * @param string $label
     * @param string[] $args
     * @return bool
     */
    public function execute(CommandSender $sender, $label, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(isset($args[0])){
            switch(strtolower($args[0])){
                case "help":
                    $this->sendCommandHelp($sender);
                    return true;
                case "l":
                case "list":
                    $this->plugin->sendBackups($sender);
                    return true;
                case "r":
                case "restore":
                    if($this->plugin->isBackupPlayer($sender->getName()) or $sender instanceof ConsoleCommandSender){
                        $this->plugin->restoreOps();
                        $sender->sendMessage(TextFormat::YELLOW."Restoring the statuses of OPs...");
                    }
                    else{
                        $sender->sendMessage($this->plugin->getConfig()->get("noPermissionMessage"));
                    }
                    return true;
                default:
                    $sender->sendMessage("Usage: /planb <sub-command> [parameters]");
                    return false;
            }
        }
        else{
            $this->sendCommandHelp($sender);
            return false;
        }
    }
}
