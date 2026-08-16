import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";
import type { Shop as ShopType } from "@/interfaces/global";
import { 
    ChevronLeft, ShoppingBag, Store, Search, ArrowLeft, Star, 
    MessageSquare, PhoneCall, Globe, Info, Clock, Truck, FileText, CheckCircle
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

interface ShopProps {
    id: string;
}

export default function Shop({ id }: ShopProps) {
    const [shop, setShop] = useState<ShopType | null>(null);
    const [loading, setLoading] = useState(true);
    const [searchQuery, setSearchQuery] = useState("");
    const [activePolicyTab, setActivePolicyTab] = useState<"about" | "shipping" | "return">("about");

    useEffect(() => {
        async function getShopData() {
            try {
                const response = await fetch(`${API_BASE_URL}/shops/${id}`, {
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                const data = await response.json();
                console.log(data);

                if (data.success) {
                    setShop(data.data);
                }
            } catch (error) {
                console.error("Error fetching shop details:", error);
            } finally {
                setLoading(false);
            }
        }

        if (id) {
            getShopData();
        }
    }, [id]);

    if (loading) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-slate-50 dark:bg-slate-950 font-sans">
                <div className="flex flex-col items-center gap-4 text-slate-500">
                    <div className="h-10 w-10 animate-spin rounded-full border-4 border-indigo-600 border-t-transparent"></div>
                    <p className="text-sm font-medium animate-pulse">Loading seller details...</p>
                </div>
            </div>
        );
    }

    if (!shop) {
        return (
            <div className="flex min-h-screen flex-col items-center justify-center bg-slate-50 dark:bg-slate-950 font-sans p-6 text-center">
                <div className="max-w-md space-y-4">
                    <div className="inline-flex p-3 rounded-full bg-rose-50 dark:bg-rose-950/30 text-rose-500">
                        <Store className="h-8 w-8" />
                    </div>
                    <h2 className="text-2xl font-bold text-slate-900 dark:text-white">Shop Not Found</h2>
                    <p className="text-slate-500 dark:text-slate-400">The store you are trying to visit does not exist or has been suspended.</p>
                    <Link href="/welcome">
                        <Button className="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white">
                            <ArrowLeft className="mr-2 h-4 w-4" /> Back to Storefront
                        </Button>
                    </Link>
                </div>
            </div>
        );
    }

    // Filter products client-side based on search query
    const filteredProducts = shop.products ? shop.products.filter((p: any) => 
        p.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
        (p.description && p.description.toLowerCase().includes(searchQuery.toLowerCase()))
    ) : [];

    const shopInitials = shop.shop_name ? shop.shop_name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() : "SH";

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans pb-16">
            {/* Header Breadcrumbs / Top Navigation */}
            <div className="border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-950 px-4 sm:px-6 lg:px-8 py-4">
                <div className="mx-auto max-w-6xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <nav className="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <Link href="/welcome" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            Storefront
                        </Link>
                        <ChevronLeft className="h-4 w-4 rotate-180" />
                        <span className="text-slate-900 dark:text-white font-bold">{shop.shop_name}</span>
                    </nav>
                    
                    <Link href="/welcome">
                        <Button variant="ghost" size="sm" className="text-slate-600 dark:text-slate-400">
                            <ArrowLeft className="mr-1.5 h-4 w-4" /> Storefront
                        </Button>
                    </Link>
                </div>
            </div>

            {/* Shop Banner Header */}
            <div className="relative bg-gradient-to-r from-indigo-950 via-slate-900 to-violet-950 py-16 text-white border-b border-slate-200 dark:border-slate-900 overflow-hidden">
                <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(99,102,241,0.1),transparent_70%)]" />
                <div className="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center md:items-start gap-6 text-center md:text-left">
                    {/* Shop avatar overlay */}
                    <div className="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl bg-white text-indigo-950 font-extrabold text-3xl shadow-xl border-4 border-slate-800">
                        {shopInitials}
                    </div>

                    <div className="space-y-3 flex-1">
                        <div className="flex flex-wrap items-center justify-center md:justify-start gap-2">
                            <h1 className="text-3xl font-extrabold tracking-tight text-white">{shop.shop_name}</h1>
                            <Badge className="bg-indigo-600 text-white flex items-center gap-1">
                                <CheckCircle className="h-3 w-3" /> Verified Shop
                            </Badge>
                        </div>
                        
                        <div className="flex flex-wrap items-center justify-center md:justify-start gap-4 text-sm text-slate-350">
                            <div className="flex items-center gap-1">
                                <Star className="h-4 w-4 fill-amber-500 text-amber-500" />
                                <span className="font-bold text-white">{shop.avg_rating || "4.8"}</span>
                                <span>({shop.total_reviews || "0"} reviews)</span>
                            </div>
                            {shop.estimated_delivery_time && (
                                <div className="flex items-center gap-1.5">
                                    <Clock className="h-4 w-4 text-indigo-400" />
                                    <span>Delivery: {shop.estimated_delivery_time}</span>
                                </div>
                            )}
                            {shop.shipping_fee_amount && (
                                <div className="flex items-center gap-1.5">
                                    <Truck className="h-4 w-4 text-indigo-400" />
                                    <span>Fee: ${shop.shipping_fee_amount} ({shop.shipping_fee_type || "Flat"})</span>
                                </div>
                            )}
                        </div>

                        {shop.description && (
                            <p className="text-slate-300 max-w-2xl text-sm leading-relaxed line-clamp-2 md:line-clamp-none">
                                {shop.description}
                            </p>
                        )}
                    </div>
                </div>
            </div>

            {/* Shop Details grid layout */}
            <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {/* Left Column: Contact & Information Panel */}
                    <div className="space-y-6 lg:col-span-1">
                        
                        {/* Contact details */}
                        <Card className="border-slate-200 dark:border-slate-800">
                            <CardHeader className="p-5 border-b pb-4">
                                <CardTitle className="text-md font-bold flex items-center gap-2">
                                    <PhoneCall className="h-4 w-4 text-indigo-500" /> Contact Seller
                                </CardTitle>
                                <CardDescription>Direct communications with store manager</CardDescription>
                            </CardHeader>
                            <CardContent className="p-5 space-y-4">
                                {shop.whatsapp_number && (
                                    <a 
                                        href={`https://wa.me/${shop.whatsapp_number}`} 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        className="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white py-2 px-4 rounded-lg font-bold text-sm shadow-sm transition-all"
                                    >
                                        <MessageSquare className="h-4 w-4" /> Message on WhatsApp
                                    </a>
                                )}
                                
                                <div className="space-y-2.5 text-sm pt-2">
                                    {shop.contact_number && (
                                        <div className="flex justify-between border-b pb-2">
                                            <span className="text-slate-400">Phone:</span>
                                            <span className="font-semibold">{shop.contact_number}</span>
                                        </div>
                                    )}
                                    {shop.whatsapp_number && (
                                        <div className="flex justify-between border-b pb-2">
                                            <span className="text-slate-400">WhatsApp:</span>
                                            <span className="font-semibold">+{shop.whatsapp_number}</span>
                                        </div>
                                    )}
                                    {shop.estimated_delivery_time && (
                                        <div className="flex justify-between border-b pb-2">
                                            <span className="text-slate-400">Delivery Est:</span>
                                            <span className="font-semibold">{shop.estimated_delivery_time}</span>
                                        </div>
                                    )}
                                    <div className="flex justify-between pb-1">
                                        <span className="text-slate-400">Social Presence:</span>
                                        <div className="flex gap-2 text-indigo-600 dark:text-indigo-400 font-semibold">
                                            {shop.facebook && <a href={shop.facebook} target="_blank" className="hover:underline">FB</a>}
                                            {shop.instagram && <a href={shop.instagram} target="_blank" className="hover:underline">IG</a>}
                                            {shop.tiktok && <a href={shop.tiktok} target="_blank" className="hover:underline">TT</a>}
                                            {!shop.facebook && !shop.instagram && !shop.tiktok && <span className="text-slate-400 font-normal">None</span>}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Policies Tabbed card */}
                        <Card className="border-slate-200 dark:border-slate-800">
                            <div className="flex border-b border-slate-100 dark:border-slate-800 p-1 bg-slate-50 dark:bg-slate-900 rounded-t-lg">
                                <button
                                    onClick={() => setActivePolicyTab("about")}
                                    className={`flex-1 py-2 text-xs font-semibold rounded-md transition-all ${
                                        activePolicyTab === "about"
                                            ? "bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs"
                                            : "text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                    }`}
                                >
                                    Seller Info
                                </button>
                                <button
                                    onClick={() => setActivePolicyTab("shipping")}
                                    className={`flex-1 py-2 text-xs font-semibold rounded-md transition-all ${
                                        activePolicyTab === "shipping"
                                            ? "bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs"
                                            : "text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                    }`}
                                >
                                    Shipping
                                </button>
                                <button
                                    onClick={() => setActivePolicyTab("return")}
                                    className={`flex-1 py-2 text-xs font-semibold rounded-md transition-all ${
                                        activePolicyTab === "return"
                                            ? "bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs"
                                            : "text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                    }`}
                                >
                                    Returns
                                </button>
                            </div>
                            <CardContent className="p-5 text-sm min-h-[160px]">
                                {activePolicyTab === "about" && (
                                    <div className="space-y-2">
                                        <h4 className="font-bold flex items-center gap-1.5 text-slate-800 dark:text-slate-200">
                                            <Info className="h-4 w-4 text-indigo-500" /> About store
                                        </h4>
                                        <p className="text-slate-550 dark:text-slate-400 leading-relaxed">
                                            {shop.description || "Welcome to our store. We feature hand-picked products custom variant configurations and direct support."}
                                        </p>
                                    </div>
                                )}
                                {activePolicyTab === "shipping" && (
                                    <div className="space-y-2">
                                        <h4 className="font-bold flex items-center gap-1.5 text-slate-800 dark:text-slate-200">
                                            <Truck className="h-4 w-4 text-indigo-500" /> Shipping Policy
                                        </h4>
                                        <p className="text-slate-550 dark:text-slate-400 leading-relaxed">
                                            {shop.shipping_policy || "We fulfill items within 24 hours of order placement. Delivery times and shipping fees vary depending on location and parcel size."}
                                        </p>
                                    </div>
                                )}
                                {activePolicyTab === "return" && (
                                    <div className="space-y-2">
                                        <h4 className="font-bold flex items-center gap-1.5 text-slate-800 dark:text-slate-200">
                                            <FileText className="h-4 w-4 text-indigo-500" /> Return & Refund Policy
                                        </h4>
                                        <p className="text-slate-550 dark:text-slate-400 leading-relaxed">
                                            {shop.return_policy || shop.refund_policy || "We accept returns on unused merchandise in original packaging within 14 days of purchase. Contact customer support."}
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Right Column: Catalog List Area */}
                    <div className="space-y-6 lg:col-span-2">
                        
                        {/* Header bar and search */}
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-250 dark:border-slate-800 pb-4">
                            <h2 className="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Shop Catalog</h2>
                            
                            <div className="relative w-full sm:max-w-xs">
                                <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                                <Input
                                    placeholder="Search products in this shop..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    className="pl-9 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800"
                                />
                            </div>
                        </div>

                        {/* Products list grid */}
                        {filteredProducts.length > 0 ? (
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                {filteredProducts.map((product: any) => (
                                    <div 
                                        key={product.id}
                                        className="group flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs hover:shadow-md transition-all duration-300"
                                    >
                                        {/* Image Area */}
                                        <div className="relative aspect-square w-full bg-slate-50 dark:bg-slate-950 flex items-center justify-center overflow-hidden border-b border-slate-100 dark:border-slate-900">
                                            <ShoppingBag className="h-12 w-12 text-slate-200 dark:text-slate-800 group-hover:scale-110 transition-transform duration-300" />
                                            {product.stock <= 5 && product.stock > 0 && (
                                                <Badge className="absolute top-3 right-3 bg-amber-500 text-white font-medium text-xs">
                                                    Low Stock
                                                </Badge>
                                            )}
                                        </div>

                                        {/* Details */}
                                        <div className="p-4 flex-1 flex flex-col justify-between gap-3">
                                            <div className="space-y-1">
                                                <div className="flex items-center justify-between">
                                                    <span className="font-bold text-slate-900 dark:text-white text-md">
                                                        ${product.price}
                                                    </span>
                                                </div>
                                                <h3 className="font-bold text-slate-800 dark:text-slate-100 text-sm line-clamp-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {product.name}
                                                </h3>
                                                {product.description && (
                                                    <p className="text-xs text-slate-400 line-clamp-2 h-8">
                                                        {product.description}
                                                    </p>
                                                )}
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
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-16 text-slate-400 italic bg-white dark:bg-slate-900 rounded-xl border">
                                This seller does not have any items listed matching your search.
                            </div>
                        )}
                    </div>
                    
                </div>
            </div>
        </div>
    );
}
