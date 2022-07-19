<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationController extends AbstractController
{
    public function logout(Request $request)
    {
        dd('in logout controller');
        // logout from cas
    }
}
