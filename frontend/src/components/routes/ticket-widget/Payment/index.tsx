import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import { LoadingMask } from '../../../common/LoadingMask';

const Payment = () => {
  const { eventId, orderShortId } = useParams();
  const [paymentData, setPaymentData] = useState(null);

  useEffect(() => {
    const fetchPaymentData = async () => {
      try {
        const response = await axios.get(`/api/events/${eventId}/orders/${orderShortId}/jazzcash/initiate`);
        setPaymentData(response.data);
      } catch (error) {
        console.error('Error fetching payment data:', error);
        // Handle the error, e.g., show an error message to the user
      }
    };

    fetchPaymentData();
  }, [eventId, orderShortId]);

  useEffect(() => {
    if (paymentData && paymentData.redirect_url) {
      window.location.href = paymentData.redirect_url;
    } else if (paymentData && paymentData.error) {
      // Handle the error, e.g., show an error message to the user
      console.error('Payment initiation failed:', paymentData.error);
    }
  }, [paymentData]);

  return <LoadingMask />;
};

export default Payment;