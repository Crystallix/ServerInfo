<?php

namespace ServerInfo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use ServerInfo\lgsys\LanguageManager;

class ServerInfo extends PluginBase
{
    /**
     * @var LanguageManager
     */
    private $languageManager = null;

    public function onEnable()
    {
        $this->getLogger()->info(TextFormat::AQUA . "Starting ServerInfo plugin...");

        $dir = $this->getDataFolder() . DIRECTORY_SEPARATOR . "languages" . DIRECTORY_SEPARATOR;
        $this->createLanguages();
        $this->languageManager = new LanguageManager($dir);

        $this->getLogger()->info(TextFormat::AQUA . "ServerInfo plugin started.");
    }

    /**
     * Create the default language configs
     */
    private function createLanguages()
    {
        $this->saveResource("languages/de.yml");
        $this->saveResource("languages/en.yml");
    }

    /**
     * @return LanguageManager
     */
    public function getLanguageManager()
    {
        return $this->languageManager;
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool
    {
        if ($sender instanceof Player) {
            switch ($cmd->getName()) {
                case "serverinfo":
                    $this->getLanguageManager()->sendTranslation($sender, "serverinfo_message", $this->getServer()->getVersion(), sizeof($sender->getLevel()->getEntities()), sizeof($this->getServer()->getOnlinePlayers()), $this->getServer()->getIp());
                    return true;
                // TODO: LastSeen, PlayerIP
            }
        }
        return true;
    }
}
