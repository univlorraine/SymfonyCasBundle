<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout(Request $request)
    {
        dd('coucou');
    }
}
