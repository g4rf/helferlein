<?php
class Config {
    const CONFIGFILE = '../config.json';
    const CONFIGEXAMPLEFILE = '../config.example.json';
    
    public static function get() {
        return json_decode(file_get_contents(self::CONFIGFILE), true);
    }
    
    public static function set($key, $value) {
        if(! is_writable(self::CONFIGFILE)) {   
            die(_('FATAL: Es fehlen Schreibrechte für die Konfigurationsdatei.'));
        }
        
        $config = self::get();
        $config[$key] = $value;
        return file_put_contents(self::CONFIGFILE, 
                json_encode($config, JSON_FORCE_OBJECT));
    }    
    
    public static function check() {
        if(! file_exists(self::CONFIGFILE)) {
            if(file_put_contents(self::CONFIGFILE,
                    file_get_contents (self::CONFIGEXAMPLEFILE)) === false) {
                // can't write config file
                die(_('FATAL: Es fehlen Schreibrechte für die Konfigurationsdatei.'));
            }
        }
    }    
}