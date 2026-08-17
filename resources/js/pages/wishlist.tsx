import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import { 
    Heart, ShoppingBag, ChevronLeft, 
    ArrowRight, ShoppingCart, Trash2 
} from "lucide-react";
import type { Product } from "../interfaces/global";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import StorefrontHeader from "@/components/storefront-header";

interface WishlistItem {
    id: number;
    user_id: number;
    product_id: number;
    created_at: string;
    updated_at: string;
    product: Product;
}

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? '/api/v1';

function buildApiUrl(path: string) {
    if (/^https?:\/\//.test(API_BASE_URL)) {
        return `${API_BASE_URL}${path}`;
    }
    return `${window.location.origin}${API_BASE_URL}${path}`;
}

export default function Wishlist() {
    const [wishlist, setWishlist] = useState<WishlistItem[]>([]);
    const [loading, setLoading] = useState(true);
    const [addingToCart, setAddingToCart] = useState<number | null>(null);

    async function getWishlist(showLoading = true) {
        if (showLoading) {
            setLoading(true);
        }
        try {
            const response = await fetch(buildApiUrl('/wishlists'), {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`,
                    Accept: 'application/json',
                }
            });
            const data = await response.json();
            if (response.ok && data.success) {
                setWishlist(data.data || []);
            } else {
                toast.error(data.message || "Failed to load wishlist");
            }
        } catch (error) {
            console.error("Error loading wishlist:", error);
            toast.error("Something went wrong");
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        getWishlist();
    }, []);

    async function removeFromWishlist(productId: number) {
        try {
            const response = await fetch(buildApiUrl(`/wishlists`), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
                body: JSON.stringify({
                    product_id: productId,
                }),
            });
            const data = await response.json();
            if (response.ok) {
                toast.success(data.message || "Removed from wishlist");
                getWishlist(false);
            } else {
                toast.error(data.message || "Failed to remove item");
            }
        } catch (error) {
            console.error("Error removing from wishlist", error);
            toast.error("Something went wrong");
        }
    }

    async function addToCartDirect(productId: number) {
        setAddingToCart(productId);
        try {
            const response = await fetch(buildApiUrl('/carts'), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                }),
            });
            const data = await response.json();
            if (response.ok && data.success) {
                toast.success("Added to cart successfully!");
                // Optionally remove from wishlist after successful add-to-cart
                await removeFromWishlist(productId);
            } else {
                toast.error(data.message || "Failed to add to cart.");
            }
        } catch (error) {
            console.error("Error adding to cart:", error);
            toast.error("Something went wrong");
        } finally {
            setAddingToCart(null);
        }
    }

    async function clearWishlist() {
        const productIds = wishlist.map(item => item.product_id);
        if (productIds.length === 0) return;
        try {
            const response = await fetch(buildApiUrl('/wishlists/clear'), {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
                body: JSON.stringify({
                    product_ids: productIds,
                }),
            });
            const data = await response.json();
            if (response.ok) {
                toast.success("Wishlist cleared successfully");
                setWishlist([]);
            } else {
                toast.error(data.message || "Failed to clear wishlist");
            }
        } catch (error) {
            console.error("Error clearing wishlist:", error);
            toast.error("Something went wrong");
        }
    }

    if (loading) {
        return (
            <div className="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans pb-16">
                <StorefrontHeader />
                <div className="flex min-h-[400px] items-center justify-center">
                    <div className="flex flex-col items-center gap-4 text-slate-500">
                        <div className="h-10 w-10 animate-spin rounded-full border-4 border-indigo-600 border-t-transparent"></div>
                        <p className="text-sm font-medium animate-pulse">Loading your wishlist...</p>
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
                        <span className="text-slate-900 dark:text-white font-bold">My Wishlist</span>
                    </nav>

                    {wishlist.length > 0 && (
                        <Button 
                            variant="ghost" 
                            size="sm" 
                            onClick={() => confirm("Are you sure you want to clear your wishlist?") && clearWishlist()}
                            className="text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/20 font-semibold cursor-pointer"
                        >
                            <Trash2 className="mr-1.5 h-3.5 w-3.5" />
                            Clear All
                        </Button>
                    )}
                </div>
            </div>

            <main className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
                {wishlist.length === 0 ? (
                    <div className="text-center py-20 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs max-w-md mx-auto space-y-6">
                        <div className="inline-flex p-4 rounded-full bg-rose-50 dark:bg-rose-950/30 text-rose-500">
                            <Heart className="h-10 w-10 animate-pulse fill-current" />
                        </div>
                        <div className="space-y-2">
                            <h2 className="text-xl font-bold text-slate-900 dark:text-white">Your Wishlist is Empty</h2>
                            <p className="text-slate-550 dark:text-slate-400 text-sm max-w-xs mx-auto">
                                Save items you're interested in here to keep track of them. Let's find some amazing products!
                            </p>
                        </div>
                        <Link href="/welcome">
                            <Button className="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm cursor-pointer">
                                Explore Storefront
                            </Button>
                        </Link>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        {wishlist.map((item) => {
                            const product = item.product;
                            if (!product) return null;

                            const hasVariants = product.variants && product.variants.length > 0;
                            const finalPrice = product.final_price ?? product.price;

                            return (
                                <div 
                                    key={item.id}
                                    className="group flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:shadow-md transition-shadow relative"
                                >
                                    {/* Visual Thumbnail Area */}
                                    <div className="relative aspect-square w-full bg-slate-50 dark:bg-slate-950 flex items-center justify-center overflow-hidden border-b border-slate-150 dark:border-slate-850">
                                        <ShoppingBag className="h-14 w-14 text-slate-200 dark:text-slate-850 group-hover:scale-105 transition-transform duration-300" />
                                        
                                        {/* Heart Toggle Badge (Clicking deletes item) */}
                                        <button 
                                            onClick={() => removeFromWishlist(product.id)}
                                            className="absolute top-3 right-3 flex items-center justify-center h-8.5 w-8.5 rounded-full bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-750 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 cursor-pointer transition-colors"
                                            title="Remove from wishlist"
                                        >
                                            <Heart className="h-4.5 w-4.5 fill-current" />
                                        </button>

                                        {product.is_featured === 1 && (
                                            <Badge className="absolute top-3 left-3 bg-indigo-600 text-white font-medium text-[10px]">
                                                Featured
                                            </Badge>
                                        )}
                                    </div>

                                    {/* Product details */}
                                    <div className="p-4 flex-1 flex flex-col justify-between gap-3">
                                        <div className="space-y-1">
                                            <div className="flex items-center justify-between gap-2">
                                                <span className="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider line-clamp-1">
                                                    Storefront Item
                                                </span>
                                                <span className="font-extrabold text-slate-900 dark:text-white text-sm shrink-0">
                                                    Rs {finalPrice}
                                                </span>
                                            </div>
                                            <h3 className="font-bold text-slate-850 dark:text-slate-100 text-sm line-clamp-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {product.name}
                                            </h3>
                                            <p className="text-xs text-slate-400 line-clamp-2 h-8 leading-relaxed">
                                                {product.description || "No description provided."}
                                            </p>
                                        </div>

                                        {/* Add to Cart button */}
                                        <div className="pt-2 border-t border-slate-100 dark:border-slate-900 mt-1">
                                            {hasVariants ? (
                                                <Link href={`/products/${product.id}`} className="w-full block">
                                                    <Button 
                                                        variant="outline" 
                                                        size="sm" 
                                                        className="w-full text-xs font-semibold cursor-pointer border-slate-200 dark:border-slate-800"
                                                    >
                                                        Select Options
                                                        <ArrowRight className="ml-1.5 h-3.5 w-3.5" />
                                                    </Button>
                                                </Link>
                                            ) : (
                                                <Button 
                                                    onClick={() => addToCartDirect(product.id)}
                                                    variant="default"
                                                    size="sm" 
                                                    className="w-full text-xs font-semibold cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs"
                                                    disabled={addingToCart === product.id}
                                                >
                                                    <ShoppingCart className="mr-1.5 h-3.5 w-3.5" />
                                                    {addingToCart === product.id ? "Adding..." : "Add to Cart"}
                                                </Button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </main>
        </div>
    );
}
