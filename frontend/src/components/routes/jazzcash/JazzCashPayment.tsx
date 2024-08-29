import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import { LoadingMask } from '../../common/LoadingMask';

const JazzCashPayment = () => {
    const { eventId, orderShortId } = useParams();
    const [paymentData, setPaymentData] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        const initiatePayment = async () => {
            try {
                const response = await axios.post(`/api/events/${eventId}/orders/${orderShortId}/jazzcash/initiate`);
                setPaymentData(response.data);
                if (response.data.redirect_url) {
                    window.location.href = response.data.redirect_url;
                }
            } catch (error) {
                console.error('Error initiating payment:', error);
                setError('Failed to initiate payment. Please try again later.');
            }
        };

        initiatePayment();
    }, [eventId, orderShortId]);

    if (error) return <div className="error-message">{error}</div>;
    return <LoadingMask />;
};

export default JazzCashPayment;