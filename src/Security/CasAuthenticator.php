<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Security;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use UnivLorraine\Bundle\SymfonyCasBundle\Event\CasAuthenticationFailureEvent;

class CasAuthenticator extends AbstractAuthenticator
{
    protected string $cas_service_validate_url;
    protected string $cas_ticket_parameter;
    protected string $cas_service_parameter;
    protected string $xml_cas_namespace;
    protected string $xml_username_attribute;

    private HttpClientInterface $httpClient;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct($config, HttpClientInterface $httpClient, EventDispatcherInterface $eventDispatcher) {
        $this->cas_service_validate_url = $config['cas_service_validate_url'];
        $this->cas_service_parameter = $config['cas_service_parameter'];
        $this->cas_ticket_parameter = $config['cas_ticket_parameter'];
        $this->xml_cas_namespace = $config['xml_cas_namespace'];
        $this->xml_username_attribute = $config['xml_username_attribute'];

        $this->httpClient = $httpClient;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): ?bool
    {
        return $request->query->has($this->cas_ticket_parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Request $request): Passport
    {
        // Request CAS Server
        $response = $this->httpClient->request('GET', $this->cas_service_validate_url, [
            'query' => [
                $this->cas_ticket_parameter => $request->get($this->cas_ticket_parameter),
                $this->cas_service_parameter => $this->cleanTicketInUri($request),
            ],
        ]);

        // Parse response content in xml object
        $xml = new \SimpleXMLElement($response->getContent(), 0, false, $this->xml_cas_namespace, true);

        if ($response->getStatusCode() === Response::HTTP_OK && isset($xml->authenticationSuccess)) {
            return new SelfValidatingPassport(
                new UserBadge((string) $xml->authenticationSuccess->user),
                []
            );
        }

        throw new AuthenticationException('CAS Authentication fail !');
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->cleanTicketInUri($request));
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $errorMessage = strtr($exception->getMessageKey(), $exception->getMessageData());

        if ($exception instanceof BadCredentialsException) {
            $response = new JsonResponse($errorMessage, Response::HTTP_FORBIDDEN);
        } else {
            $response = new JsonResponse($errorMessage, Response::HTTP_UNAUTHORIZED);
        }

        $event = new CasAuthenticationFailureEvent($request, $exception, $response);
        $this->eventDispatcher->dispatch($event, CasAuthenticationFailureEvent::CAS_AUTH_EXCEPTION);

        return $event->getResponse();
    }

    private function cleanTicketInUri(Request $request) : string
    {
        if ($request->query->has($this->cas_ticket_parameter)) {
            $request->query->remove($this->cas_ticket_parameter);
            $request->overrideGlobals(); // Require to generate new uri without 'ticket' parameter
        }
        return $request->getUri();
    }


}
