<?php


namespace HiEvents\Http\Actions\Events;

use HiEvents\DomainObjects\EventDomainObject;
use HiEvents\DomainObjects\OrganizerDomainObject;
use HiEvents\DomainObjects\TaxAndFeesDomainObject;
use HiEvents\DomainObjects\TicketDomainObject;
use HiEvents\DomainObjects\TicketPriceDomainObject;
use HiEvents\Http\Actions\BaseAction;
use HiEvents\Repository\Eloquent\Value\Relationship;
use HiEvents\Repository\Interfaces\EventRepositoryInterface;
use HiEvents\Resources\Event\EventResource;
use Illuminate\Http\JsonResponse;

class GetEventAction extends BaseAction
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function __invoke($event_id): JsonResponse
    {
        $event_id = (int) $event_id;
        if (!is_int($event_id)) {
            throw new \InvalidArgumentException('Event ID must be an integer');
        }
        $this->isActionAuthorized($event_id, EventDomainObject::class);

        $event = $this->eventRepository
            ->loadRelation(new Relationship(domainObject: OrganizerDomainObject::class, name: 'organizer'))
            ->loadRelation(
                new Relationship(TicketDomainObject::class, [
                    new Relationship(TicketPriceDomainObject::class),
                    new Relationship(TaxAndFeesDomainObject::class),
                ]),
            )
            ->findById($event_id);

        return $this->resourceResponse(EventResource::class, $event);
    }
}
