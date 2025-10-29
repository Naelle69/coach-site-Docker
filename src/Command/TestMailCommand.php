<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(name: 'app:test:mail')]
class TestMailCommand extends Command
{
    public function __construct(private MailerInterface $mailer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('test@example.com')
            ->to('me@example.com')
            ->subject('Hello Mailpit (console)')
            ->text('Message de test depuis une commande Symfony.');

        $this->mailer->send($email);
        $output->writeln('OK (mail envoyé)');

        return Command::SUCCESS;
    }
}
