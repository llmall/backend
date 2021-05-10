<?php

namespace app\Controller\v1;

use App\Controller\AbstractController;

class UserController extends AbstractController
{

    /**
     * @return string
     */
    public function index()
    {
        $user = $this->request->getAttribute('User');
        return 'hello '.$user->username ;
    }

}