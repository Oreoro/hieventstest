import {publicApi} from "./public-client.ts";
import {
    GenericDataResponse,
    GenericPaginatedResponse,
    IdParam,
    Order,
    QueryFilters,
    Ticket,
    PromoCode,
    Question,
    Event
    

}
 from "../types.ts";
import {api} from "./client.ts";
import {queryParamsHelper} from "../utilites/queryParamsHelper.ts";
interface PaymentData {
    redirect_url?: string;
    error?: string;
  }


export interface OrderDetails {
    first_name: string,
    last_name: string,
    email: string,
}

export interface AttendeeDetails extends OrderDetails {
    ticket_id: number,
}

export interface FinaliseOrderPayload {
    order: OrderDetails,
    attendees: AttendeeDetails[],
}


export interface TicketPriceQuantityFormValue {
    price?: number,
    quantity: number,
    price_id: number,
}

export interface TicketFormValue {
    ticket_id: number,
    quantities: TicketPriceQuantityFormValue[],
}

export interface TicketFormPayload {
    tickets?: TicketFormValue[],
    promo_code: string | null,
    session_identifier?: string,
}


export interface RefundOrderPayload {
    amount: number;
    notify_buyer: boolean;
    cancel_order: boolean;
}

export const orderClient = {
    all: async (event_id: IdParam, pagination: QueryFilters) => {
        const response = await api.get<GenericPaginatedResponse<Order>>(
            `/events/${event_id}/orders` + queryParamsHelper.buildQueryString(pagination),
        );
        return response.data;
    },

    findByID: async (event_id: IdParam, order_id: IdParam) => {
        const response = await api.get<GenericDataResponse<Order>>(`events/${event_id}/orders/${order_id}`);
        return response.data;
    },

    refund: async (event_id: IdParam, order_id: IdParam, refundPayload: RefundOrderPayload) => {
        const response = await api.post<GenericDataResponse<Order>>('events/' + event_id + '/orders/' + order_id + '/refund', refundPayload);
        return response.data;
    },

    resendConfirmation: async (event_id: IdParam, order_id: IdParam) => {
        const response = await api.post<GenericDataResponse<Order>>('events/' + event_id + '/orders/' + order_id + '/resend_confirmation');
        return response.data;
    },

    cancel: async (event_id: IdParam, order_id: IdParam) => {
        const response = await api.post<GenericDataResponse<Order>>('events/' + event_id + '/orders/' + order_id + '/cancel');
        return response.data;
    },

    exportOrders: async (event_id: IdParam): Promise<Blob> => {
        const response = await api.post(`public/events/${event_iddd}/orders/export`, {}, {
            responseType: 'blob',
        });

        return new Blob([response.data]);
    },
}

export const orderClientPublic = {
    create: async (event_id: number, createOrderPayload: TicketFormPayload) => {
        const response = await publicApi.post<GenericDataResponse<Order>>(`events/${event_id}/orders`, createOrderPayload);
        return response.data;
    },

    findByShortId: async (event_id: number, orderShortId: string, sessionIdentifier: string) => {
        const response = await publicApi.get<GenericDataResponse<Order>>(`events/${event_id}/orders/${orderShortId}?session_identifier=${sessionIdentifier}`);
        return response.data;
    },

    initiateJazzCashPayment: async (event_id: number, orderShortId: string) => {
        const response = await publicApi.post<JazzCashInitiateResponse>(
            `events/${event_id}/orders/${orderShortId}/jazzcash/initiate`
        );
        return response.data;
    },


    finaliseOrder: async (
        event_id: number,
        orderShortId: string,
        payload: FinaliseOrderPayload
    ) => {
        const response = await publicApi.put<GenericDataResponse<Order>>(`events/${event_id}/orders/${orderShortId}`, payload);
        return response.data;
    },

    // Add other methods as needed, for example:
    getEventDetails: async (event_id: number) => {
        const response = await publicApi.get<GenericDataResponse<Event>>(`events/${event_id}`);
        return response.data;
    },

    getEventTickets: async (event_id: number) => {
        const response = await publicApi.get<GenericDataResponse<Ticket[]>>(`events/${event_id}/tickets`);
        return response.data;
    },
    getPromoCode: async (event_id: number, promoCode: string) => {
        const response = await publicApi.get<GenericDataResponse<PromoCode>>(`events/${event_id}/promo-codes/${promoCode}`);
        return response.data;
    },

    getEventQuestions: async (event_id: number) => {
        const response = await publicApi.get<GenericDataResponse<Question[]>>(`events/${event_id}/questions`);
        return response.data;
    },
};
