<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UnivLorraine\Bundle\SymfonyCasBundle\Services\CasAuthenticationService;


class AuthenticationController extends AbstractController
{
    public function login(Request $request, CasAuthenticationService $casService): RedirectResponse
    {
        $login_url = $casService->getCasLoginUrl();

        $login_url = $this->removePathServiceFromLoginUrl($login_url) .
            urlencode($request->getSchemeAndHttpHost() . '/' . $casService->getLoginRedirectUrl());

        return new RedirectResponse('/' . $casService->getLoginRedirectUrl());
    }

    public function logout(Request $request): Response
    {
        dd('in logout function');
        // logout from cas
    }

    private function removePathServiceFromLoginUrl($url): string
    {
        return substr($url, 0, (int) strpos($url, 'service=') + strlen('service='));
    }
}
