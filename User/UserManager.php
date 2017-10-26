<?php
namespace Scriber\Bundle\CoreBundle\User;

use Doctrine\ORM\EntityManagerInterface;
use Scriber\Bundle\CoreBundle\Data\UserData;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param EntityManagerInterface $em
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EntityManagerInterface $em, EncoderFactoryInterface $encoderFactory)
    {
        $this->em = $em;
        $this->passwordEncoder = $encoderFactory->getEncoder('scriber_core.admin');
    }

    /**
     * @param string $email
     *
     * @return User|null
     * @throws UserNotFoundException
     */
    public function getUser(string $email): User
    {
        $user = $this->em->getRepository(User::class)
            ->findOneBy([
                'email' => $email
            ]);

        if (!$user) {
            throw new UserNotFoundException(
                sprintf(
                    'Could not find user with e-mail %s',
                    $email
                )
            );
        }

        return $user;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function userExists(string $email): bool
    {
        try {
            $this->getUser($email);
        } catch (UserNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->setPassword(
            $this->passwordEncoder->encodePassword($password, '')
        );

        $this->em->flush();
    }

    /**
     * @param string $encodedPassword
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword($encodedPassword, $password): bool
    {
        return $this->passwordEncoder->isPasswordValid($encodedPassword, $password, '');
    }

    /**
     * @param UserData $data
     *
     * @return User
     */
    public function createUser(UserData $data): User
    {
        $user = new User($data->email, $data->name);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param User $user
     * @param string[] $roles
     */
    public function updateRoles(User $user, array $roles): void
    {
        $user->setRoles($roles);
        $this->em->flush();
    }

    /**
     * \EntityManagerInterface::flush forwarder
     */
    public function save(): void
    {
        $this->em->flush();
    }
}
