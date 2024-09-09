import React, { useEffect, useState } from 'react';
import { useNavigate, useParams, useLocation } from 'react-router-dom';
import { LoadingMask } from '../../common/LoadingMask';
import axios from 'axios';
import { t } from "@lingui/macro";

const JazzCashResponse = () => {
    const { event_id, orderShortId } = useParams();
    const navigate = useNavigate();
    const location = useLocation();
    const [isProcessing, setIsProcessing] = useState(true);

    useEffect(() => {
        const processPaymentResponse = async () => {
            const searchParams = new URLSearchParams(location.search);
            
            try {
                const response = await axios.post(`/api/events/${event_id}/orders/${orderShortId}/jazzcash/response`, searchParams);

                if (response.data.success) {
                    
                    navigate(`/checkout/${event_id}/${orderShortId}/summary`);
                } else {
                
                    navigate(`/checkout/${event_id}/${orderShortId}/payment?payment_failed=true`);
                }
            } catch (error) {
                console.error('Error processing JazzCash response:', error);
                
                navigate(`/checkout/${event_id}/${orderShortId}/payment?payment_failed=true`);
            } finally {
                setIsProcessing(false);
            }
        };

        processPaymentResponse();
    }, [eventId, orderShortId, location.search, navigate]);

    if (isProcessing) {
        return <LoadingMask />;
    }

    return null;
};

export default JazzCashResponse;