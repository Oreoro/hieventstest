import React, { useEffect, useState } from 'react';
import { useNavigate, useParams, useLocation } from 'react-router-dom';
import { LoadingMask } from '../../common/LoadingMask';
import axios from 'axios';
import { showError, showSuccess } from '../../../utilites/notifications.tsx';
import { t } from "@lingui/macro";

const JazzCashResponse = () => {
    const { eventId, orderShortId } = useParams();
    const navigate = useNavigate();
    const location = useLocation();
    const [isProcessing, setIsProcessing] = useState(true);

    useEffect(() => {
        const processPaymentResponse = async () => {
            const searchParams = new URLSearchParams(location.search);
            
            try {
                const response = await axios.post(`/api/events/${eventId}/tickets/${orderShortId}/jazzcash/process`, {
                    pp_ResponseCode: searchParams.get('pp_ResponseCode'),
                    pp_ResponseMessage: searchParams.get('pp_ResponseMessage'),
                    pp_TxnRefNo: searchParams.get('pp_TxnRefNo'),
                    pp_Amount: searchParams.get('pp_Amount'),
                    // Add any other necessary parameters from JazzCash response
                });

                if (response.data.success) {
                    showSuccess(t`Payment successful`);
                    navigate(`/checkout/${eventId}/${orderShortId}/summary`);
                } else {
                    showError(t`Payment failed: ${response.data.message}`);
                    navigate(`/checkout/${eventId}/${orderShortId}/payment?payment_failed=true`);
                }
            } catch (error) {
                console.error('Error processing JazzCash response:', error);
                showError(t`An error occurred while processing your payment`);
                navigate(`/checkout/${eventId}/${orderShortId}/payment?payment_failed=true`);
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