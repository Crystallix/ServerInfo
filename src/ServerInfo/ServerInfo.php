<?php

namespace ServerInfo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use ServerInfo\autoupdater\AutoUpdater;
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

        AutoUpdater::searchForUpdates($this, "https://raw.githubusercontent.com/Crystallix/ServerInfo/master/updates.yml", $this->getDescription()->getVersion());

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
                case "lastseen":
                    if (empty($args[0])) {
                        $this->getLanguageManager()->sendTranslation($sender, "lastseen_usage", $cmd->getUsage());
                        return true;
                    }

                    $offlinePlayer = $this->getServer()->getOfflinePlayer($args[0]);
                    if (!$offlinePlayer->hasPlayedBefore()) {
                        $this->getLanguageManager()->sendTranslation($sender, "lastseen_never_joined", $args[0]);
                        return true;
                    }

                    $player = $offlinePlayer->getPlayer();
                    if ($player !== null) {
                        $this->getLanguageManager()->sendTranslation($sender, "lastseen_online", $player->getName());
                        return true;
                    }

                    $date = date("d.m.y", $offlinePlayer->getLastPlayed() / 1000);
                    $time = date("h:ia", $offlinePlayer->getLastPlayed() / 1000);

                    $this->getLanguageManager()->sendTranslation($sender, "lastseen_success", $offlinePlayer->getName(), $date, $time);
                    return true;
                case "playerip":
                    if (empty($args[0])) {
                        $this->getLanguageManager()->sendTranslation($sender, "playerip_message", $sender->getName(), $sender->getAddress());
                        return true;
                    }

                    $player = $this->getServer()->getPlayer($args[0]);
                    if ($player === null) {
                        $this->getLanguageManager()->sendTranslation($sender, "playerip_offline", $args[0]);
                        return true;
                    }

                    $this->getLanguageManager()->sendTranslation($sender, "playerip_message", $player->getName(), $player->getAddress());
                    return true;
            }
        }
        return true;
    }
}
