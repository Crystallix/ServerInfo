<?php

namespace ServerInfo\lgsys;

use pocketmine\Player;
use pocketmine\utils\Config;

class LanguageManager
{
    /** @var Language[] */
    public $languages = array();
    /** @var string */
    public $default = "";

    /**
     * @param string $dir
     */
    public function __construct($dir)
    {
        $files = \scandir($dir);

        foreach ($files as $file) {
            if ($file !== "." && $file !== ".." && \substr($file, strlen($file) - 4, strlen($file)) === ".yml") {
                $cfg = new Config($dir . $file, Config::YAML);

                $name = substr($file, 0, strlen($file) - 4);
                $language = new Language($name, $cfg);

                $this->languages[$name] = $language;
            }
        }

        $cfg = new Config($dir . "../langConfig.yml", Config::YAML);
        if ($cfg->exists("default")) {
            $this->default = $cfg->get("default");
        } else {
            $cfg->set("default", "en");
            $cfg->save();
            $this->default = "en";
        }
    }

    /**
     * Send the translated message from default language to $player
     * Arguments are optional, unlimited number
     *
     * @param Player $player
     * @param string $name
     */
    public function sendTranslation(Player $player, $name)
    {
        /** @var Language */
        $language = $this->languages[$this->default];

        $args = \func_get_args();
        \array_shift($args);
        \array_shift($args);

        $msg = $language->getTranslation($name, $args);

        $player->sendMessage($msg);
    }
}
