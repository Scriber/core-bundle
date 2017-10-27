<?php
namespace Scriber\Bundle\CoreBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Rzeka\DataHandler\DataHandler;
use Rzeka\DataHandler\DataHandlerResult;
use Scriber\Bundle\CoreBundle\Command\UserChangePasswordCommand;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Exception\UserNotFoundException;
use Scriber\Bundle\CoreBundle\User\Data\ChangePasswordData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserChangePasswordCommandTest extends TestCase
{
    /**
     * @var UserManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    /**
     * @var DataHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataHandler;

    public function setUp()
    {
        $this->manager = $this->createMock(UserManager::class);
        $this->dataHandler = $this->createMock(DataHandler::class);
    }

    public function tearDown()
    {
        $this->manager = null;
        $this->dataHandler = null;
    }

    public function testConfigure()
    {
        $mock = $this
            ->getMockBuilder(UserChangePasswordCommand::class)
            ->setMethods(['setName', 'addArgument'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects(static::once())
            ->method('setName')
            ->with('scriber:user:change-password')
            ->willReturn($mock);

        $mock
            ->expects(static::atLeastOnce())
            ->method('addArgument')
            ->withConsecutive(
                ['email', 1],
                ['password', 1]
            )
            ->willReturnSelf();

        $mock->configure();
    }

    public function testExecute()
    {
        $password = 'secret';
        $email = 'text@example.com';

        $data = [
            'password' => $password
        ];

        $input = $this->createMock(InputInterface::class);
        $input
            ->expects(static::exactly(2))
            ->method('getArgument')
            ->withConsecutive(['email'], ['password'])
            ->willReturnOnConsecutiveCalls($email, $password);

        $output = $this->createMock(OutputInterface::class);
        $dataResult = $this->createMock(DataHandlerResult::class);
        $user = $this->createMock(User::class);

        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->with($email)
            ->willReturn($user);

        $this->dataHandler
            ->expects(static::once())
            ->method('handle')
            ->with(
                $data,
                static::isInstanceOf(ChangePasswordData::class),
                ['validation_groups' => ['manual']]
            )
            ->willReturnCallback(function ($requestData, $data) use ($password, $dataResult) {
                $data->password = $password;

                return $dataResult;
            });

        $dataResult
            ->expects(static::once())
            ->method('isValid')
            ->willReturn(true);

        $this->manager
            ->expects(static::once())
            ->method('updatePassword')
            ->with($user, $password);

        $output
            ->expects(static::once())
            ->method('writeln')
            ->with(static::stringContains('Password updated'));

        $command = new UserChangePasswordCommand($this->manager, $this->dataHandler);
        $command->execute($input, $output);
    }

    public function testExecuteWithError()
    {
        $password = 'secret';
        $email = 'text@example.com';

        $input = $this->createMock(InputInterface::class);
        $input
            ->expects(static::exactly(2))
            ->method('getArgument')
            ->withConsecutive(['email'], ['password'])
            ->willReturnOnConsecutiveCalls($email, $password);

        $output = $this->createMock(OutputInterface::class);
        $dataResult = $this->createMock(DataHandlerResult::class);
        $user = $this->createMock(User::class);

        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->with($email)
            ->willReturn($user);

        $this->dataHandler
            ->method('handle')
            ->willReturn($dataResult);

        $dataResult
            ->expects(static::once())
            ->method('isValid')
            ->willReturn(false);

        $output
            ->expects(static::once())
            ->method('writeln')
            ->with(static::stringContains('[Validation error]'));

        $mock = $this
            ->getMockBuilder(UserChangePasswordCommand::class)
            ->setMethods(['getHelper', 'getValidationErrors'])
            ->setConstructorArgs([$this->manager, $this->dataHandler])
            ->getMock();

        $mock
            ->method('getHelper')
            ->with('formatter')
            ->willReturn(new FormatterHelper());

        $mock
            ->method('getValidationErrors')
            ->willReturn(['[Validation error]']);

        $result = $mock->execute($input, $output);

        static::assertEquals(1, $result);
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

        $command = new UserChangePasswordCommand($this->manager, $this->dataHandler);
        $command->execute($input, $output);
    }
}
