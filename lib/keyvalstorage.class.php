<?php

Class keyvalStorage {

    protected static $instance = null;
    protected $file = 'db';

    public function __construct($storagePath, $file) {
        $this->file = $file;
        $this->path = $storagePath ? $storagePath : (getcwd() . "/");
        $this->fullPath = $this->path . $this->file;
    }

    public function getKey($key) {
        $data = json_decode(file_get_contents($this->fullPath), true);
        return $data[$key];
    }

    public function setKey($key, $val) {
        $data = json_decode(file_get_contents($this->fullPath), true);
        $data[$key] = $val;
        file_put_contents($this->fullPath, json_encode($data));
        return true;
    }

    public static function get() {

        if (!static::$instance) {
            static::$instance = new keyvalStorage(Config::$storagePath, Config::$storageFile);
        }

        return static::$instance;
    }

}
