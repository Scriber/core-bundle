<?php
namespace Scriber\Bundle\CoreBundle\Security;

use Scriber\Bundle\CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class SecurityUser implements AdvancedUserInterface, \Serializable, EncoderAwareInterface
{
    const ENCODER = 'scriber';

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array|\string[]
     */
    private $roles;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->username = $user->getEmail();
        $this->password = $user->getPassword();
        $this->enabled = $user->isActive();
        $this->roles = $user->getRoles();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->getUsername();
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): void
    {
    }

    public function eraseCredentials(): void
    {
    }

    public function isAccountNonExpired(): bool
    {
        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return (bool) $this->enabled;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return serialize([
            $this->username,
            $this->password,
            $this->roles,
            $this->enabled
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [
            $this->username,
            $this->password,
            $this->roles,
            $this->enabled
        ] = unserialize($serialized, [
            'allowed_classes' => [self::class]
        ]);
    }

    /**
     * @return string
     */
    public function getEncoderName(): string
    {
        return self::ENCODER;
    }
}
