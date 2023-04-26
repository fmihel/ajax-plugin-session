<?php
namespace fmihel\ajax\plugin\session;

use fmihel\ajax\Plugin;

//require_once __DIR__ . '/iSession.php';
//require_once __DIR__ . '/SessionDefault.php';

class session extends Plugin
{

    private static $session;

    public function __construct($sessionClass = 'fmihel\ajax\plugin\session\SessionDefault')
    {
        self::$session = new $sessionClass();
    }

    public function before($pack)
    {
        $to = $pack['to'];

        if ($to === 'session/autorize') {
            $this->ajax::out(session::autorize($this->ajax::$data));

        }if ($pack['to'] === 'session/logout') {

            session::logout();
            $this->ajax::out(['session' => []]);

        } else {
            if (!isset($pack['session']) || empty(session::autorize($pack['session']))) {
                $this->ajax::error('no autorize', ['session' => []]);
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
