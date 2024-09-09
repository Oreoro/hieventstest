<?php

declare(strict_types=1);

namespace HiEvents\Services\Handlers\Order;

use HiEvents\DomainObjects\EventDomainObject;
use HiEvents\DomainObjects\EventSettingDomainObject;
use HiEvents\DomainObjects\Generated\PromoCodeDomainObjectAbstract;
use HiEvents\DomainObjects\OrderDomainObject;
use HiEvents\DomainObjects\PromoCodeDomainObject;
use HiEvents\DomainObjects\Status\EventStatus;
use HiEvents\Repository\Interfaces\EventRepositoryInterface;
use HiEvents\Repository\Interfaces\PromoCodeRepositoryInterface;
use HiEvents\Services\Domain\Order\OrderItemProcessingService;
use HiEvents\Services\Domain\Order\OrderManagementService;
use HiEvents\Services\Handlers\Order\DTO\CreateOrderPublicDTO;
use HiEvents\Services\Infrastructure\Session\CheckoutSessionManagementService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\UnauthorizedException;
use Throwable;
use HiEvents\Repository\Interfaces\OrderRepositoryInterface;
use HiEvents\Repository;



readonly class CreateOrderHandler
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly PromoCodeRepositoryInterface $promoCodeRepository,
        private readonly OrderManagementService $orderManagementService,
        private readonly OrderItemProcessingService $orderItemProcessingService,
        private readonly DatabaseManager $databaseManager,
        private readonly CheckoutSessionManagementService $sessionIdentifierService,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(
        int                  $event_id,
        CreateOrderPublicDTO $createOrderPublicDTO,
        bool                 $deleteExistingOrdersForSession = true
    ): OrderDomainObject
    {
        $sessionId = $this->sessionIdentifierService->getSessionId();

        return $this->databaseManager->transaction(function () use ($event_id, $createOrderPublicDTO, $deleteExistingOrdersForSession, $sessionId) {
            $event = $this->eventRepository
                ->loadRelation(EventSettingDomainObject::class)
                ->findById($event_id);

            $this->validateEventStatus($event, $createOrderPublicDTO);

            $promoCode = $this->getPromoCode($createOrderPublicDTO, $event_id);

            if ($deleteExistingOrdersForSession) {
                $this->orderManagementService->deleteExistingOrders($event_id, $sessionId);
            }

            $order = $this->orderManagementService->createNewOrder(
                event_id: $event_id,
                event: $event,
                timeOutMinutes: $event->getEventSettings()?->getOrderTimeoutInMinutes(),
                locale: $createOrderPublicDTO->order_locale,
                promoCode: $promoCode,
                sessionId: $sessionId,
            );

            // Save the order to ensure it has an ID
            $order = $this->orderRepository->save($order);

            if ($order->getId() === null) {
                throw new \RuntimeException('Failed to save order and generate ID');
            }

            $orderItems = $this->orderItemProcessingService->process(
                order: $order,
                ticketsOrderDetails: $createOrderPublicDTO->tickets,
                event: $event,
                promoCode: $promoCode,
            );

            return $this->orderManagementService->updateOrderTotals($order, $orderItems);
        });
    }

    private function getPromoCode(CreateOrderPublicDTO $createOrderPublicDTO, int $event_id): ?PromoCodeDomainObject
    {
        if ($createOrderPublicDTO->promo_code === null) {
            return null;
        }

        $promoCode = $this->promoCodeRepository->findFirstWhere([
            PromoCodeDomainObjectAbstract::CODE => strtolower(trim($createOrderPublicDTO->promo_code)),
            PromoCodeDomainObjectAbstract::EVENT_ID => $event_id,
        ]);

        if ($promoCode?->isValid()) {
            return $promoCode;
        }

        return null;
    }

    public function validateEventStatus(EventDomainObject $event, CreateOrderPublicDTO $createOrderPublicDTO): void
    {
        if (!$createOrderPublicDTO->is_user_authenticated && $event->getStatus() !== EventStatus::LIVE->name) {
            throw new UnauthorizedException(
                __('This event is not live.')
            );
        }
    }
}
