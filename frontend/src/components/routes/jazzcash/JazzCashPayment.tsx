import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import { LoadingMask } from '../../common/LoadingMask';

const JazzCashPayment = () => {
    const { event_id, orderShortId } = useParams();
    const [error, setError] = useState(null);

    useEffect(() => {
        const initiatePayment = async () => {
            try {
                const response = await axios.post(`/api/events/${event_id}/orders/${orderShortId}/jazzcash/initiate`);
                const { formData, postUrl } = response.data;

                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = postUrl;

                for (const key in formData) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = formData[key];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            } catch (error) {
                console.error('Error initiating payment:', error);
                setError(error.response?.data?.message || 'Failed to initiate payment. Please try again later.');
            }
        };

        initiatePayment();
    }, [event_id, orderShortId]);

    if (error) return <div className="error-message">{error}</div>;
    return <LoadingMask />;
};

export default JazzCashPayment;