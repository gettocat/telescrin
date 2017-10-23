<?php

Class AjaxEvent {

    public static function dispatch($evt) {

        if ($act = $_POST['action']) {
            $data = $_POST['data'];
            $result = $evt($act, $data);
            $result['status'] = 1;
            static::output($result);
        }
    }

    public static function dispatchEvent($event_name, $evt) {

        return static::dispatch(function($act, $data) use($event_name, $evt) {
                    if ($act == $event_name)
                        return $evt($act, $data);

                    return false;
                });
    }

    public static function output($data) {
        die(json_encode($data));
    }

}
