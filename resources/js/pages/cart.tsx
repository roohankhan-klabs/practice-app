import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import type { Cart } from "../interfaces/global";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Cart() {
    const [cart, setCart] = useState<Cart>();
    const [loading, setLoading] = useState(true);
    const [total, setTotal] = useState(0);
    const [subtotal, setSubtotal] = useState(0);
    const [discount, setDiscount] = useState(0);
    const [tax, setTax] = useState(0);

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

    async function removeFromCart(id: string) {
        try {
            const response = await fetch(`${API_BASE_URL}/carts/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const data = await response.json();
            toast.success(data.message);
            getCart();
        } catch (error) {
            console.error('Error removing from cart:', error);
        }
    }

    async function updateCart(id: string, quantity: number) {
        try {
            const response = await fetch(`${API_BASE_URL}/carts/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
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

    async function checkout() {
        try {
            const response = await fetch(`${API_BASE_URL}/checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const data = await response.json();
            toast.success(data.message);
            router.get('/orders');
        } catch (error) {
            console.error('Error checking out:', error);
        }
    }

    async function clearCart() {
        try {
            const response = await fetch(`${API_BASE_URL}/cart/clear`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const data = await response.json();
            toast.success(data.message);
            getCart();
        } catch (error) {
            console.error('Error clearing cart:', error);
        }
    }

    return (
        <>
            <div>
                <h1>Cart</h1>
                {cart?.map(item => (
                    <div key={item.id}>
                        <img src={item.product.images[0].image} alt={item.product.name} />
                        <h2>{item.product.name}</h2>
                        <p>{item.product.description}</p>
                        <p>{item.product.price}</p>
                        <p>{item.product.stock}</p>
                        <p>{item.product.is_in_cart}</p>
                    </div>
                ))}
            </div>
        </>
    );
}
