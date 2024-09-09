import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import { t } from "@lingui/macro";
import { LoadingMask } from '../../../common/LoadingMask';

const JazzCashForm: React.FC = () => {
    const { event_id, orderShortId } = useParams<{ event_id: string, orderShortId: string }>();
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

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
                console.error('Error initiating JazzCash payment:', error);
                setError('Failed to initiate payment. Please try again.');
            } finally {
                setLoading(false);
            }
        };

        initiatePayment();
    }, [event_id, orderShortId]);

    if (loading) return <LoadingMask />;
    if (error) return <div>{error}</div>;

    return (
        <div>
            <h3>{t`JazzCash Payment`}</h3>
            <p>{t`Redirecting to JazzCash payment page...`}</p>
        </div>
    );
};

export default JazzCashForm;