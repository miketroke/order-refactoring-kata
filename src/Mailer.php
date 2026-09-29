<?php
declare(strict_types=1);
namespace MRC\OrderRefactoring;
interface Mailer
{
    public function send(string $email, string $subject, string $body): void;
}
