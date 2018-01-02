<?php

namespace ServerInfo\lgsys;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Language
{
    public $name = "";
    /** @var Config */
    public $cfg = null;
    /** @var array */
    public $translations = array();

    /**
     * @param string $name
     * @param Config $cfg
     */
    public function __construct($name, $cfg)
    {
        $this->name = $name;
        $this->cfg = $cfg;

        $this->createArray();
    }

    private function createArray()
    {
        $this->translations = $this->cfg->getAll();
    }

    /**
     * Get a translation from this language under the key $name
     * Replaces & with § and {NL} with PHP_EOL (new line)
     *
     * @param string $name
     * @param array $args
     *
     * @return string $msg
     */
    public function getTranslation($name, $args = array())
    {
        if (!isset($this->translations[$name])) {
            return "Error in translation";
        }

        $msg = $this->translations[$name];

        $msg = \str_replace("{NL}", TextFormat::RESET . " \n", $msg);
        $msg = \str_replace("&", "§", $msg);

        if (sizeof($args) > 0) {
            foreach ($args as $key => $value) {
                $msg = \str_replace("{" . $key . "}", $value, $msg);
            }
        }

        return $msg;
    }
}
