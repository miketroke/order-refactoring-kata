<?php
declare(strict_types=1);
namespace MRC\OrderRefactoring;

final class OrderService
{
    public function __construct(
        private OrderRepository $repository,
        private Mailer $mailer,
        private Logger $logger,
    ) {
    }

    public function process(Order $order): float
    {
        $this->validateOrder($order);

        $total = $this->calculateTotal($order);

        if ($total < 0)
            throw new \RuntimeException('Invalid total');

        $this->repository->save($order, $total);

        $this->sendEmail($order, $total);
        $this->logOrderProcessed($order, $total);

        return $total;
    }

    private function validateOrder(Order $order): void
    {
        if ($order->id <= 0)
            throw new \InvalidArgumentException('Invalid order id');
        if ($order->customerEmail === '')
            throw new \InvalidArgumentException('Customer email is required');
        if ($order->subtotal <= 0)
            throw new \InvalidArgumentException('Subtotal must be greater than zero');
    }

    private function calculateTotal(Order $order): float
    {
        $discount = 0.0;
        if ($order->vip)
            $discount = $order->subtotal * 0.10;

        $discountedSubtotal = $order->subtotal - $discount;
        $tax = $discountedSubtotal * 0.21;
        $total = $discountedSubtotal + $tax;
        return $total;
    }

    private function sendEmail(Order $order, float $total): void
    {
        $subject = 'Order #' . $order->id;
        $body = 'Your order total is ' . number_format($total, 2, '.', '');
        $this->mailer->send($order->customerEmail, $subject, $body);
    }

    private function logOrderProcessed(Order $order, float $total): void
    {
        $this->logger->info('Order #' . $order->id . ' processed with total ' . number_format($total, 2, '.', ''));
    }

}
