# Symfony Cas Bundle

This bundle provides a CAS authentication client for Symfony 5.4+, wrapping the phpCAS library from Apereo (https://www.apereo.org/projects/cas).

## Requirements
* [PHP 7.4](http://php.net/releases/7_4_0.php) or greater
* [Symfony 5.4](https://symfony.com/roadmap/5.4) or greater

## Installation

1. Require the bundle using Composer:

```sh
composer require univlorraine/symfony-cas-bundle
```

2. Declare the bundle in Symfony Kernel if it's not already in (should be added automatically).
In *config/bundles.php*: 
```php
<?php

return [
    ...
    UnivLorraine\Bundle\SymfonyCasBundle\UnivLorraineSymfonyCasBundle::class => ['all' => true],
    ...
];
```

## Configuration

Create the config file *config/packages/univ_lorraine_symfony_cas.yaml*, and add these settings:
```yaml
univ_lorraine_symfony_cas:
  cas_url: my-cas-server-url.domain.tld # required
  cas_context: ~ # optional (eg: /cas)
  cas_port: 443 # optional (default: 443)
  cas_ca_cert: ~ # optional
  cas_login_redirect: / # optional (default: /)
  cas_logout_redirect: ~ # optional (must be a public area)
  cas_version: "3.0" # optional (default: 2.0)
```

* **cas_url**: CAS server url (HTTP(S) scheme is not required).
* **cas_context**: possible additional path to access CAS Server if not root (eg: my-cas.cas.com/cas).
* **cas_port**: server port. If not set, the bundle will use default 443 port.
* **cas_ca_cert**: path to the SSL CA Certificate.
* **cas_login_redirect**: the path the user will be redirected to after he logged in successfully.
  (*It is only triggered when the user goes through the /login url, 
otherwise he will be automatically redirected to the url he requested, after he logged in*).
* **cas_logout_redirect**: the path or url the user will be redirected to after he logged out.
If not set, the user will be redirected to the CAS Server success logout page.
* **cas_version**: the version of the CAS Server used.

## Routes configuration

The bundle provides 2 routes :
* /login
* /logout

For adding these routes to your app, create the file *config/routes/univ_lorraine_symfony_cas.yaml*, and add these settings:
```yaml
_symfony_cas:
  resource: '@UnivLorraineSymfonyCasBundle/Resources/config/routes.xml'
  prefix: /auth-cas
```
Feel free to use a different name as prefix, just remember it for the next security part.

## Security Configuration

Update the security config file *config/packages/security.yaml*.

1. Enable custom authenticator:
```yaml
security:
    enable_authenticator_manager: true
    ...
```

2. Create / update your secure area to use CAS authentication:
```yaml
security:
  ...
  firewalls:
    ...
    secure:
      pattern: ^/secure
      provider: #feel free to use the User provider of your choice
      security: true
      custom_authenticators:
        - univ_lorraine_symfony_cas.authenticator
      ...
    main:
      lazy: true
      ...
```

3. Create public access for the login route (if behind secure area):
```yaml
security:
  ...

  access_control:
    - { path: ^/auth-cas/login$, roles: PUBLIC_ACCESS }
    ...
```
## SSO Logout
This bundle is configured to logout from Symfony AND from the CAS.

## Additional user attributes
The CAS Server can return additional attributes in addition to the main attributes (uid).
When the user is authenticated, it is possible to get these attributes through the security token of Symfony:

```php
use Symfony\Component\Security\Core\Security;

public function myFunction (Security $security) {
    $user_attributes = $security->getToken()->getAttributes();
}
```

## Customize error pages
The bundle triggers an event (named *univ_lorraine_symfony_cas.authentication_failure*) during authentication failure, with 2 different error codes:
* **403 FORBIDDEN** : when the user provider cannot find the authenticated user
* **401 UNAUTHORIZED**: when an error occurs during the CAS authentication

You can use this event to customize the error pages.

1. Create an Event Listener (eg: *src/Event/AuthenticationFailureListener.php*):
```php
<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use UnivLorraine\Bundle\SymfonyCasBundle\Event\CASAuthenticationFailureEvent;

class AuthenticationFailureListener {

    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationFailureResponse(CASAuthenticationFailureEvent $event): void
    {
        switch ($event->getResponse()->getStatusCode()) {
            case Response::HTTP_UNAUTHORIZED:
                $event->setResponse(new RedirectResponse($this->router->generate('auth_fail')));
                break;
            case Response::HTTP_FORBIDDEN:
                $event->setResponse(new RedirectResponse($this->router->generate('access_denied')));
                break;
        }
    }
}
```

2. Register your listener in the services. In *config/services.yaml*:
```yaml
...
services:
  ...

  ## Permet de rediriger les utilisateurs non autorisés vers la page 403
  App\Event\AuthenticationFailureListener:
    tags:
      - { name: kernel.event_listener, event: univ_lorraine_symfony_cas.authentication_failure, method: onAuthenticationFailureResponse }
    arguments: [ '@router' ]

```

## License
See the [LICENSE](LICENSE) file for copyrights and limitations (CeCILL 2.1).
