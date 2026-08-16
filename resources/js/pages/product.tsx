import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";
import type { Product as ProductType, Variant } from "../interfaces/global";
import { 
    ChevronLeft, ShoppingBag, ArrowLeft, Heart, Sparkles, 
    Check, ShoppingCart, ShieldCheck, Truck, RotateCcw, AlertTriangle
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { toast } from "sonner";

export interface ProductProps {
    id: number;
}
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Product({ id }: ProductProps) {
    const [product, setProduct] = useState<ProductType>();
    const [loading, setLoading] = useState(true);
    const [selectedVariant, setSelectedVariant] = useState<Variant | null>(null);
    const [addingToCart, setAddingToCart] = useState(false);

    useEffect(() => {
        async function getProductById() {
            try {
                const response = await fetch(`${API_BASE_URL}/products/${id}`, {
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                const data = await response.json();
                console.log(data);
                if (data.success && data.data) {
                    const prod = data.data;
                    setProduct(prod);
                    // Default to first variant
                    if (prod.variants && prod.variants.length > 0) {
                        setSelectedVariant(prod.variants[0]);
                    }
                }
            } catch (error) {
                console.error("Error fetching product details:", error);
                toast.error("Failed to load product details.");
            } finally {
                setLoading(false);
            }
        }
        getProductById();
    }, [id]);

    async function addToCart(productId: number, variantId: number) {
        setAddingToCart(true);
        try {
            const response = await fetch(`${API_BASE_URL}/carts`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: 1,
                }),
            });
            const data = await response.json();
            console.log(data);

            if (data.success) {
                toast.success("Product added to cart successfully!");
                
                // Update local product state to reflect variant is in cart
                setProduct((prevProduct) => {
                    if (!prevProduct) return undefined;

                    const updatedVariants = prevProduct.variants.map(variant => {
                        if (variant.id === variantId) {
                            return { ...variant, is_in_cart: true };
                        }
                        return variant;
                    });

                    return { ...prevProduct, variants: updatedVariants };
                });

                // Update selected variant state
                setSelectedVariant(prev => prev ? { ...prev, is_in_cart: true } : null);
            } else {
                toast.error(data.message || "Failed to add product to cart.");
            }
        } catch (error) {
            console.error("Error adding to cart:", error);
            toast.error("An error occurred. Please try again.");
        } finally {
            setAddingToCart(false);
        }
    }

    if (loading) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-slate-50 dark:bg-slate-950 font-sans">
                <div className="flex flex-col items-center gap-4 text-slate-500">
                    <div className="h-10 w-10 animate-spin rounded-full border-4 border-indigo-600 border-t-transparent"></div>
                    <p className="text-sm font-medium animate-pulse">Loading product details...</p>
                </div>
            </div>
        );
    }

    if (!product) {
        return (
            <div className="flex min-h-screen flex-col items-center justify-center bg-slate-50 dark:bg-slate-950 font-sans p-6 text-center">
                <div className="max-w-md space-y-4">
                    <div className="inline-flex p-3 rounded-full bg-rose-50 dark:bg-rose-950/30 text-rose-500">
                        <AlertTriangle className="h-8 w-8" />
                    </div>
                    <h2 className="text-2xl font-bold text-slate-900 dark:text-white">Product Not Found</h2>
                    <p className="text-slate-500 dark:text-slate-400">The product you are looking for does not exist or has been removed.</p>
                    <Link href="/welcome">
                        <Button className="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white">
                            <ArrowLeft className="mr-2 h-4 w-4" /> Back to Storefront
                        </Button>
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans pb-16">
            {/* Header Breadcrumbs / Top Navigation */}
            <div className="border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-950 px-4 sm:px-6 lg:px-8 py-4">
                <div className="mx-auto max-w-5xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <nav className="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <Link href="/welcome" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            Storefront
                        </Link>
                        <ChevronLeft className="h-4 w-4 rotate-180" />
                        <span className="text-slate-900 dark:text-white font-bold line-clamp-1">{product.name}</span>
                    </nav>
                    
                    <Link href="/welcome">
                        <Button variant="ghost" size="sm" className="text-slate-600 dark:text-slate-400">
                            <ArrowLeft className="mr-1.5 h-4 w-4" /> Storefront
                        </Button>
                    </Link>
                </div>
            </div>

            {/* Product Details Layout */}
            <div className="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-10">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    {/* Left Column: Visual Area */}
                    <div className="space-y-6">
                        <div className="relative aspect-square w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden shadow-xs">
                            <ShoppingBag className="h-28 w-28 text-slate-200 dark:text-slate-850" />
                            {product.is_featured === 1 && (
                                <Badge className="absolute top-4 left-4 bg-indigo-600 text-white font-semibold text-xs py-1 px-2.5">
                                    Featured
                                </Badge>
                            )}
                            <div className="absolute top-4 right-4 flex items-center justify-center h-10 w-10 rounded-full bg-white dark:bg-slate-800 shadow-sm border cursor-pointer hover:text-rose-500 dark:hover:text-rose-400 transition-colors">
                                <Heart className="h-5 w-5" />
                            </div>
                        </div>

                        {/* Store Policies / Info Cards */}
                        <div className="grid grid-cols-3 gap-4">
                            <div className="border rounded-xl p-3 bg-white dark:bg-slate-900 flex flex-col items-center text-center space-y-1">
                                <Truck className="h-5 w-5 text-indigo-500" />
                                <span className="text-[10px] font-bold text-slate-800 dark:text-slate-300">Fast Shipping</span>
                                <span className="text-[9px] text-slate-400">2-4 Days delivery</span>
                            </div>
                            <div className="border rounded-xl p-3 bg-white dark:bg-slate-900 flex flex-col items-center text-center space-y-1">
                                <RotateCcw className="h-5 w-5 text-indigo-500" />
                                <span className="text-[10px] font-bold text-slate-800 dark:text-slate-300">Easy Return</span>
                                <span className="text-[9px] text-slate-400">14 Days policy</span>
                            </div>
                            <div className="border rounded-xl p-3 bg-white dark:bg-slate-900 flex flex-col items-center text-center space-y-1">
                                <ShieldCheck className="h-5 w-5 text-indigo-500" />
                                <span className="text-[10px] font-bold text-slate-800 dark:text-slate-300">Secure Order</span>
                                <span className="text-[9px] text-slate-400">100% Protected</span>
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Information Panel */}
                    <div className="space-y-6 flex flex-col justify-between">
                        <div className="space-y-4">
                            <div className="space-y-2">
                                <Badge className="bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 font-bold text-xs">
                                    In Stock
                                </Badge>
                                <h1 className="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">{product.name}</h1>
                            </div>

                            {/* Price Section */}
                            <div className="flex items-baseline gap-3">
                                <span className="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">
                                    ${selectedVariant ? selectedVariant.price : product.price}
                                </span>
                                {product.discount_value > 0 && (
                                    <>
                                        <span className="text-lg text-slate-400 line-through">
                                            ${(selectedVariant ? selectedVariant.price : product.price) + product.discount_value}
                                        </span>
                                        <Badge className="bg-emerald-500 text-white text-[10px] font-bold px-1.5 py-0.5">
                                            Save {product.discount_type === "percentage" ? `${product.discount_value}%` : `$${product.discount_value}`}
                                        </Badge>
                                    </>
                                )}
                            </div>

                            {/* Description */}
                            <div className="space-y-1.5 border-t border-b py-4 border-slate-200 dark:border-slate-800">
                                <h4 className="text-xs font-bold text-slate-400 uppercase tracking-wider">Description</h4>
                                <p className="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                    {product.description || "This product does not have a detailed description yet."}
                                </p>
                            </div>

                            {/* Variant Selector */}
                            {product.variants && product.variants.length > 0 && (
                                <div className="space-y-3">
                                    <h4 className="text-xs font-bold text-slate-400 uppercase tracking-wider">Select Options</h4>
                                    <div className="grid grid-cols-1 gap-2.5">
                                        {product.variants.map((variant) => {
                                            const isSelected = selectedVariant?.id === variant.id;
                                            return (
                                                <button
                                                    key={variant.id}
                                                    type="button"
                                                    onClick={() => setSelectedVariant(variant)}
                                                    className={`flex items-center justify-between p-3.5 rounded-xl border text-left transition-all duration-200 ${
                                                        isSelected
                                                            ? "border-indigo-600 bg-indigo-50/30 dark:border-indigo-400 dark:bg-indigo-950/20 shadow-xs"
                                                            : "border-slate-200 hover:border-slate-400 dark:border-slate-800 dark:hover:border-slate-700 bg-white dark:bg-slate-900"
                                                    }`}
                                                >
                                                    <div className="flex items-center gap-3">
                                                        <div className={`flex h-5 w-5 shrink-0 items-center justify-center rounded-full border ${
                                                            isSelected ? "border-indigo-600 dark:border-indigo-400 bg-indigo-600 text-white" : "border-slate-300"
                                                        }`}>
                                                            {isSelected && <Check className="h-3 w-3" />}
                                                        </div>
                                                        <span className="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                                            {variant.variant_options_summary || "Standard Option"}
                                                        </span>
                                                    </div>
                                                    <div className="flex items-center gap-2">
                                                        <span className="text-sm font-bold text-slate-900 dark:text-white">${variant.price}</span>
                                                        {variant.is_in_cart && (
                                                            <Badge className="bg-emerald-500 text-white text-[9px] font-bold uppercase tracking-wider px-1">
                                                                In Cart
                                                            </Badge>
                                                        )}
                                                    </div>
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Cart Button Action */}
                        <div className="pt-6 border-t border-slate-200 dark:border-slate-800 mt-6 space-y-3">
                            {selectedVariant ? (
                                <Button
                                    onClick={() => addToCart(product.id, selectedVariant.id)}
                                    className={`w-full py-6 font-bold rounded-xl shadow-md transition-all flex items-center justify-center gap-2 text-md ${
                                        selectedVariant.is_in_cart
                                            ? "bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600"
                                            : "bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white"
                                    }`}
                                    disabled={addingToCart}
                                >
                                    {selectedVariant.is_in_cart ? (
                                        <>
                                            <Check className="h-5 w-5" /> Added to Cart (Add Again)
                                        </>
                                    ) : (
                                        <>
                                            <ShoppingCart className="h-5 w-5" /> Add Option to Cart
                                        </>
                                    )}
                                </Button>
                            ) : (
                                <Button disabled className="w-full py-6 font-bold rounded-xl text-md">
                                    Option Unavailable
                                </Button>
                            )}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    );
}
