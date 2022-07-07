<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\EventDispatcher\Event;

class CasAuthenticationFailureEvent extends Event
{
    const CAS_AUTH_EXCEPTION = 'univ_lorraine_symfony_cas.authentication_failure';

    private Request $request;
    private AuthenticationException $exception;
    private Response $response;

    public function __construct(Request $request, AuthenticationException $exception, Response $response) {
        $this->request = $request;
        $this->exception = $exception;
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response {
        return $this->response;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request {
        return $this->request;
    }

    /**
     * @return AuthenticationException
     */
    public function getException(): AuthenticationException {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getExceptionType(): string {
        return get_class($this->exception);
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void {
        $this->response = $response;
    }

}
