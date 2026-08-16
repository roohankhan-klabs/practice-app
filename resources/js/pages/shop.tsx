import { useEffect, useState } from "react";
import type { Shop } from "@/interfaces/global";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

interface ShopProps {
    id: string;
}

export default function Shop({ id }: ShopProps) {
    const [shop, setShop] = useState<Shop | null>(null);
    const [loading, setLoading] = useState(true);

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
            <div className="p-8 max-w-4xl mx-auto text-center text-gray-500">
                Loading shop details...
            </div>
        );
    }

    if (!shop) {
        return (
            <div className="p-8 max-w-4xl mx-auto text-center text-red-500 font-medium">
                Shop not found.
            </div>
        );
    }

    return (
        <div className="p-8 max-w-4xl mx-auto">
            <h1 className="text-3xl font-bold mb-2 text-slate-800">{shop.shop_name}</h1>
            {shop.description && (
                <p className="text-gray-600 mb-6 text-lg">{shop.description}</p>
            )}

            <h2 className="text-2xl font-semibold mb-4 text-slate-700 border-b pb-2">Products</h2>
            <div className="grid gap-4">
                {shop.products && shop.products.length > 0 ? (
                    shop.products.map((product: any) => (
                        <div key={product.id} className="p-3 bg-slate-50 border border-slate-100 rounded-md flex justify-between items-center">
                            <span className="font-medium text-slate-700">{product.name}</span>
                            <span className="font-bold text-slate-900">${product.price}</span>
                        </div>
                    ))
                ) : (
                    <p className="text-gray-400 text-xs italic">No products available in this shop.</p>
                )}
            </div>
        </div>
    );
}
