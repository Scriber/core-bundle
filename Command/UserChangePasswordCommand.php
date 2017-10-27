<?php
namespace Scriber\Bundle\CoreBundle\Command;

use Rzeka\DataHandler\DataHandler;
use Scriber\Bundle\CoreBundle\User\Data\ChangePasswordData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserChangePasswordCommand extends Command
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
            ->setName('scriber:user:change-password')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $user = $this->manager->getUser($email);

        $requestData = [
            'password' => $input->getArgument('password')
        ];

        $data = new ChangePasswordData();
        $result = $this->dataHandler->handle($requestData, $data, [
            'validation_groups' => ['manual']
        ]);

        if ($result->isValid()) {
            $this->manager->updatePassword($user, $data->password);

            $output->writeln('<info>Password updated</info>');
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
