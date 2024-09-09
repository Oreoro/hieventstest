<?php

declare(strict_types=1);

namespace HiEvents\Repository\Interfaces;

use HiEvents\DomainObjects\OrderDomainObject;
use HiEvents\DomainObjects\OrderItemDomainObject;
use HiEvents\Http\DTO\QueryParamsDTO;
use HiEvents\Repository\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<OrderDomainObject>
 */
interface OrderRepositoryInterface extends RepositoryInterface

{
    public function save(OrderDomainObject $order): OrderDomainObject;
    public function findByEventId(int $event_id, QueryParamsDTO $params): LengthAwarePaginator;

    public function getOrderItems(int $order_id);

    public function getAttendees(int $order_id);

    public function addOrderItem(array $data): OrderItemDomainObject;

    public function findByShortId(string $orderShortId): ?OrderDomainObject;
}
