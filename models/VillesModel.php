<?php

namespace models;

use models\base\SQL;

class VillesModel extends SQL
{
    public function __construct()
    {
        parent::__construct('exemplaire', 'villes');
    }
    
}
