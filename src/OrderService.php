<?php
declare(strict_types=1);
namespace MRC\OrderRefactoring;

final class OrderService {
    public function __construct(
        private OrderRepository $repository,
        private Mailer $mailer,
        private Logger $logger,
    ) {}

    public function process(Order $order): float {
        if ($order->id <= 0) throw new \InvalidArgumentException('Invalid order id');
        if ($order->customerEmail === '') throw new \InvalidArgumentException('Customer email is required');
        if ($order->subtotal <= 0) throw new \InvalidArgumentException('Subtotal must be greater than zero');

        $discount = 0.0;
        if ($order->vip) $discount = $order->subtotal * 0.10;

        $discountedSubtotal = $order->subtotal - $discount;
        $tax = $discountedSubtotal * 0.21;
        $total = $discountedSubtotal + $tax;

        if ($total < 0) throw new \RuntimeException('Invalid total');

        $this->repository->save($order, $total);

        $subject = 'Order #' . $order->id;
        $body = 'Your order total is ' . number_format($total, 2, '.', '');
        $this->mailer->send($order->customerEmail, $subject, $body);
        $this->logger->info('Order #' . $order->id . ' processed with total ' . number_format($total, 2, '.', ''));

        return $total;
    }
}