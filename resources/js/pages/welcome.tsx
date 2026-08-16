import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";
import type { Category } from "../interfaces/global";
import type { SubCategory } from "../interfaces/global";
import type { Product } from "../interfaces/global";
import type { Shop } from "../interfaces/global";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Welcome() {
    const [categories, setCategories] = useState<Category[]>([]);
    const [subcategories, setSubcategories] = useState<SubCategory[]>([]);
    const [products, setProducts] = useState<Product[]>([]);
    const [shops, setShops] = useState<Shop[]>([]);

    async function addToCart(productId: number) {
        const response = await fetch(`${API_BASE_URL}/carts`, {
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
        console.log(data);

        if (data.success) {
            alert("Product added to cart successfully");
            setProducts((prevProducts) =>
                prevProducts.map((p) =>
                    p.id === productId ? { ...p, is_in_cart: true } : p
                )
            );
        } else {
            alert("Failed to add product to cart");
        }
    }

    useEffect(() => {
        async function getInitData() {
            const response = await fetch(`${API_BASE_URL}/init`, {
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            });
            const data = await response.json();
            console.log(data);
            setCategories(data.data.categories);
            setSubcategories(data.data.subcategories);
            setProducts(data.data.products);
            setShops(data.data.shops);
        }
        getInitData();
    }, []);

    return (
        <div className="p-8 max-w-6xl mx-auto">
            <h1 className="text-3xl font-bold mb-6 text-slate-800">Welcome</h1>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="border p-4 rounded-lg bg-white shadow-sm">
                    <div className="border-b pb-2 font-semibold text-lg text-slate-700 mb-3">Categories</div>
                    <div className="space-y-2">
                        {categories.map((category) => (
                            <Link key={category.id} href={`/categories/${category.id}`} className="block text-blue-600 hover:underline">
                                {category.name}
                            </Link>
                        ))}
                    </div>
                </div>
                <div className="border p-4 rounded-lg bg-white shadow-sm">
                    <div className="border-b pb-2 font-semibold text-lg text-slate-700 mb-3">SubCategories</div>
                    <div className="space-y-2">
                        {subcategories.map((subcategory) => (
                            <Link key={subcategory.id} href={`/subcategories/${subcategory.id}`} className="block text-blue-600 hover:underline">
                                {subcategory.name}
                            </Link>
                        ))}
                    </div>
                </div>
                <div className="border p-4 rounded-lg bg-white shadow-sm">
                    <div className="border-b pb-2 font-semibold text-lg text-slate-700 mb-3">Products</div>
                    <div className="space-y-2">
                        {products.map((product) => (
                            <div key={product.id} className="flex items-center justify-between">
                                <Link href={`/products/${product.id}`} className="block text-blue-600 hover:underline">
                                    {product.name}
                                </Link>
                                <button
                                    onClick={() => addToCart(product.id)}
                                    className="bg-green-500 text-black border border-green-500 hover:bg-green-600 hover:text-white rounded-md px-2 py-1 cursor-pointer"
                                >
                                    {product.is_in_cart ? "In Cart" : "Add to Cart"}
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
                <div className="border p-4 rounded-lg bg-white shadow-sm">
                    <div className="border-b pb-2 font-semibold text-lg text-slate-700 mb-3">Shops</div>
                    <div className="space-y-2">
                        {shops.map((shop) => (
                            <Link key={shop.id} href={`/shops/${shop.id}`} className="block text-blue-600 hover:underline">
                                {shop.shop_name}
                            </Link>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
