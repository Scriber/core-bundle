<?php
namespace Scriber\Bundle\CoreBundle\User;

use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\Exception\NoResultException;
use Happyr\DoctrineSpecification\Filter\Equals;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User as Event;
use Scriber\Bundle\CoreBundle\Exception\UserNotFoundException;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\Data\CreateData;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface $em
     * @param EncoderFactoryInterface $encoderFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $em, EncoderFactoryInterface $encoderFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->passwordEncoder = $encoderFactory->getEncoder(SecurityUser::ENCODER);
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $email
     *
     * @return User
     * @throws \Scriber\Bundle\CoreBundle\Exception\UserNotFoundException
     */
    public function getUser(string $email): User
    {
        $event = new Event\UserGetBeforeEvent($email);
        $this->eventDispatcher->dispatch(Event\UserGetBeforeEvent::class, $event);

        try {
            $user = $this->em
                ->getRepository(User::class)
                ->matchSingleResult(
                    new Equals('email', $event->getEmail())
                );

            $this->eventDispatcher->dispatch(Event\UserGetAfterEvent::class, new Event\UserGetAfterEvent($user));

            return $user;
        } catch (NoResultException $e) {
            throw new UserNotFoundException(
                sprintf(
                    'Could not find user with e-mail %s',
                    $email
                )
            );
        }
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
        $this->eventDispatcher->dispatch(
            Event\UserChangePasswordBeforeEvent::class,
            new Event\UserChangePasswordBeforeEvent($user, $password)
        );

        $user->setPassword(
            $this->passwordEncoder->encodePassword($password, '')
        );

        $this->em->flush();

        $this->eventDispatcher->dispatch(
            Event\UserChangePasswordAfterEvent::class,
            new Event\UserChangePasswordAfterEvent($user, $password)
        );
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
     * @param CreateData $data
     *
     * @return User
     */
    public function createUser(CreateData $data): User
    {
        $this->eventDispatcher->dispatch(
            Event\UserCreateBeforeEvent::class,
            new Event\UserCreateBeforeEvent($data)
        );

        $user = new User($data->email, $data->name);

        $this->em->persist($user);
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            Event\UserCreateAfterEvent::class,
            new Event\UserCreateAfterEvent($user, $data)
        );

        return $user;
    }

    /**
     * @param UpdateData $data
     */
    public function updateUser(UpdateData $data): void
    {
        $user = $data->getUser();

        $this->eventDispatcher->dispatch(
            Event\UserUpdateBeforeEvent::class,
            new Event\UserUpdateBeforeEvent($user, $data)
        );

        $user->setEmail($data->email);
        $user->setName($data->name);

        $this->em->flush();

        $this->eventDispatcher->dispatch(
            Event\UserUpdateAfterEvent::class,
            new Event\UserUpdateAfterEvent($user, $data)
        );
    }

    /**
     * @param User $user
     * @param string[] $roles
     */
    public function updateRoles(User $user, array $roles): void
    {
        $event = new Event\UserUpdateRolesBeforeEvent($user, $roles);

        $this->eventDispatcher->dispatch(
            Event\UserUpdateRolesBeforeEvent::class,
            $event
        );

        $user->setRoles($event->getRoles());
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            Event\UserUpdateRolesAfterEvent::class,
            new Event\UserUpdateRolesAfterEvent($user, $roles)
        );
    }
}
