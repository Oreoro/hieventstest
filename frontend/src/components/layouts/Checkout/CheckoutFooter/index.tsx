import { ActionIcon, Button } from "@mantine/core";
import { t } from "@lingui/macro";
import { IconShoppingCartDown, IconShoppingCartUp } from "@tabler/icons-react";
import classes from "./CheckoutFooter.module.scss";
import { Event, Order } from "../../../../types.ts";
import { CheckoutSidebar } from "../CheckoutSidebar";
import { useState } from "react";
import classNames from "classnames";
import axios from "axios"; // Import axios for API calls
import { useNavigate } from "react-router-dom"; // Import useNavigate

interface ContinueButtonProps {
    isLoading: boolean;
    buttonText?: string;
    order: Order;
    event: Event;
    isOrderComplete?: boolean;
}

export const CheckoutFooter = ({ isLoading, buttonText, event, order, isOrderComplete = false }: ContinueButtonProps) => {
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const navigate = useNavigate(); // Add useNavigate hook

        const handlePayment = () => {
            if (order && order.short_id) {
                navigate(`/checkout/${event.id}/${order.short_id}/payment`);
            } else {
                console.error('Order short ID is undefined');
                // Optionally, show an error message to the user
            }
        };

    return (
        <>
            {isSidebarOpen && <div className={classes.overlay} onClick={() => setIsSidebarOpen(false)} />}
            <div className={classNames(classes.footer, isOrderComplete ? classes.orderComplete : '')}>
                {isSidebarOpen && <CheckoutSidebar event={event} order={order} className={classes.sidebar} />}
                <div className={classes.buttons}>
                    {!isOrderComplete && (
                        <>
                            <Button
                                className={classes.continueButton}
                                loading={isLoading}
                                type="button" // Change to button to prevent form submission
                                size="md"
                                onClick={handlePayment} // Call the payment handler
                            >
                                {buttonText || t`Pay with JazzCash`}
                            </Button>
                        </>
                    )}
                    <ActionIcon onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                        variant={'transparent'}
                        size={'md'}
                        className={classes.orderSummaryToggle}
                    >
                        {isSidebarOpen ? <IconShoppingCartDown stroke={2} /> : <IconShoppingCartUp stroke={2} />}
                    </ActionIcon>
                </div>
            </div>
        </>
    );
}
