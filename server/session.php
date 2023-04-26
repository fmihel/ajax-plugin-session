<?php
namespace fmihel\Ajax\Session;

use fmihel\Ajax\AjaxPlugin;

require_once __DIR__ . '/session/iSession.php';
require_once __DIR__ . '/session/SessionDefault.php';

class session extends AjaxPlugin
{

    private static $session;

    public function __construct($sessionClass = 'fmihel\Ajax\Session\SessionDefault')
    {
        self::$session = new $sessionClass();
    }

    public function before($pack)
    {
        $to = $pack['to'];

        if ($to === 'session/autorize') {
            $this->router::out(session::autorize($this->router::$data));

        }if ($pack['to'] === 'session/logout') {

            session::logout();
            $this->router::out(['session' => []]);

        } else {
            if (!isset($pack['session']) || empty(session::autorize($pack['session']))) {
                $this->router::error('no autorize', ['session' => []]);
            }
        }
        return $pack;

    }

    public static function autorize($param = [])
    {
        return self::$session->autorize($param);
    }

    public static function logout()
    {
        self::$session->logout();
    }

    public static function enabled()
    {
        return self::$session->enabled();
    }

}
