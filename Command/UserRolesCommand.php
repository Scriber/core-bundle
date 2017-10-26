<?php
namespace Scriber\Bundle\CoreBundle\Command;

use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserRolesCommand extends Command
{
    /**
     * @var UserManager
     */
    private $manager;

    /**
     * @param UserManager $manager
     */
    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('scriber:user:roles')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addOption('add', 'a', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL)
            ->addOption('remove', 'r', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $user = $this->manager->getUser($email);

        $roles = $user->getRoles();

        foreach ($input->getOption('add') as $role) {
            if (!in_array($role, $roles, true)) {
                $roles[] = $role;
            }
        }

        foreach ($input->getOption('remove') as $role) {
            $key = array_search($role, $roles, true);

            if ($key !== false) {
                unset($roles[$key]);
            }
        }

        $this->manager->updateRoles($user, $roles);

        $output->writeln('<info>Roles updated</info>');
    }
}
