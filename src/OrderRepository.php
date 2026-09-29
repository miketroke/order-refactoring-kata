<?php
declare(strict_types=1);
namespace MRC\OrderRefactoring;
interface OrderRepository { public function save(Order $order, float $total): void; }