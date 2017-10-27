<?php
namespace Scriber\Bundle\CoreBundle\Command;

use Rzeka\DataHandler\DataHandler;
use Scriber\Bundle\CoreBundle\Data\UserData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserAddCommand extends Command
{
    use DataHandlerValidationFormatterTrait;

    /**
     * @var UserManager
     */
    private $manager;

    /**
     * @var DataHandler
     */
    private $dataHandler;

    /**
     * @param UserManager $manager
     * @param DataHandler $dataHandler
     */
    public function __construct(UserManager $manager, DataHandler $dataHandler)
    {
        $this->manager = $manager;
        $this->dataHandler = $dataHandler;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('scriber:user:add')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $validationGroups = ['create'];
        $requestData = [
            'email' => $input->getArgument('email'),
            'name' => $input->getArgument('name')
        ];

        $data = new UserData();

        $result = $this->dataHandler->handle($requestData, $data, ['validation_groups' => $validationGroups]);
        if ($result->isValid()) {
            $user = $this->manager->createUser($data);
            $output->writeln('<info>User created</info>');
        } else {
            $output->writeln(
                $this->getHelper('formatter')->formatBlock(
                    $this->getValidationErrors($result->getErrors()),
                    'error'
                )
            );

            return 1;
        }
    }
}
