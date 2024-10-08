<?php

namespace HiEvents\Http\Actions\PromoCodes;

use HiEvents\DomainObjects\EventDomainObject;
use HiEvents\Http\Actions\BaseAction;
use HiEvents\Services\Handlers\PromoCode\DeletePromoCodeHandler;
use HiEvents\Services\Handlers\PromoCode\DTO\DeletePromoCodeDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeletePromoCodeAction extends BaseAction
{
    public function __construct(
        private readonly DeletePromoCodeHandler $deletePromoCodeHandler
    )
    {
    }

    public function __invoke(Request $request, int $event_id, int $promoCodeId): Response
    {
        $this->isActionAuthorized($event_id, EventDomainObject::class);

        $this->deletePromoCodeHandler->handle(DeletePromoCodeDTO::fromArray([
            'promo_code_id' => $promoCodeId,
            'event_id' => $event_id,
            'user_id' => $request->user()->id,
        ]));

        return $this->noContentResponse();
    }
}
