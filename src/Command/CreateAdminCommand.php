<?php

namespace App\Command;

use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        ValidatorInterface $validator,
        TokenGenerator $tokenGenerator,
        ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        parent::__construct();

        $this->validator = $validator;
        $this->tokenGenerator = $tokenGenerator;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new user, with ADMIN role')
            ->setHelp('This command allows you to create a user, password is random generated, the role is ROLE_ADMIN')
            ->addArgument('email', InputArgument::REQUIRED, 'A valid email address - this is also the username')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $inputs = ['email' => $email];

        $constraints = new Constraints\Collection([
            'email' => [new Email(), new NotBlank()],
        ]);

        $violations = $this->validator->validate($inputs, $constraints);

        if(count($violations) > 0) {
            $io->error("$email doesn't look like a valid email. Please use a valid email address!");
        } else {
            $user = new User();
            $user->setEmail($email);
            $user->setRoles([User::ROLE_ADMIN]);
            $user->setEnabled(true);
            $generatedPlainTextPassword = $this->tokenGenerator->getRandomSecureToken(8);
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $generatedPlainTextPassword);
            $user->setPassword($encodedPassword);

            $this->manager->persist($user);
            $this->manager->flush();

            $io->success("User with email $email has been created, password is: $generatedPlainTextPassword");
        }
    }
}
