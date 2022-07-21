<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Services;

use phpCAS;

class CasAuthenticationService
{
    private string $env;

    private string $cas_url;
    private string $cas_context;
    private int $cas_port;
    private string $cas_ca_cert;
    private string $cas_login_redirect;
    private string $cas_logout_redirect;
    private string $cas_version;


    public function __construct(array $config, string $env)
    {
        $this->cas_url = $config['cas_url'];
        $this->cas_context = (string) $config['cas_context'];
        $this->cas_port = (int) $config['cas_port'];
        $this->cas_ca_cert = (string) $config['cas_ca_cert'];
        $this->cas_login_redirect =  ltrim($config['cas_login_redirect'], '/\\');
        $this->cas_logout_redirect = $config['cas_logout_redirect'] ?: '';
        $this->cas_version = $config['cas_version'];
        $this->env = $env;
    }

    /**
     * phpCAS Client initialization
     */
    protected function initCas(): void
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

            // Check if a certificate is set up
            if ($this->cas_ca_cert && "" !== $this->cas_ca_cert) {
                phpCAS::setCasServerCACert($this->cas_ca_cert);
            } else {
                phpCAS::setNoCasServerValidation();
            }
        }
    }

    /**
     * Ask phpCAS client for login url with service attribute
     * @return string
     */
    public function getCasLoginUrl(): string
    {
        $this->initCas();
        return phpCAS::getServerLoginURL();
    }

    /**
     * Return url to redirect to after the user logged in
     * @return string
     */
    public function getLoginRedirectUrl(): string
    {
        return $this->cas_login_redirect;
    }

    /**
     * Authenticate the user
     * @return string|null
     */
    public function authenticate(): ?string
    {
        $this->initCas();



        phpCAS::forceAuthentication();

        return phpCAS::getUser() ?: null;
    }

    /**
     * Get additional attributes returned by CAS Server after the user logged in
     * @return array|null
     */
    public function getAttributes(): ?array
    {
       if (phpCAS::isInitialized()) {
           return phpCAS::getAttributes();
       }
       return null;
    }

    /**
     * Return if the user is authenticated or not from CAS
     * @return bool
     */
    public function isAuthenticated(): bool
    {
       return phpCAS::isAuthenticated();
    }

    /**
     * Logout the user
     */
    public function logout(): void
    {
       $this->initCas();
       if ($this->cas_logout_redirect && "" !== $this->cas_logout_redirect) {
           phpCAS::logoutWithRedirectService($this->cas_logout_redirect);
       } else {
           phpCAS::logout();
       }
    }

}
