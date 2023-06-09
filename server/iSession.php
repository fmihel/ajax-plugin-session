<?php
namespace fmihel\ajax\plugin\session;

interface iSession
{
    public function autorize($params = []): array;
    public function logout();
    public function enabled(): bool;
};
