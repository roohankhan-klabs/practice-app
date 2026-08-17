import { Link } from "@inertiajs/react";
import { Search, Store, ShoppingBag, Heart, LogOut } from "lucide-react";
import { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? '/api/v1';

function buildApiUrl(path: string) {
    if (/^https?:\/\//.test(API_BASE_URL)) {
        return `${API_BASE_URL}${path}`;
    }

    return `${window.location.origin}${API_BASE_URL}${path}`;
}

interface StorefrontHeaderProps {
    searchQuery?: string;
    setSearchQuery?: (query: string) => void;
    showSearch?: boolean;
}

export default function StorefrontHeader({
    searchQuery = "",
    setSearchQuery,
    showSearch = false,
}: StorefrontHeaderProps) {

    const [cartItemsCount, setCartItemsCount] = useState(0);

    async function handleLogout() {
        try {
            await fetch(buildApiUrl('/logout'), {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            });
        } catch (e) {
            console.error("Logout request failed", e);
        }

        localStorage.removeItem("token");
        window.location.href = "/";
    }
    useEffect(() => {
        async function getCartItemsCount() {
            try {
                const response = await fetch(buildApiUrl('/cart-items-count'), {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                const data = await response.json();
                setCartItemsCount(data.data.cart_items_count);
            } catch (e) {
                console.error("Cart items count request failed", e);
            }
        }
        getCartItemsCount();
    }, []);

    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

    return (
        <header className="sticky top-0 z-50 w-full border-b border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md">
            <div className="mx-auto flex max-w-7xl h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                {/* Brand Logo */}
                <Link href="/welcome" className="flex items-center gap-2.5 group shrink-0">
                    <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-xs group-hover:scale-105 transition-transform duration-300">
                        <Store className="h-4.5 w-4.5" />
                    </div>
                    <span className="text-lg font-bold tracking-tight text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                        NexusMarket
                    </span>
                </Link>

                {/* Optional Search Bar */}
                {showSearch && setSearchQuery && (
                    <div className="hidden md:flex flex-1 max-w-md mx-8 relative">
                        <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                        <Input
                            placeholder="Search categories, products, shops..."
                            className="pl-9 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 focus:bg-white focus:dark:bg-slate-950 transition-all"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                    </div>
                )}

                {/* Right Side Navigation */}
                <div className="flex items-center gap-2 sm:gap-4">
                    {/* Storefront Home */}
                    {/* <Link href="/welcome">
                        <Button
                            variant="ghost"
                            size="sm"
                            className={`hidden sm:inline-flex text-xs font-semibold ${
                                currentPath === '/welcome'
                                ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/30'
                                : 'text-slate-650 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                            }`}
                        >
                            Storefront
                        </Button>
                    </Link> */}

                    {/* Wishlist Link */}
                    <Link href="/wishlist">
                        <Button
                            variant="ghost"
                            size="icon"
                            className={`relative h-9 w-9 rounded-lg ${currentPath === '/wishlist'
                                ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/30'
                                : 'text-slate-650 dark:text-slate-350 hover:bg-slate-100 dark:hover:bg-slate-900'
                                }`}
                            title="Wishlist"
                        >
                            <Heart className={`h-4.5 w-4.5 ${currentPath === '/wishlist' ? 'fill-current' : ''}`} />
                        </Button>
                    </Link>

                    {/* Cart Link */}
                    <Link href="/cart">
                        <div className="flex items-center gap-2">
                            <Button
                                variant="ghost"
                                size="icon"
                                className={`relative h-9 w-9 rounded-lg ${currentPath === '/cart'
                                    ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/30'
                                    : 'text-slate-655 dark:text-slate-350 hover:bg-slate-100 dark:hover:bg-slate-900'
                                    }`}
                                title="Shopping Cart"
                            >
                                <ShoppingBag className="h-4.5 w-4.5" />
                                {cartItemsCount > 0 && (
                                    <div className="absolute top-1 right-0.5 h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">
                                        {cartItemsCount}
                                    </div>
                                )}
                            </Button>
                        </div>
                    </Link>

                    <div className="h-4 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block" />

                    {/* Dashboard */}
                    {/* <Link href="/dashboard">
                        <Button
                            variant="ghost"
                            size="sm"
                            className="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-650 dark:text-slate-450 hover:text-slate-900 dark:hover:text-slate-200"
                        >
                            <LayoutGrid className="h-3.5 w-3.5" />
                            Dashboard
                        </Button>
                    </Link> */}

                    {/* Log Out */}
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={handleLogout}
                        className="text-xs font-semibold text-slate-650 hover:text-slate-950 dark:text-slate-350 dark:hover:text-white flex items-center gap-1.5 cursor-pointer border-slate-200 dark:border-slate-800"
                    >
                        <LogOut className="h-3.5 w-3.5" />
                        <span className="hidden sm:inline">Log Out</span>
                    </Button>
                </div>
            </div>
        </header>
    );
}
