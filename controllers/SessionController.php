<?php

namespace Controllers;

use Core\db\Generate;

/**
 * Author:QiLin
 * Class SessionController
 * @package Controllers
 */
class SessionController extends BaseController
{
    public function __construct()
    {

    }

    public function login()
    {
        $generate = new Generate([
            'Adapter' => 'Pdo'
        ]);
        $generate->create('agent');
    }
}