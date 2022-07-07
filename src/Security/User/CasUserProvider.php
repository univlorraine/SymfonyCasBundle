<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CasUserProvider implements UserProviderInterface
{
    /**
     * @inheritDoc
     */
    public function loadUserByIdentifier($identifier): UserInterface
    {
        if ($identifier && '' !== $identifier)
            return new CasUser($identifier);

        throw new UserNotFoundException(
            sprintf('Username "%s" does not exist.', $identifier)
        );
    }

    /**
     * @deprecated since Symfony 5.3, loadUserByIdentifier() is used instead
     */
    public function loadUserByUsername($username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof CasUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return CasUser::class === $class || is_subclass_of($class, CasUser::class);
    }
}
