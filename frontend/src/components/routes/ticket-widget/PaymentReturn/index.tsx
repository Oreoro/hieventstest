export const PaymentReturn = () => {
    const [shouldPoll, setShouldPoll] = useState(true);
    const { eventId, orderShortId } = useParams();
    const { data: order } = usePollGetOrderPublic(eventId, orderShortId, shouldPoll);
    const navigate = useNavigate();

    useEffect(() => {
        const timeout = setTimeout(() => {
            setShouldPoll(false);
        }, 10000);

        return () => {
            clearTimeout(timeout);
        };
    }, []);

    useEffect(() => {
        if (isSsr() || !order) {
            return;
        }

        if (order?.status === 'COMPLETED') {
            navigate(eventCheckoutPath(eventId, orderShortId, 'summary'));
        }
        if (order?.payment_status === 'PAYMENT_FAILED' || (typeof window !== 'undefined' && window?.location.search.includes('failed'))) {
            navigate(eventCheckoutPath(eventId, orderShortId, 'payment') + '?payment_failed=true');
        }
    }, [order]);

    return (
        <CheckoutContent>
            <div className={classes.container}>
                <HomepageInfoMessage
                    iconType={'processing'}
                    message={
                        shouldPoll
                            ? t`We're processing your order. Please wait...`
                            : t`We could not process your payment. Please try again or contact support.`
                    }
                />
            </div>
        </CheckoutContent>
    );
};