import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import type { Cart } from "../interfaces/global";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Cart() {
    const [cart, setCart] = useState<Cart[]>([]);
    const [selectedCartItems, setSelectedCartItems] = useState<number[]>([]);
    const [loading, setLoading] = useState(true);

    async function getCart(showLoading = true) {
        if (showLoading) {
            setLoading(true);
        }

        try {
            const response = await fetch(`${API_BASE_URL}/carts`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
            });
            const data = await response.json();
            console.log(data);
            setCart(data.data);
        } catch (error) {
            console.error('Error fetching cart:', error);
        } finally {
            setLoading(false);
        }
    }
    useEffect(() => {
        getCart(false);
    }, [])

    async function removeFromCart(id: number) {
        try {
            const response = await fetch(`${API_BASE_URL}/carts/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
            });
            const data = await response.json();
            toast.success(data.message);
            getCart();
        } catch (error) {
            console.error('Error removing from cart:', error);
        }
    }

    async function updateCart(id: number, quantity: number) {
        try {
            const response = await fetch(`${API_BASE_URL}/carts/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
                body: JSON.stringify({ quantity }),
            });
            const data = await response.json();
            toast.success(data.message);
            getCart();
        } catch (error) {
            console.error('Error updating cart:', error);
        }
    }

    async function redirectToCheckout(cartItemIds: number[]) {
        router.visit(`/checkout?items=${cartItemIds.join(',')}`);
    }

    async function clearCart() {
        try {
            const response = await fetch(`${API_BASE_URL}/cart/clear`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
            });
            const data = await response.json();
            toast.success(data.message);
            getCart();
        } catch (error) {
            console.error('Error clearing cart:', error);
        }
    }

    const selectedItems = cart.filter(item => selectedCartItems.includes(item.id));

    const subtotal = selectedItems.reduce((sum, item) => {
        const originalPrice = item.variant?.price ?? item.product.price;

        return sum + (originalPrice * item.quantity);
    }, 0);

    const total = selectedItems.reduce((sum, item) => {
        const finalPrice = item.variant?.final_price ?? item.product.final_price ?? item.product.price;

        return sum + (finalPrice * item.quantity);
    }, 0);

    const discount = subtotal - total;

    return (
        <>
            <div>
                <h1>{loading ? 'Loading...' : 'Cart'}</h1>
                <button className="bg-red-500 text-white px-4 py-2 rounded-md" onClick={() => confirm("Are you sure you want to clear your cart?") && clearCart()}>Clear Cart</button>
                {cart?.map(item => (
                    <div key={item.id}>
                        <input type="checkbox" checked={selectedCartItems.includes(item.id)} onChange={(e) => setSelectedCartItems(e.target.checked ? [...selectedCartItems, item.id] : selectedCartItems.filter(id => id !== item.id))} />
                        <h2>{item.product.name}</h2>
                        <p>{item.variant.variant_options_summary}</p>
                        <p>Rs {item.product.price}</p>
                        <p>Rs {item.variant.final_price}</p>
                        <p>Quantity: {item.quantity}</p>
                        <div className="flex items-center">
                            <button className="bg-red-500 text-white px-4 py-2 rounded-md" onClick={() => updateCart(item.id, item.quantity - 1)}>-</button>
                            <button className="bg-green-500 text-white px-4 py-2 rounded-md" onClick={() => updateCart(item.id, item.quantity + 1)}>+</button>
                            <button className="bg-red-500 text-white px-4 py-2 rounded-md" onClick={() => confirm("Are you sure you want to remove this item from your cart?") && removeFromCart(item.id)}>Remove</button>
                        </div>
                    </div>
                ))}
                <p>Subtotal: {subtotal}</p>
                <p>Discount: {discount}</p>
                <p>Total: {total}</p>
                <button className={`bg-green-500 text-white px-4 py-2 rounded-md ${selectedCartItems.length === 0 ? 'opacity-50 cursor-not-allowed' : ''}`} disabled={selectedCartItems.length === 0} onClick={() => redirectToCheckout(selectedCartItems)}>Checkout</button>
            </div>
        </>
    );
}
