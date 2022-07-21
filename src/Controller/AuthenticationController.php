<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use UnivLorraine\Bundle\SymfonyCasBundle\Services\CasAuthenticationService;

class AuthenticationController extends AbstractController
{
    public function login(Request $request, CasAuthenticationService $casService): RedirectResponse
    {
        $login_url = $casService->getCasLoginUrl();

        $login_url = $this->removePathServiceFromLoginUrl($login_url, 'service') .
            urlencode($request->getSchemeAndHttpHost() . '/' . $casService->getLoginRedirectUrl());

        return new RedirectResponse($login_url);
    }

    public function logout(Request $request, CasAuthenticationService $casService): void
    {
        $casService->logout();
    }

    private function removePathServiceFromLoginUrl($url, $service_name): string
    {
        return substr($url, 0, (int) strpos($url, $service_name . '=') + strlen($service_name . '='));
    }
}
