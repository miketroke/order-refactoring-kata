<?php
declare(strict_types=1);
namespace MRC\OrderRefactoring;
final class Order {
    public function __construct(
        public readonly int $id,
        public readonly string $customerEmail,
        public readonly float $subtotal,
        public readonly bool $vip,
    ) {}
}