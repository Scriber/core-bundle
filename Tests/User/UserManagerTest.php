<?php
namespace Scriber\Bundle\CoreBundle\Tests\User;

use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;
use Happyr\DoctrineSpecification\Exception\NoResultException;
use Happyr\DoctrineSpecification\Filter\Equals;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Exception\UserNotFoundException;
use Scriber\Bundle\CoreBundle\User\Data\CreateData;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserManagerTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var EncoderFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $encoderFactory;

    /**
     * @var PasswordEncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $passwordEncoder;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventDispatcher;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->encoderFactory = $this->createMock(EncoderFactoryInterface::class);
        $this->passwordEncoder = $this->createMock(PasswordEncoderInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->encoderFactory
            ->method('getEncoder')
            ->willReturn($this->passwordEncoder);
    }

    public function tearDown()
    {
        $this->em = null;
        $this->encoderFactory = null;
        $this->passwordEncoder = null;
    }

    public function testGetUser()
    {
        $user = $this->createMock(User::class);
        $email = 'test@example.com';

        $repository = $this->createMock(EntitySpecificationRepository::class);
        $repository
            ->expects(static::once())
            ->method('matchSingleResult')
            ->with(static::isInstanceOf(Equals::class), null)
            ->willReturn($user);

        $this->em
            ->expects(static::once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository);

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher, $this->eventDispatcher);
        $result = $manager->getUser($email);

        static::assertEquals($user, $result);
    }

    public function testGetNonExistentUser()
    {
        $email = 'test@example.com';

        $repository = $this->createMock(EntitySpecificationRepository::class);
        $repository
            ->expects(static::once())
            ->method('matchSingleResult')
            ->willThrowException(new NoResultException());

        $this->em
            ->expects(static::once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository);

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher);

        $this->expectException(UserNotFoundException::class);
        $manager->getUser($email);
    }

    public function testUserExistsUserFound()
    {
        $email = 'test@example.com';

        $user = $this->createMock(User::class);

        $repository = $this->createMock(EntitySpecificationRepository::class);
        $repository
            ->expects(static::once())
            ->method('matchSingleResult')
            ->with(static::isInstanceOf(Equals::class), null)
            ->willReturn($user);

        $this->em
            ->expects(static::once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository);

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher);
        $result = $manager->userExists($email);

        static::assertTrue($result);
    }

    public function testUserExistsUserNotFound()
    {
        $email = 'test@example.com';

        $repository = $this->createMock(EntitySpecificationRepository::class);
        $repository
            ->expects(static::once())
            ->method('matchSingleResult')
            ->willThrowException(new NoResultException());

        $this->em
            ->expects(static::once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository);

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher);
        $result = $manager->userExists($email);

        static::assertFalse($result);
    }

    public function testUpdatePassword()
    {
        $password = 'secret';
        $encodedPassword = 'password';

        $this->passwordEncoder
            ->expects(static::once())
            ->method('encodePassword')
            ->with($password, '')
            ->willReturn($encodedPassword);

        $user = $this->createMock(User::class);
        $user
            ->expects(static::once())
            ->method('setPassword')
            ->with($encodedPassword);

        $this->em
            ->expects(static::once())
            ->method('flush');

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher);
        $manager->updatePassword($user, $password);
    }

    public function testPasswordValidation()
    {
        $password = 'secret';
        $encodedPassword = 'password';

        $this->passwordEncoder
            ->expects(static::once())
            ->method('isPasswordValid')
            ->with($encodedPassword, $password)
            ->willReturn(true);

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher);
        $result = $manager->checkPassword($encodedPassword, $password);

        static::assertTrue($result);
    }

    public function testCreateUser()
    {
        $email = 'test@example.com';
        $name = 'John Doe';

        $data = new CreateData();
        $data->email = $email;
        $data->name = $name;

        $this->em
            ->expects(static::once())
            ->method('persist')
            ->with(static::callback(function ($v) use ($data) {
                return $v instanceof User &&
                       $v->getEmail() === $data->email &&
                       $v->getName() === $data->name;
            }));

        $this->em
            ->expects(static::once())
            ->method('flush');

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher);
        $user = $manager->createUser($data);

        static::assertInstanceOf(User::class, $user);
        static::assertEquals($user->getEmail(), $email);
        static::assertEquals($user->getName(), $name);
    }

    public function testUpdateRoles()
    {
        $user = $this->createMock(User::class);
        $roles = ['ROLE_TEST'];

        $user
            ->expects(static::once())
            ->method('setRoles')
            ->with($roles);

        $this->em
            ->expects(static::once())
            ->method('flush');

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher);
        $manager->updateRoles($user, $roles);
    }

    public function testUpdateUser()
    {
        $email = 'test@example.com';
        $name = 'John Doe';

        $user = $this->createMock(User::class);

        $data = new UpdateData($user);
        $data->email = $email;
        $data->name = $name;

        $user
            ->expects(static::once())
            ->method('setName')
            ->with($name);

        $user
            ->expects(static::once())
            ->method('setEmail')
            ->with($email);

        $this->em
            ->expects(static::once())
            ->method('flush');

        $manager = new UserManager($this->em, $this->encoderFactory, $this->eventDispatcher);
        $manager->updateUser($data);
    }
}
