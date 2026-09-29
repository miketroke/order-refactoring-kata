<?php
declare(strict_types=1);
namespace MRC\OrderRefactoring;
interface Logger { public function info(string $message): void; }