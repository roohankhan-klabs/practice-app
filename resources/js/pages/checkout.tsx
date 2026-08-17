import { useEffect, useState } from "react";
import { toast } from "sonner";
import type { CheckoutData, Bill, CartItem } from "@/interfaces/global";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Checkout() {
    const [loading, setLoading] = useState<boolean>(true);
    const [checkoutData, setCheckoutData] = useState<CheckoutData>();
    const [selectedCartItems, setSelectedCartItems] = useState<number[]>([]);
    const [selectedPaymentMethodId, setSelectedPaymentMethodId] = useState<number | null>(null);
    const [selectedAddressId, setSelectedAddressId] = useState<number | null>(null);
    const [bill, setBill] = useState<Bill | null>(null);

    async function checkout() {
        try {
            const response = await fetch(`${API_BASE_URL}/checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
                body: JSON.stringify({
                    cart_item_ids: selectedCartItems,
                    address_id: selectedAddressId,
                    payment_method_id: selectedPaymentMethodId,
                }),
            });
            setLoading(true);
            const data = await response.json();

            if (data.success) {
                setLoading(false);
                toast.success(data.message);
                console.log("data", data);
                const checkoutUrl = data.data.checkout_url;

                if (!checkoutUrl) {
                    toast.error("Checkout URL not available");

                    return;
                }

                window.location.href = checkoutUrl;
            } else {
                setLoading(false);
                toast.error(data.message);
                console.log("data", data);
            }

        } catch (error) {
            console.error('Error checking out:', error);
            toast.error("Something went wrong");
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        async function fetchCheckoutData() {
            const params = new URLSearchParams(window.location.search);
            const itemsParam = params.get('items');

            if (!itemsParam) {
                toast.error('No items found in the cart');
                // window.location.href = "/cart";
            }

            const cartItemIds = itemsParam?.split(',').map(Number);

            try {
                const response = await fetch(`${API_BASE_URL}/ready-for-checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    },
                    body: JSON.stringify({ cart_item_ids: cartItemIds }),
                });
                const data = await response.json();

                if (data.success) {
                    setCheckoutData(data.data);
                    setBill(data.data.bill);
                    setSelectedCartItems(cartItemIds ?? []);

                    if (data.data.payment_methods?.length > 0) {
                        setSelectedPaymentMethodId(data.data.payment_methods[3].id);
                    }

                    if (data.data.addresses?.length === 0) {
                        toast.error("No addresses found");
                        // window.location.href = "/success";
                    }

                    if (data.data.addresses?.length > 0) {
                        setSelectedAddressId(data.data.addresses[0].id);
                        // window.location.href = "/failed";
                    }

                    console.log("data", data);
                } else {
                    toast.error(data.message);
                    console.log("data", data);
                }
            } catch (error) {
                console.error('Error checking out:', error);
                toast.error("Something went wrong");
            } finally {
                setLoading(false);
            }
        }
        fetchCheckoutData();
    }, []);

    if (loading) {
        return <div>Loading checkout details...</div>
    }

    if (!checkoutData) {
        return <div>No checkout data available</div>
    }

    return (
        <div>
            <h1>Checkout</h1>
            {checkoutData.cart_items?.map((cart_item: CartItem) => (
                <div key={cart_item.id}>
                    <p>{cart_item.product.name}</p>
                    <p>{cart_item.variant.variant_options_summary}</p>
                    <p>{cart_item.quantity}</p>
                </div>
            ))}
            {checkoutData.addresses?.map(address => (
                <div key={address.id}>
                    <input type="radio" name="address" id="address" value={address.id} checked={selectedAddressId === address.id} onChange={(e) => setSelectedAddressId(Number(e.target.value))} />
                    <label htmlFor="address">{address.address_line_1}</label>
                </div>
            ))}
            {checkoutData.payment_methods?.map(payment_method => (
                <div key={payment_method.id}>
                    <input type="radio" name="payment_method" id="payment_method" value={payment_method.id} checked={selectedPaymentMethodId === payment_method.id} onChange={(e) => setSelectedPaymentMethodId(Number(e.target.value))} />
                    <label htmlFor="payment_method">{payment_method.name}</label>
                </div>
            ))}
            {bill && (
                <>
                    Sub total: {bill.subtotal}
                    Shipping fees: {bill.shipping_fees}
                    Tax: {bill.tax}
                    Discount: {bill.discount}
                    Total amount: {bill.total_amount}
                </>
            )}
            <button onClick={checkout}>Checkout</button>
        </div>
    );
}
