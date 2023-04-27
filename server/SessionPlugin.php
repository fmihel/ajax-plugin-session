<?php
namespace fmihel\ajax\plugin\session;

use fmihel\ajax\Plugin;

//require_once __DIR__ . '/iSession.php';
//require_once __DIR__ . '/SessionDefault.php';

class SessionPlugin extends Plugin
{

    private $session;

    public function __construct($sessionClass = 'fmihel\ajax\plugin\session\SessionDefault')
    {
        $this->session = new $sessionClass();
    }

    public function before($pack)
    {
        $to = $pack['to'];

        if ($to === 'session/autorize') {

            $this->ajax::out($this->autorize($this->ajax::$data));

        }if ($pack['to'] === 'session/logout') {

            $this->logout();
            $this->ajax::out(['session' => []]);

        } else {
            if (!isset($pack['session']) || empty($this->autorize($pack['session']))) {
                $this->ajax::error('no autorize', ['session' => []]);
            }
        }
        return $pack;

    }

    public function autorize($param = [])
    {
        return $this->session->autorize($param);
    }

    public function logout()
    {
        return $this->session->logout();
    }

    public function enabled()
    {
        return $this->session->enabled();
    }

}
