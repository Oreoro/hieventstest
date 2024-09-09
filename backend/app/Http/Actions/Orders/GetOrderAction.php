<?php

namespace HiEvents\Http\Actions\Orders;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use HiEvents\DomainObjects\AttendeeDomainObject;
use HiEvents\DomainObjects\OrderItemDomainObject;
use HiEvents\DomainObjects\QuestionAndAnswerViewDomainObject;
use HiEvents\Http\Actions\BaseAction;
use HiEvents\Repository\Interfaces\OrderRepositoryInterface;
use HiEvents\Resources\Order\OrderResource;

class GetOrderAction extends BaseAction
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(int $eventId, int $orderId): JsonResponse
    {
        try {
            $order = $this->orderRepository
                ->loadRelation(OrderItemDomainObject::class)
                ->loadRelation(AttendeeDomainObject::class)
                ->loadRelation(QuestionAndAnswerViewDomainObject::class)
                ->findById($orderId);

            if (!$order || $order->getEventId() !== $eventId) {
                return response()->json(['error' => "Order not found with order_id: $orderId"], 404);
            }

            return $this->resourceResponse(OrderResource::class, $order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    }
}
