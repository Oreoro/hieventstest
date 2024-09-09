import {useQuery} from "@tanstack/react-query";
import {orderClient} from "../api/order.client.ts";
import {GenericPaginatedResponse, IdParam, Order, QueryFilters} from "../types.ts";

export const GET_EVENT_ORDERS_QUERY_KEY = 'getEventOrders';

export const useGetEventOrders = (event_id: string | undefined, filters: QueryFilters) => {
    return useQuery(['orders', event_id, filters], () => orderClient.all(event_id!, filters), {
        enabled: !!event_id,
    });
};