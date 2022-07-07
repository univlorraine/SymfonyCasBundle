<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

class CasUser implements UserInterface
{
    private string $username;
    private array $roles = [];

    public function __construct($username) {
        $this->username = $username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
}
