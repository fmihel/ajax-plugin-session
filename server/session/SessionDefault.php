<?php
namespace fmihel\Ajax\Session;

class SessionDefault implements iSession
{
    public $current = [/*'login'=>false,'id'=>false*/];

    protected $enable = false;
    protected $users = [
        ['id' => '84893', 'login' => 'admin', 'pass' => 'xxx', 'sid' => '3992'],
    ];

    public function autorize($params = []): array
    {

        if (isset($params['sid'])) {
            $user = self::findUser(['sid' => $params['sid']]);
            if (!empty($user)) {
                $this->enable = true;
                $this->current = $user;
                return $this->current;
            }
        } elseif (isset($params['login']) && isset($params['pass'])) {

            $user = self::findUser(['login' => $params['login'], 'pass' => $params['pass']]);
            if (!empty($user)) {
                $this->enable = true;
                $this->current = $user;
                return $this->current;
            };
        }
        return [];
    }
    public function logout()
    {
        $this->enable = false;
        $this->current = [];
    }
    public function enabled(): bool
    {
        return $this->enable;
    }

    protected function findUser(array $FieldValue): array
    {
        if (empty($FieldValue)) {
            throw new \Exception('FieldValue is empty');
        }

        foreach ($this->users as $user) {

            $find = true;
            foreach ($FieldValue as $field => $value) {
                $find = $user[$field] == $value;
                if (!$find) {
                    break;
                }

            };

            if ($find) {
                return $user;
            }

        };
        return [];
    }

}
