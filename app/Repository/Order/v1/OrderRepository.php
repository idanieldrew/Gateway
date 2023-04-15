<?php

namespace App\Repository\Order\v1;

use App\Models\Cart;
use App\Models\Order;
use App\Repository\Repository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class OrderRepository implements Repository
{
    public function model(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::query();
    }

    /**
     * Find or fail order
     *
     * @param string $order
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function find(string $order)
    {
        return $this->model()->findOrFail($order);
    }

    /**
     * Store order
     *
     * @param Cart $cart
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Cart $cart)
    {
        return $cart->order()->create([
            'user_id' => auth()->user()->id,
            'total' => $cart->total
        ]);
    }

    /**
     * Check expire order
     *
     * @return mixed
     */
    public function expired()
    {
        return $this->model()
            ->where('expired_at', '<', now())
            ->delete();
    }

    /**
     * Check last status
     *
     * @param Order $order
     * @param string $name
     * @return bool
     */
    public function lastStatus(Order $order, string $name): bool
    {
        return $order->model->name == $name;
    }

    /**
     * Check last order expire
     *
     * @param Order $order
     * @param object $date
     * @return bool
     */
    public function checkLastOrderExpire(Order $order, object $date): bool
    {
        return $order->expired_at > $date;
    }

    /**
     * Check expire order
     *
     * @param Order $order
     * @return bool
     */
    protected function isExpired(Order $order): bool
    {
        return (bool)$order->isExpired(now())->count();
    }

    /**
     * Check order status
     *
     * @param Order $order
     * @return bool
     */
    protected function isOpened(Order $order): bool
    {
        return $order->model->name == 'pending';
    }

    /**
     * Check payment is complete
     *
     * @param Order $order
     * @return bool
     */
    protected function isPaid(Order $order): bool
    {
        return $order->payments->isEmpty() || $order->payments->last()->model->name !== 'complete';
    }

    /**
     * @throws Exception
     */
    public function beforePayment(Order $order): void
    {
        if (!$this->isExpired($order)) {
            throw new Exception('Order was expired');
        }
        if (!$this->isOpened($order)) {
            throw new Exception('Order was completed');
        }
        if (!$this->isPaid($order)) {
            throw new Exception('Order was payed');
        }
    }
}
