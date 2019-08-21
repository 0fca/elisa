<?php


final class ConfigurationProvider
{
        static private $configObject = array();

        static public function loadConfiguration(){
            self::$configObject = unserialize(file_get_contents(configPath));
        }

        static public function getConfigurationField(string $name){
            return self::$configObject[$name];
        }
}