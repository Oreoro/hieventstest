import React, { useEffect } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import { LoadingMask } from '../../common/LoadingMask';

const JazzCashPayment = () => {
    const { eventId, orderShortId } = useParams();

    useEffect(() => {
        const initiatePayment = async () => {
            try {
                const response = await axios.post(`/api/events/${eventId}/tickets/${orderShortId}/jazzcash/payment`);
                if (response.data && response.data.redirect_url) {
                    window.location.href = response.data.redirect_url;
                } else {
                    throw new Error('Invalid response from server');
                }
            } catch (error) {
                console.error('Error initiating JazzCash payment:', error);
                // Handle error (e.g., show error message to user)
            }
        };

        initiatePayment();
    }, [eventId, orderShortId]);

    return <LoadingMask />;
};

export default JazzCashPayment;