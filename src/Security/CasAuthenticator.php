<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Security;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use UnivLorraine\Bundle\SymfonyCasBundle\Event\CasAuthenticationFailureEvent;
use UnivLorraine\Bundle\SymfonyCasBundle\Services\CasAuthenticationService;

class CasAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private CasAuthenticationService $casService;
    private EventDispatcherInterface $eventDispatcher;
    private Security $security;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        CasAuthenticationService $casService,
        Security $security
    ) {
        $this->casService = $casService;
        $this->security = $security;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        // Get the complete CAS login URL from the phpCAS lib
        $login_url = $this->casService->getCasLoginUrl();

        // Start authentication. Redirect to CAS Login page
        return new RedirectResponse($login_url);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): ?bool
    {
        // If user already connected, skip the CAS auth
        return !$this->security->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Request $request): Passport
    {
        try {
            $username = $this->casService->authenticate();
            return new SelfValidatingPassport(new UserBadge((string) $username));
        } catch (\Exception $e) {
            throw new AuthenticationException('Cannot contact CAS Server');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($this->casService->isAuthenticated()) {
            $token->setAttributes($this->casService->getAttributes());
        }

        // On success, let the request continue
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $errorMessage = strtr($exception->getMessageKey(), $exception->getMessageData());

        if ($exception instanceof BadCredentialsException) {
            $response = new Response($errorMessage, Response::HTTP_FORBIDDEN);
        } else {
            $response = new Response($errorMessage, Response::HTTP_UNAUTHORIZED);
        }

        $event = new CasAuthenticationFailureEvent($request, $exception, $response);
        $this->eventDispatcher->dispatch($event, CasAuthenticationFailureEvent::CAS_AUTH_EXCEPTION);

        return $event->getResponse();
    }
}
