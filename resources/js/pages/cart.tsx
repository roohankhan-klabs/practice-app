import { Link, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import { 
    ShoppingBag, Trash2, Plus, Minus, ChevronLeft, 
    Lock, ArrowRight, ShieldCheck, HelpCircle 
} from "lucide-react";
import type { Cart } from "../interfaces/global";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import StorefrontHeader from "@/components/storefront-header";

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
            setCart(data.data || []);
        } catch (error) {
            console.error('Error fetching cart:', error);
            toast.error("Failed to load cart items");
        } finally {
            setLoading(false);
        }
    }
    
    useEffect(() => {
        getCart(false);
    }, []);

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
            if (response.ok) {
                toast.success(data.message || "Item removed from cart");
                setSelectedCartItems(prev => prev.filter(itemId => itemId !== id));
                getCart();
            } else {
                toast.error(data.message || "Failed to remove item");
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
            toast.error("Something went wrong");
        }
    }

    async function updateCart(id: number, quantity: number) {
        if (quantity < 1) return;
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
            if (response.ok) {
                toast.success(data.message || "Quantity updated");
                getCart(false);
            } else {
                toast.error(data.message || "Failed to update quantity");
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            toast.error("Something went wrong");
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
            if (response.ok) {
                toast.success(data.message || "Cart cleared");
                setSelectedCartItems([]);
                getCart();
            } else {
                toast.error(data.message || "Failed to clear cart");
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            toast.error("Something went wrong");
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

    const isAllSelected = cart.length > 0 && selectedCartItems.length === cart.length;
    
    function toggleSelectAll() {
        if (isAllSelected) {
            setSelectedCartItems([]);
        } else {
            setSelectedCartItems(cart.map(item => item.id));
        }
    }

    function toggleSelectItem(id: number) {
        if (selectedCartItems.includes(id)) {
            setSelectedCartItems(selectedCartItems.filter(itemId => itemId !== id));
        } else {
            setSelectedCartItems([...selectedCartItems, id]);
        }
    }

    if (loading) {
        return (
            <div className="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans pb-16">
                <StorefrontHeader />
                <div className="flex min-h-[400px] items-center justify-center">
                    <div className="flex flex-col items-center gap-4 text-slate-500">
                        <div className="h-10 w-10 animate-spin rounded-full border-4 border-indigo-600 border-t-transparent"></div>
                        <p className="text-sm font-medium animate-pulse">Loading your cart...</p>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans pb-16">
            <StorefrontHeader />

            {/* Breadcrumb path */}
            <div className="border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-950 px-4 sm:px-6 lg:px-8 py-4">
                <div className="mx-auto max-w-7xl flex items-center justify-between">
                    <nav className="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <Link href="/welcome" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            Storefront
                        </Link>
                        <ChevronLeft className="h-4 w-4 rotate-180" />
                        <span className="text-slate-900 dark:text-white font-bold">Shopping Cart</span>
                    </nav>
                </div>
            </div>

            <main className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
                {cart.length === 0 ? (
                    <div className="text-center py-20 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs max-w-md mx-auto space-y-6">
                        <div className="inline-flex p-4 rounded-full bg-indigo-50/80 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400">
                            <ShoppingBag className="h-10 w-10 animate-bounce" />
                        </div>
                        <div className="space-y-2">
                            <h2 className="text-xl font-bold text-slate-900 dark:text-white">Your Cart is Empty</h2>
                            <p className="text-slate-550 dark:text-slate-400 text-sm max-w-xs mx-auto">
                                Looks like you haven't added anything to your cart yet. Let's find some amazing products!
                            </p>
                        </div>
                        <Link href="/welcome">
                            <Button className="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm cursor-pointer">
                                Explore Storefront
                            </Button>
                        </Link>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                        {/* Cart Items List */}
                        <div className="lg:col-span-2 space-y-4">
                            {/* Toolbar */}
                            <div className="flex items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-2xs">
                                <label className="flex items-center gap-3 cursor-pointer select-none">
                                    <input 
                                        type="checkbox" 
                                        checked={isAllSelected} 
                                        onChange={toggleSelectAll}
                                        className="h-4.5 w-4.5 rounded border-slate-350 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                    />
                                    <span className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        Select All ({cart.length} items)
                                    </span>
                                </label>
                                <Button 
                                    variant="ghost" 
                                    size="sm" 
                                    className="text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/20 font-semibold cursor-pointer"
                                    onClick={() => confirm("Are you sure you want to clear your cart?") && clearCart()}
                                >
                                    <Trash2 className="mr-1.5 h-3.5 w-3.5" />
                                    Clear Cart
                                </Button>
                            </div>

                            {/* Items */}
                            <div className="space-y-3">
                                {cart.map((item) => {
                                    const hasDiscount = item.product.discount_value > 0;
                                    const originalPrice = item.variant?.price ?? item.product.price;
                                    const finalPrice = item.variant?.final_price ?? item.product.final_price ?? item.product.price;
                                    
                                    return (
                                        <div 
                                            key={item.id}
                                            className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-2xs flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between"
                                        >
                                            {/* Left side info */}
                                            <div className="flex items-center gap-4 flex-1">
                                                <input 
                                                    type="checkbox" 
                                                    checked={selectedCartItems.includes(item.id)} 
                                                    onChange={() => toggleSelectItem(item.id)}
                                                    className="h-4.5 w-4.5 rounded border-slate-350 text-indigo-600 focus:ring-indigo-500 cursor-pointer shrink-0"
                                                />
                                                
                                                {/* Visual Area */}
                                                <div className="h-16 w-16 bg-slate-50 dark:bg-slate-950 rounded-lg border border-slate-250 dark:border-slate-850 flex items-center justify-center text-slate-350 dark:text-slate-800 shrink-0">
                                                    <ShoppingBag className="h-8 w-8" />
                                                </div>

                                                <div className="space-y-1">
                                                    <h3 className="font-bold text-sm text-slate-900 dark:text-white line-clamp-1">
                                                        {item.product.name}
                                                    </h3>
                                                    {item.variant && item.variant.variant_options_summary && (
                                                        <Badge variant="outline" className="text-[10px] font-bold py-0 bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400">
                                                            {item.variant.variant_options_summary}
                                                        </Badge>
                                                    )}
                                                    
                                                    {/* Prices */}
                                                    <div className="flex items-center gap-2 pt-0.5">
                                                        <span className="text-sm font-extrabold text-indigo-600 dark:text-indigo-400">
                                                            Rs {finalPrice}
                                                        </span>
                                                        {hasDiscount && (
                                                            <span className="text-xs text-slate-400 line-through">
                                                                Rs {originalPrice}
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Right side actions */}
                                            <div className="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 dark:border-slate-800">
                                                {/* Quantity selector */}
                                                <div className="flex items-center border border-slate-250 dark:border-slate-850 rounded-lg bg-slate-50/50 dark:bg-slate-950 overflow-hidden shrink-0">
                                                    <Button 
                                                        variant="ghost" 
                                                        size="icon" 
                                                        className="h-8 w-8 text-slate-500 hover:text-slate-900 dark:hover:text-slate-100 rounded-none cursor-pointer"
                                                        onClick={() => updateCart(item.id, item.quantity - 1)}
                                                        disabled={item.quantity <= 1}
                                                    >
                                                        <Minus className="h-3 w-3" />
                                                    </Button>
                                                    <span className="w-8 text-center text-xs font-bold text-slate-800 dark:text-slate-200 select-none">
                                                        {item.quantity}
                                                    </span>
                                                    <Button 
                                                        variant="ghost" 
                                                        size="icon" 
                                                        className="h-8 w-8 text-slate-500 hover:text-slate-900 dark:hover:text-slate-100 rounded-none cursor-pointer"
                                                        onClick={() => updateCart(item.id, item.quantity + 1)}
                                                    >
                                                        <Plus className="h-3 w-3" />
                                                    </Button>
                                                </div>

                                                {/* Delete Item */}
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    className="h-9 w-9 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/20 shrink-0 cursor-pointer rounded-lg"
                                                    onClick={() => confirm("Are you sure you want to remove this item?") && removeFromCart(item.id)}
                                                    title="Remove item"
                                                >
                                                    <Trash2 className="h-4.5 w-4.5" />
                                                </Button>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        {/* Order Summary Panel */}
                        <div className="lg:sticky lg:top-20 space-y-4">
                            <Card className="border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
                                <CardHeader className="border-b border-slate-100 dark:border-slate-800 pb-4">
                                    <CardTitle className="text-md font-bold text-slate-900 dark:text-white">Order Summary</CardTitle>
                                    <CardDescription className="text-xs">
                                        Choose items to compute checkout totals.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4 pt-4">
                                    <div className="space-y-2.5 text-sm">
                                        <div className="flex justify-between text-slate-500 dark:text-slate-400">
                                            <span>Subtotal</span>
                                            <span className="font-medium text-slate-900 dark:text-white">Rs {subtotal}</span>
                                        </div>
                                        
                                        {discount > 0 && (
                                            <div className="flex justify-between text-slate-500 dark:text-slate-400">
                                                <span>Coupon Discount</span>
                                                <span className="font-semibold text-emerald-600 dark:text-emerald-450">-Rs {discount}</span>
                                            </div>
                                        )}

                                        <div className="flex justify-between text-slate-550 dark:text-slate-400">
                                            <span>Shipping Fee</span>
                                            <span className="text-xs text-slate-400 italic">Calculated at checkout</span>
                                        </div>

                                        <div className="h-px bg-slate-100 dark:bg-slate-800 my-1" />

                                        <div className="flex justify-between text-md font-extrabold text-slate-900 dark:text-white pt-1">
                                            <span>Total Amount</span>
                                            <span className="text-lg font-black text-indigo-600 dark:text-indigo-400">Rs {total}</span>
                                        </div>
                                    </div>

                                    {/* Checkout button */}
                                    <Button
                                        className={`w-full py-6 text-sm font-semibold rounded-xl transition-all shadow-md cursor-pointer ${
                                            selectedCartItems.length === 0 
                                            ? 'bg-slate-200 text-slate-400 dark:bg-slate-800 dark:text-slate-600 shadow-none cursor-not-allowed' 
                                            : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-600/15'
                                        }`}
                                        disabled={selectedCartItems.length === 0}
                                        onClick={() => redirectToCheckout(selectedCartItems)}
                                    >
                                        Proceed to Checkout ({selectedCartItems.length})
                                        <ArrowRight className="ml-2 h-4 w-4" />
                                    </Button>
                                </CardContent>
                            </Card>

                            {/* Trust badges */}
                            <div className="bg-slate-100/50 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-800/50 rounded-xl p-3.5 flex items-start gap-3">
                                <ShieldCheck className="h-5 w-5 text-emerald-500 shrink-0 mt-0.5" />
                                <div className="space-y-0.5">
                                    <p className="text-[11px] font-bold text-slate-700 dark:text-slate-350">Secure Transactions</p>
                                    <p className="text-[10px] text-slate-450 dark:text-slate-500 leading-normal">
                                        Your payment information is encrypted and secured by SSL protection.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </main>
        </div>
    );
}
