<?php
namespace Scriber\Bundle\CoreBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Rzeka\DataHandler\DataHandler;
use Rzeka\DataHandler\DataHandlerResult;
use Scriber\Bundle\CoreBundle\Command\UserAddCommand;
use Scriber\Bundle\CoreBundle\Data\UserData;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserAddCommandTest extends TestCase
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
            ->getMockBuilder(UserAddCommand::class)
            ->setMethods(['setName', 'addArgument', 'addOption'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects(static::once())
            ->method('setName')
            ->with('scriber:user:add')
            ->willReturn($mock);

        $mock
            ->expects(static::atLeastOnce())
            ->method('addArgument')
            ->withConsecutive(
                ['email', 1],
                ['name', 1]
            )
            ->willReturnSelf();

        $mock
            ->expects(static::once())
            ->method('addOption')
            ->with('password', null, 4)
            ->willReturnSelf();

        $mock->configure();
    }

    public function testExecute()
    {
        $email = 'test@example.com';
        $name = 'John Doe';

        $data = [
            'email' => $email,
            'name' => $name
        ];

        $input = $this->createMock(InputInterface::class);
        $input
            ->expects(static::exactly(2))
            ->method('getArgument')
            ->withConsecutive(['email'], ['name'])
            ->willReturnOnConsecutiveCalls($email, $name);

        $output = $this->createMock(OutputInterface::class);
        $dataResult = $this->createMock(DataHandlerResult::class);

        $this->dataHandler
            ->expects(static::once())
            ->method('handle')
            ->with(
                $data,
                static::isInstanceOf(UserData::class),
                ['validation_groups' => ['create']]
            )
            ->willReturnCallback(function ($requestData, $data) use ($dataResult) {
                $data->email = $requestData['email'];
                $data->name = $requestData['name'];

                return $dataResult;
            });

        $dataResult->expects(static::once())
                   ->method('isValid')
                   ->willReturn(true);

        $this->manager
            ->expects(static::once())
            ->method('createUser')
            ->with(static::callback(function ($user) use ($email, $name) {
                return $user instanceof UserData &&
                       $user->email === $email &&
                       $user->name === $name;
            }));

        $output
            ->expects(static::once())
            ->method('writeln')
            ->with(static::stringContains('User created'));

        $command = new UserAddCommand($this->manager, $this->dataHandler);
        $command->execute($input, $output);
    }

    public function testExecuteWithError()
    {
        $email = 'test@example.com';
        $name = 'John Doe';

        $input = $this->createMock(InputInterface::class);
        $input
            ->method('getArgument')
            ->willReturnOnConsecutiveCalls($email, $name);

        $output = $this->createMock(OutputInterface::class);
        $dataResult = $this->createMock(DataHandlerResult::class);

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
            ->getMockBuilder(UserAddCommand::class)
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

    public function testExecuteWithPassword()
    {
        $email = 'test@example.com';
        $name = 'John Doe';
        $password = 'test';

        $data = [
            'email' => $email,
            'name' => $name,
            'password' => $password
        ];

        $input = $this->createMock(InputInterface::class);
        $input
            ->method('getArgument')
            ->willReturnOnConsecutiveCalls($email, $name);

        $input
            ->expects(static::once())
            ->method('getOption')
            ->with('password')
            ->willReturn($password);

        $output = $this->createMock(OutputInterface::class);
        $dataResult = $this->createMock(DataHandlerResult::class);
        $user = $this->createMock(User::class);

        $this->dataHandler
            ->expects(static::once())
            ->method('handle')
            ->with(
                $data,
                static::isInstanceOf(UserData::class),
                ['validation_groups' => ['create', 'password']]
            )
            ->willReturnCallback(function ($requestData, $data) use ($dataResult) {
                $data->email = $requestData['email'];
                $data->name = $requestData['name'];
                $data->password = $requestData['password'];

                return $dataResult;
            });

        $dataResult->expects(static::once())
                   ->method('isValid')
                   ->willReturn(true);

        $this->manager
            ->method('createUser')
            ->willReturn($user);

        $this->manager
            ->expects(static::once())
            ->method('updatePassword')
            ->with($user, $password);

        $output
            ->expects(static::once())
            ->method('writeln')
            ->with(static::stringContains('User created'));

        $command = new UserAddCommand($this->manager, $this->dataHandler);
        $command->execute($input, $output);
    }
}
