import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";
import type { Category, SubCategory, Product, Shop } from "../interfaces/global";
import { 
    Search, Store, ShoppingBag, FolderOpen, LogOut, ArrowRight, 
    Star, MessageSquare, PhoneCall, Heart, Sparkles, Compass
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Welcome() {
    const [categories, setCategories] = useState<Category[]>([]);
    const [subcategories, setSubcategories] = useState<SubCategory[]>([]);
    const [products, setProducts] = useState<Product[]>([]);
    const [shops, setShops] = useState<Shop[]>([]);
    const [loading, setLoading] = useState(true);
    const [searchQuery, setSearchQuery] = useState("");

    useEffect(() => {
        async function getInitData() {
            try {
                const response = await fetch(`${API_BASE_URL}/init`, {
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                if (response.status === 401) {
                    // Token expired or invalid
                    localStorage.removeItem("token");
                    window.location.href = "/";
                    return;
                }
                const data = await response.json();
                console.log(data);
                setCategories(data.data.categories || []);
                setSubcategories(data.data.subcategories || []);
                setProducts(data.data.products || []);
                setShops(data.data.shops || []);
            } catch (error) {
                console.error("Error loading welcome page data:", error);
            } finally {
                setLoading(false);
            }
        }
        getInitData();
    }, []);

    async function handleLogout() {
        try {
            await fetch(`${API_BASE_URL}/logout`, {
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

    // Client-side search filtering
    const filteredCategories = categories.filter(c => 
        c.name.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const filteredSubcategories = subcategories.filter(s => 
        s.name.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const filteredShops = shops.filter(s => 
        s.shop_name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        s.description?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const filteredProducts = products.filter(p => 
        p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        p.description?.toLowerCase().includes(searchQuery.toLowerCase())
    );

    if (loading) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-slate-50 dark:bg-slate-950 font-sans">
                <div className="flex flex-col items-center gap-4 text-slate-500">
                    <div className="h-10 w-10 animate-spin rounded-full border-4 border-indigo-600 border-t-transparent"></div>
                    <p className="text-sm font-medium animate-pulse">Loading curated marketplace...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans">
            {/* Header Navigation */}
            <header className="sticky top-0 z-50 w-full border-b border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md">
                <div className="mx-auto flex max-w-7xl h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center gap-2.5">
                        <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-xs">
                            <Store className="h-4.5 w-4.5" />
                        </div>
                        <span className="text-lg font-bold tracking-tight text-slate-900 dark:text-white">
                            NexusMarket
                        </span>
                    </div>

                    {/* Inline Search Bar on Header (hidden on mobile, shown on desktop) */}
                    <div className="hidden md:flex flex-1 max-w-md mx-8 relative">
                        <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                        <Input
                            placeholder="Search categories, products, shops..."
                            className="pl-9 bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 focus:bg-white focus:dark:bg-slate-950"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                    </div>

                    <div className="flex items-center gap-3">
                        <Link href="/dashboard">
                            <Button variant="ghost" size="sm" className="hidden sm:inline-flex">
                                Dashboard
                            </Button>
                        </Link>
                        <Button 
                            variant="outline" 
                            size="sm" 
                            onClick={handleLogout}
                            className="text-slate-600 hover:text-slate-950 dark:text-slate-300 dark:hover:text-white flex items-center gap-1.5"
                        >
                            <LogOut className="h-3.5 w-3.5" />
                            Log Out
                        </Button>
                    </div>
                </div>
            </header>

            {/* Hero Section */}
            <section className="relative overflow-hidden py-16 sm:py-24 bg-gradient-to-b from-indigo-50/50 via-white to-slate-50 dark:from-indigo-950/20 dark:via-slate-950 dark:to-slate-950 border-b border-slate-200 dark:border-slate-900">
                <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(99,102,241,0.05),transparent_70%)]" />
                <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center space-y-6">
                    <span className="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/10 dark:bg-indigo-500/20 px-3 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-300 ring-1 ring-inset ring-indigo-500/20">
                        <Sparkles className="h-3.5 w-3.5" /> Curated Independent Catalog
                    </span>
                    <h1 className="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl text-slate-900 dark:text-white leading-tight">
                        Your Nexus for <span className="bg-gradient-to-r from-indigo-600 to-violet-500 bg-clip-text text-transparent">Curated Goods</span>
                    </h1>
                    <p className="mx-auto max-w-2xl text-slate-500 dark:text-slate-400 text-lg sm:text-xl">
                        Shop directly from verified local stores. Custom configurations, transparent pricing, and direct communications.
                    </p>

                    {/* Search on mobile & hero */}
                    <div className="mx-auto max-w-xl relative mt-4">
                        <Search className="absolute left-3.5 top-3.5 h-5 w-5 text-slate-400" />
                        <Input
                            placeholder="What are you looking for today?"
                            className="pl-11 h-12 text-md shadow-sm bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 rounded-xl"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                    </div>
                </div>
            </section>

            {/* Main Content Layout */}
            <main className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 space-y-16">

                {/* Section 1: Categories */}
                <div className="space-y-6">
                    <div className="flex items-center justify-between border-b border-slate-200 dark:border-slate-900 pb-3">
                        <div className="flex items-center gap-2">
                            <FolderOpen className="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                            <h2 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Explore Categories</h2>
                        </div>
                        <span className="text-xs font-medium text-slate-400 uppercase tracking-wider">{filteredCategories.length} Categories</span>
                    </div>

                    {filteredCategories.length > 0 ? (
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            {filteredCategories.map((category) => (
                                <Link 
                                    key={category.id} 
                                    href={`/categories/${category.id}`}
                                    className="group relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:shadow-md hover:border-indigo-500/50 dark:hover:border-indigo-400/50 transition-all duration-300 flex flex-col justify-between min-h-[120px]"
                                >
                                    <div className="absolute right-[-10px] top-[-10px] size-16 bg-indigo-50 dark:bg-indigo-950/30 rounded-full scale-0 group-hover:scale-100 transition-transform duration-300 -z-10" />
                                    <div className="space-y-1">
                                        <h3 className="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {category.name}
                                        </h3>
                                        <p className="text-xs text-slate-400 line-clamp-2">{category.description || "Browse collections."}</p>
                                    </div>
                                    <div className="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 font-semibold mt-3 group-hover:translate-x-1 transition-transform">
                                        View Products <ArrowRight className="h-3 w-3" />
                                    </div>
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-8 text-slate-400 italic bg-white dark:bg-slate-900 rounded-xl border">
                            No categories matched your search.
                        </div>
                    )}
                </div>

                {/* Section 2: Shops */}
                <div className="space-y-6">
                    <div className="flex items-center justify-between border-b border-slate-200 dark:border-slate-900 pb-3">
                        <div className="flex items-center gap-2">
                            <Store className="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                            <h2 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Featured Shops</h2>
                        </div>
                        <span className="text-xs font-medium text-slate-400 uppercase tracking-wider">{filteredShops.length} Active Sellers</span>
                    </div>

                    {filteredShops.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {filteredShops.map((shop) => {
                                const initials = shop.shop_name ? shop.shop_name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() : "SH";
                                return (
                                    <Card key={shop.id} className="overflow-hidden border-slate-200 dark:border-slate-800 hover:shadow-md transition-shadow flex flex-col justify-between">
                                        <CardHeader className="p-5 flex flex-row items-start gap-4 space-y-0">
                                            {/* Shop Initials Logo Placeholder */}
                                            <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white font-bold text-lg shadow-inner">
                                                {initials}
                                            </div>
                                            <div className="space-y-1">
                                                <CardTitle className="text-lg font-bold text-slate-900 dark:text-white line-clamp-1">{shop.shop_name}</CardTitle>
                                                <div className="flex items-center gap-2 text-xs text-slate-400">
                                                    <span className="flex items-center gap-0.5 text-amber-500">
                                                        <Star className="h-3 w-3 fill-amber-500" /> {shop.avg_rating || "4.8"}
                                                    </span>
                                                    <span>•</span>
                                                    <span>{shop.total_reviews || "0"} reviews</span>
                                                </div>
                                            </div>
                                        </CardHeader>
                                        <CardContent className="px-5 pb-4 pt-0 flex-1">
                                            <p className="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 h-10 mb-4">
                                                {shop.description || "No description available for this seller. Explore their products page."}
                                            </p>
                                            <div className="flex flex-wrap gap-2 mb-4">
                                                {shop.whatsapp_number && (
                                                    <a 
                                                        href={`https://wa.me/${shop.whatsapp_number}`} 
                                                        target="_blank" 
                                                        rel="noopener noreferrer"
                                                        className="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-2 py-1 rounded-md font-medium hover:underline"
                                                    >
                                                        <PhoneCall className="h-3 w-3" /> WhatsApp Contact
                                                    </a>
                                                )}
                                                {shop.estimated_delivery_time && (
                                                    <Badge variant="secondary" className="text-xs bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                        Delivery: {shop.estimated_delivery_time}
                                                    </Badge>
                                                )}
                                            </div>
                                        </CardContent>
                                        <div className="border-t border-slate-100 dark:border-slate-800 p-4 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center">
                                            <span className="text-xs text-slate-400">Products: {shop.products?.length || 0}</span>
                                            <Link href={`/shops/${shop.id}`}>
                                                <Button size="sm" className="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs">
                                                    Visit Shop
                                                </Button>
                                            </Link>
                                        </div>
                                    </Card>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="text-center py-12 text-slate-400 italic bg-white dark:bg-slate-900 rounded-xl border">
                            No shops matched your search query.
                        </div>
                    )}
                </div>

                {/* Section 3: Products */}
                <div className="space-y-6">
                    <div className="flex items-center justify-between border-b border-slate-200 dark:border-slate-900 pb-3">
                        <div className="flex items-center gap-2">
                            <ShoppingBag className="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                            <h2 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Curated Products</h2>
                        </div>
                        <span className="text-xs font-medium text-slate-400 uppercase tracking-wider">{filteredProducts.length} Items Listed</span>
                    </div>

                    {filteredProducts.length > 0 ? (
                        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            {filteredProducts.map((product) => {
                                // Match the product's shop to display shop name
                                const productShop = shops.find(s => s.id === product.shop_id);
                                return (
                                    <div 
                                        key={product.id}
                                        className="group flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:shadow-md transition-shadow"
                                    >
                                        {/* Product image area / placeholder */}
                                        <div className="relative aspect-square w-full bg-slate-100 dark:bg-slate-950 flex items-center justify-center overflow-hidden border-b border-slate-100 dark:border-slate-900">
                                            <ShoppingBag className="h-12 w-12 text-slate-300 dark:text-slate-800 group-hover:scale-110 transition-transform duration-300" />
                                            {product.is_featured === 1 && (
                                                <Badge className="absolute top-3 left-3 bg-indigo-600 text-white font-medium text-xs">
                                                    Featured
                                                </Badge>
                                            )}
                                            {product.stock <= 5 && product.stock > 0 && (
                                                <Badge className="absolute top-3 right-3 bg-amber-500 text-white font-medium text-xs">
                                                    Only {product.stock} left
                                                </Badge>
                                            )}
                                        </div>

                                        <div className="p-4 flex-1 flex flex-col justify-between gap-3">
                                            <div className="space-y-1">
                                                <div className="flex items-center justify-between">
                                                    <span className="text-xs text-indigo-600 dark:text-indigo-400 font-semibold uppercase tracking-wider line-clamp-1">
                                                        {productShop?.shop_name || "Seller Store"}
                                                    </span>
                                                    <span className="font-bold text-slate-900 dark:text-white text-md">
                                                        ${product.price}
                                                    </span>
                                                </div>
                                                <h3 className="font-bold text-slate-850 dark:text-slate-100 text-sm line-clamp-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {product.name}
                                                </h3>
                                                <p className="text-xs text-slate-400 line-clamp-2 h-8">
                                                    {product.description || "No description provided."}
                                                </p>
                                            </div>

                                            <div className="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-900 mt-2">
                                                <Link href={`/products/${product.id}`} className="flex-1">
                                                    <Button variant="outline" size="sm" className="w-full text-xs font-semibold">
                                                        Details
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="text-center py-12 text-slate-400 italic bg-white dark:bg-slate-900 rounded-xl border">
                            No products matched your search.
                        </div>
                    )}
                </div>

            </main>
        </div>
    );
}
