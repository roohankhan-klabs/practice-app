import { Link } from "@inertiajs/react";
import StorefrontHeader from "@/components/storefront-header";
import { useEffect, useState } from "react";
import { ChevronLeft, ShoppingBag, Tag, Search, ArrowLeft, Heart, Sparkles } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

interface CategoryProps {
    id: string;
}

export default function Category({ id }: CategoryProps) {
    const [category, setCategory] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [searchQuery, setSearchQuery] = useState("");

    useEffect(() => {
        async function getCategoryData() {
            try {
                const response = await fetch(`${API_BASE_URL}/categories/${id}`, {
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                const data = await response.json();
                console.log(data);

                if (data.success) {
                    setCategory(data.data);
                }
            } catch (error) {
                console.error("Error fetching category details:", error);
            } finally {
                setLoading(false);
            }
        }

        if (id) {
            getCategoryData();
        }
    }, [id]);

    if (loading) {
        return (
            <div className="flex min-h-screen items-center justify-center bg-slate-50 dark:bg-slate-950 font-sans">
                <div className="flex flex-col items-center gap-4 text-slate-500">
                    <div className="h-10 w-10 animate-spin rounded-full border-4 border-indigo-600 border-t-transparent"></div>
                    <p className="text-sm font-medium animate-pulse">Loading category details...</p>
                </div>
            </div>
        );
    }

    if (!category) {
        return (
            <div className="flex min-h-screen flex-col items-center justify-center bg-slate-50 dark:bg-slate-950 font-sans p-6 text-center">
                <div className="max-w-md space-y-4">
                    <div className="inline-flex p-3 rounded-full bg-rose-50 dark:bg-rose-950/30 text-rose-500">
                        <Tag className="h-8 w-8" />
                    </div>
                    <h2 className="text-2xl font-bold text-slate-900 dark:text-white">Category Not Found</h2>
                    <p className="text-slate-500 dark:text-slate-400">The category you are looking for does not exist or has been disabled.</p>
                    <Link href="/welcome">
                        <Button className="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white">
                            <ArrowLeft className="mr-2 h-4 w-4" /> Back to Storefront
                        </Button>
                    </Link>
                </div>
            </div>
        );
    }

    // Filter subcategories and their products based on query
    const matchesSearch = (text: string) => text.toLowerCase().includes(searchQuery.toLowerCase());

    const filteredSubcategories = category.subcategories ? category.subcategories.map((sub: any) => {
        const matchingProducts = sub.products ? sub.products.filter((p: any) => 
            matchesSearch(p.name) || (p.description && matchesSearch(p.description))
        ) : [];

        // Return subcategory if it matches search OR contains matching products
        const matchesSubName = matchesSearch(sub.name);
        if (matchesSubName || matchingProducts.length > 0) {
            return {
                ...sub,
                products: matchingProducts
            };
        }
        return null;
    }).filter(Boolean) : [];

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans pb-16">
            <StorefrontHeader searchQuery={searchQuery} setSearchQuery={setSearchQuery} showSearch={true} />
            {/* Header Breadcrumbs / Top Navigation */}
            <div className="border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-950 px-4 sm:px-6 lg:px-8 py-4">
                <div className="mx-auto max-w-5xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <nav className="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <Link href="/welcome" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            Storefront
                        </Link>
                        <ChevronLeft className="h-4 w-4 rotate-180" />
                        <span className="text-slate-900 dark:text-white font-bold">{category.name}</span>
                    </nav>
                    
                    <Link href="/welcome">
                        <Button variant="ghost" size="sm" className="text-slate-600 dark:text-slate-400">
                            <ArrowLeft className="mr-1.5 h-4 w-4" /> Storefront
                        </Button>
                    </Link>
                </div>
            </div>

            {/* Category Hero / Title area */}
            <div className="bg-gradient-to-r from-indigo-50/50 via-white to-slate-50 dark:from-indigo-950/10 dark:via-slate-950 dark:to-slate-950 border-b border-slate-200 dark:border-slate-900 py-10">
                <div className="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-4">
                    <div className="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                        <Tag className="h-5 w-5" />
                        <span className="text-xs font-bold uppercase tracking-wider">Catalog Category</span>
                    </div>
                    <h1 className="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">{category.name}</h1>
                    {category.description && (
                        <p className="text-slate-500 dark:text-slate-400 max-w-3xl text-md leading-relaxed">{category.description}</p>
                    )}

                    {/* Quick Search */}
                    <div className="max-w-md relative mt-4">
                        <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                        <Input
                            placeholder={`Search inside ${category.name}...`}
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-9 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800"
                        />
                    </div>
                </div>
            </div>

            {/* Subcategories list */}
            <div className="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-10 space-y-10">
                {filteredSubcategories.length > 0 ? (
                    filteredSubcategories.map((subcategory: any) => (
                        <div key={subcategory.id} className="space-y-4 border-b border-slate-200 dark:border-slate-900 pb-8 last:border-0 last:pb-0">
                            {/* Subcategory title banner */}
                            <div className="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2">
                                <div className="flex items-center gap-2">
                                    <h2 className="text-2xl font-bold text-slate-800 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <Link href={`/subcategories/${subcategory.id}`}>{subcategory.name}</Link>
                                    </h2>
                                    <Badge variant="secondary" className="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 dark:bg-slate-900 dark:text-slate-400 font-bold uppercase">
                                        {subcategory.products?.length || 0} Products
                                    </Badge>
                                </div>
                                {subcategory.description && (
                                    <p className="text-slate-400 dark:text-slate-500 text-sm max-w-xl italic">{subcategory.description}</p>
                                )}
                            </div>

                            {/* Subcategory products grid */}
                            {subcategory.products && subcategory.products.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 pt-2">
                                    {subcategory.products.map((product: any) => (
                                        <div 
                                            key={product.id}
                                            className="group flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs hover:shadow-md transition-all duration-300"
                                        >
                                            {/* Product placeholder / Image gallery */}
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
                                <div className="text-center py-6 text-slate-400 dark:text-slate-500 italic bg-white dark:bg-slate-900/50 rounded-xl border">
                                    No products found in this subcategory.
                                </div>
                            )}
                        </div>
                    ))
                ) : (
                    <div className="text-center py-16 text-slate-400 italic bg-white dark:bg-slate-900 rounded-xl border">
                        No subcategories matched your search filter.
                    </div>
                )}
            </div>
        </div>
    );
}
