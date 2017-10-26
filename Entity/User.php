<?php
namespace Scriber\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $roles;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string|null
     */
    private $resetToken;

    /**
     * @var \DateTime|null
     */
    private $resetTokenTimeout;

    /**
     * @param string $email
     * @param string $name
     */
    public function __construct(string $email, string $name)
    {
        $this->email = $email;
        $this->name = $name;
        $this->password = '';
        $this->roles = [];
        $this->active = true;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * @return \string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param \string[] $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = array_unique($roles);
        sort($this->roles);
    }

    /**
     * @return bool
     */
    public function hasResetToken(): bool
    {
        return is_string($this->resetToken)
               && $this->resetTokenTimeout instanceof \DateTime
               && $this->resetTokenTimeout > new \DateTime();
    }

    /**
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    /**
     * @return \DateTime
     */
    public function getResetTokenTimeout(): \DateTime
    {
        return $this->resetTokenTimeout;
    }

    /**
     * @param string $resetToken
     * @param \DateTime $timeout
     */
    public function setResetToken(string $resetToken, \DateTime $timeout)
    {
        $this->resetToken = $resetToken;
        $this->resetTokenTimeout = $timeout;
    }

    public function clearResetToken(): void
    {
        $this->resetToken = null;
        $this->resetTokenTimeout = null;
    }
}
