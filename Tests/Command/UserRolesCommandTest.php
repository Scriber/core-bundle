<?php
namespace Scriber\Bundle\CoreBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Command\UserRolesCommand;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Exception\UserNotFoundException;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserRolesCommandTest extends TestCase
{
    /**
     * @var UserManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    public function setUp()
    {
        $this->manager = $this->createMock(UserManager::class);
    }

    public function tearDown()
    {
        $this->manager = null;
    }

    public function testConfigure()
    {
        $mock = $this
            ->getMockBuilder(UserRolesCommand::class)
            ->setMethods(['setName', 'addArgument', 'addOption'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects(static::once())
            ->method('setName')
            ->with('scriber:user:roles')
            ->willReturn($mock);

        $mock
            ->expects(static::atLeastOnce())
            ->method('addArgument')
            ->withConsecutive(
                ['email', 1]
            )
            ->willReturnSelf();

        $mock
            ->expects(static::exactly(2))
            ->method('addOption')
            ->withConsecutive(
                ['add', 'a', 8 | 4],
                ['remove', 'r', 8 | 4]
            )
            ->willReturnSelf();

        $mock->configure();
    }

    public function testExecute()
    {
        $email = 'test@example.com';
        $currentRoles = [
            'ROLE_TEST',
            'ROLE_TEST_TO_REMOVE'
        ];

        $addRoles = [
            'ROLE_TEST_ADD',
            'ROLE_TEST_ADD_ANOTHER'
        ];

        $removeRoles = [
            'ROLE_TEST_TO_REMOVE'
        ];

        $expectedRoles = [
            'ROLE_TEST',
            'ROLE_TEST_ADD',
            'ROLE_TEST_ADD_ANOTHER'
        ];

        $user = $this->createMock(User::class);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $input
            ->expects(static::once())
            ->method('getArgument')
            ->with('email')
            ->willReturn($email);

        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->with($email)
            ->willReturn($user);

        $user
            ->expects(static::once())
            ->method('getRoles')
            ->willReturn($currentRoles);

        $input
            ->expects(static::exactly(2))
            ->method('getOption')
            ->withConsecutive(['add'], ['remove'])
            ->willReturnOnConsecutiveCalls($addRoles, $removeRoles);

        $this->manager
            ->expects(static::once())
            ->method('updateRoles')
            ->with(
                $user,
                static::callback(function ($v) use ($expectedRoles) {
                    return !array_diff($v, $expectedRoles) && !array_diff($expectedRoles, $v);
                })
            );

        $output
            ->expects(static::once())
            ->method('writeln')
            ->with(static::stringContains('Roles updated'));

        $command = new UserRolesCommand($this->manager);
        $command->execute($input, $output);
    }

    public function testExecuteUserNotFound()
    {
        $this->manager
            ->method('getUser')
            ->willThrowException(new UserNotFoundException());

        $input = $this->createMock(InputInterface::class);
        $input
            ->method('getArgument')
            ->willReturn('test@example.com');

        $output = $this->createMock(OutputInterface::class);

        $this->expectException(UserNotFoundException::class);

        $command = new UserRolesCommand($this->manager);
        $command->execute($input, $output);
    }
}
