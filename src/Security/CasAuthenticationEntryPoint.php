<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class CasAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    protected string $cas_login_url;
    protected string $cas_service_parameter;

    public function __construct($config)
    {
        $this->cas_login_url = $config['cas_login_url'];
        $this->cas_service_parameter = $config['cas_service_parameter'];
    }

    public function start(Request $request, AuthenticationException $authException = null) : RedirectResponse
    {
        return new RedirectResponse($this->cas_login_url.'?'.$this->cas_service_parameter.'='.urlencode($request->getUri()));
    }
}
