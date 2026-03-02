<?php
namespace fmihel\ajax\plugin\session;

use fmihel\ajax\Plugin;

//require_once __DIR__ . '/iSession.php';
//require_once __DIR__ . '/SessionDefault.php';

class SessionPlugin extends Plugin
{
    const ZERRO_SESSION = '0000-0000-0000';

    private $session;
    private $params = [];

    public function __construct($sessionClass = 'fmihel\ajax\plugin\session\SessionDefault', $params = [])
    {
        $this->session = new $sessionClass();

        $this->params = array_merge([
            'exclude' => [],
        ], $params);
    }

    public function before($pack)
    {
        $to = $pack['to'];

        if ($to === 'session/autorize') {
            $this->ajax::out($this->autorize($this->ajax::$data));
        }

        if ($pack['to'] === 'session/logout') {

            $this->logout();
            $this->ajax::out(['session' => []]);

        } else {
            if (! $this->pathExclude($to)) {
                if (! isset($pack['session']) || empty($session = $this->autorize($pack['session']))) {
                    $this->ajax::error('no autorize', ['session' => []]);
                }
                $pack['session'] = $session;

            } else {
                // добавлена имитация сессии , чтобы не сбрасывалось при ответе
                $pack['session'] = array_merge(['sid' => self::ZERRO_SESSION]);
            }
        }
        return $pack;

    }

    private function pathExclude($path): bool
    {
        $exclude = $this->params['exclude'];
        foreach ($exclude as $ex) {
            if (is_callable($ex)) {
                return ($ex($path) === true);
            } elseif ($path === $ex) {
                return true;
            }
        }

        return false;
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
