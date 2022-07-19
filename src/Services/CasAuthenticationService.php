<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Services;

use phpCAS;

class CasAuthenticationService
{
    private string $env;

    private string $cas_url;
    private string $cas_context;
    private int $cas_port;
    private string $cas_logout_path;
    private string $cas_logout_redirect;
    private string $cas_version;


    public function __construct(array $config, string $env)
    {
        $this->cas_url = $config['cas_url'];
        $this->cas_context = (string) $config['cas_context'];
        $this->cas_port = (int) $config['cas_port'];
        $this->cas_logout_path = ltrim($config['cas_logout_path'], '/\\');
        $this->cas_logout_redirect = $config['cas_logout_redirect'] ?: '';
        $this->cas_version = $config['cas_version'];
        $this->env = $env;
    }

    protected function initCas()
    {
        // Initialize phpCAS if not
        if (!phpCAS::isInitialized()) {
            // Enable debug in dev environment
            if ('dev' === $this->env) {
                phpCAS::setLogger();
                phpCAS::setVerbose(true);
            }

            phpCAS::client(
                $this->cas_version,
                $this->cas_url,
                $this->cas_port,
                $this->cas_context,
                true
            );

            // disable SSL validation of the CAS Server (not recommended in prod)
            phpCAS::setNoCasServerValidation();
        }
    }

    public function getCasLoginUrl(): string
    {
        $this->initCas();
        return phpCAS::getServerLoginURL();
    }

    public function authenticate(): ?string
    {
        $this->initCas();
        phpCAS::forceAuthentication();

        return phpCAS::getUser() ?: null;
    }

   public function getAttributes(): ?array
   {
       if (phpCAS::isInitialized()) {
           return phpCAS::getAttributes();
       }
       return null;
   }

   public function isAuthenticated(): bool
   {
       return phpCAS::isAuthenticated();
   }

}
