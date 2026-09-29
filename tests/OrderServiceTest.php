<?php
declare(strict_types=1);
namespace MRC\OrderRefactoring\Tests;

use MRC\OrderRefactoring\{Logger,Mailer,Order,OrderRepository,OrderService};
use PHPUnit\Framework\TestCase;

final class OrderServiceTest extends TestCase {
    public function test_it_processes_a_normal_order(): void {
        $repository=$this->createMock(OrderRepository::class);
        $mailer=$this->createMock(Mailer::class);
        $logger=$this->createMock(Logger::class);
        $order=new Order(100,'john@example.com',100.00,false);

        $repository->expects(self::once())->method('save')->with($order,121.00);
        $mailer->expects(self::once())->method('send')->with('john@example.com','Order #100','Your order total is 121.00');
        $logger->expects(self::once())->method('info')->with('Order #100 processed with total 121.00');

        $service=new OrderService($repository,$mailer,$logger);
        self::assertSame(121.00,$service->process($order));
    }

    public function test_it_applies_vip_discount(): void {
        $repository=$this->createMock(OrderRepository::class);
        $mailer=$this->createMock(Mailer::class);
        $logger=$this->createMock(Logger::class);
        $order=new Order(101,'vip@example.com',100.00,true);

        $repository->expects(self::once())->method('save')->with($order,108.90);
        $mailer->expects(self::once())->method('send')->with('vip@example.com','Order #101','Your order total is 108.90');
        $logger->expects(self::once())->method('info')->with('Order #101 processed with total 108.90');

        $service=new OrderService($repository,$mailer,$logger);
        self::assertSame(108.90,$service->process($order));
    }

    public function test_it_rejects_invalid_order_id(): void {
        $service=new OrderService($this->createMock(OrderRepository::class),$this->createMock(Mailer::class),$this->createMock(Logger::class));
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid order id');
        $service->process(new Order(0,'john@example.com',100,false));
    }

    public function test_it_rejects_missing_customer_email(): void {
        $service=new OrderService($this->createMock(OrderRepository::class),$this->createMock(Mailer::class),$this->createMock(Logger::class));
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Customer email is required');
        $service->process(new Order(100,'',100,false));
    }

    public function test_it_rejects_invalid_subtotal(): void {
        $service=new OrderService($this->createMock(OrderRepository::class),$this->createMock(Mailer::class),$this->createMock(Logger::class));
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Subtotal must be greater than zero');
        $service->process(new Order(100,'john@example.com',0,false));
    }
}