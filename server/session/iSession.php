<?php
namespace fmihel\session;

interface iSession{
    public function autorize($params=[]):array;
    public function logout();
    public function enabled():bool;
};