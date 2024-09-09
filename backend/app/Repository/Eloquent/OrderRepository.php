<?php

declare(strict_types=1);

namespace HiEvents\Repository\Eloquent;

use HiEvents\DomainObjects\AttendeeDomainObject;
use HiEvents\DomainObjects\Generated\OrderDomainObjectAbstract;
use HiEvents\DomainObjects\OrderDomainObject;
use HiEvents\DomainObjects\OrderItemDomainObject;
use HiEvents\DomainObjects\Status\OrderStatus;
use HiEvents\Http\DTO\QueryParamsDTO;
use HiEvents\Models\Order;
use HiEvents\Models\OrderItem;
use HiEvents\Repository\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected $orderModel;  

    public function __construct(Application $app, DatabaseManager $db, Order $orderModel)
    {
        parent::__construct($app, $db);
        $this->orderModel = $orderModel;
    }

    public function findByEventId(int $event_id, QueryParamsDTO $params): LengthAwarePaginator
    {
        $where = [
            [OrderDomainObjectAbstract::EVENT_ID, '=', $event_id],
            [OrderDomainObjectAbstract::STATUS, '!=', OrderStatus::RESERVED->name],
        ];

        if ($params->query) {
            $where[] = static function (Builder $builder) use ($params) {
                $builder
                    ->where(
                        DB::raw(
                            sprintf(
                                "(%s||' '||%s)",
                                OrderDomainObjectAbstract::FIRST_NAME,
                                OrderDomainObjectAbstract::LAST_NAME
                            )
                        ), 'ilike', '%' . $params->query . '%')
                    ->orWhere(OrderDomainObjectAbstract::LAST_NAME, 'ilike', '%' . $params->query . '%')
                    ->orWhere(OrderDomainObjectAbstract::PUBLIC_ID, 'ilike', '%' . $params->query . '%')
                    ->orWhere(OrderDomainObjectAbstract::EMAIL, 'ilike', '%' . $params->query . '%');
            };
        }

        $this->model = $this->model->orderBy(
            $params->sort_by ?? OrderDomainObject::getDefaultSort(),
            $params->sort_direction ?? 'desc',
        );

        return $this->paginateWhere(
            where: $where,
            limit: $params->per_page,
            page: $params->page,
        );
    }

    public function getOrderItems(int $order_id)
    {
        return $this->handleResults(
            $this->model->find($order_id)->orderItems,
            OrderItemDomainObject::class
        );
    }

    public function getAttendees(int $order_id)
    {
        return $this->handleResults(
            $this->model->find($order_id)->attendees,
            AttendeeDomainObject::class
        );
    }

    public function addOrderItem(array $data): OrderItemDomainObject
    {
        $orderItem = $this->initModel(OrderItem::class)->create($data);

        return $this->handleSingleResult($orderItem, OrderItemDomainObject::class);
    }

    /**
     * @param string $orderShortId
     * @return OrderDomainObject|null
     */
    public function findByShortId(string $orderShortId): ?OrderDomainObject
    {
        return $this->findFirstByField('short_id', $orderShortId);
    }

    public function getDomainObject(): string
    {
        return OrderDomainObject::class;
    }

    protected function getModel(): string
    {
        return Order::class;
    }

    public function findById($id, array $columns = self::DEFAULT_COLUMNS): \HiEvents\DomainObjects\Interfaces\DomainObjectInterface
    {
        $order = $this->model->where('short_id', $id)->first($columns);

        if (!$order) {
            Log::error("Order not found", ['short_id' => $id]);
            throw new ModelNotFoundException("Order not found with order_id: $id");
        }

        return $this->handleSingleResult($order, $this->getDomainObject());
    }

    public function save(OrderDomainObject $order): OrderDomainObject
    {
        $attributes = $order->toArray();
        unset($attributes['id']);
        
        try {
            $existingOrder = $this->model->where($this->getUniqueFields($attributes))->first();
            if ($existingOrder) {
                $existingOrder->update($attributes);
                $order->setId($existingOrder->id);
            } else {
                $newOrder = $this->model->create($attributes);
                $order->setId($newOrder->id);
            }
        } catch (QueryException $e) {
            // Log the error
            throw new \RuntimeException('Error saving order: ' . $e->getMessage(), 0, $e);
        }

        return $order;
    }

    private function getUniqueFields(array $attributes): array
    {
        // Define which fields constitute a unique order
        // For example: ['event_id', 'email', 'public_id']
        $uniqueFields = ['event_id', 'email', 'public_id'];
        return array_intersect_key($attributes, array_flip($uniqueFields));
    }
}

   
