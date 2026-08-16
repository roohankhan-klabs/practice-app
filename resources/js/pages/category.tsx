import { useEffect, useState } from "react";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

interface CategoryProps {
    id: string;
}

export default function Category({ id }: CategoryProps) {
    const [category, setCategory] = useState<any>(null);
    const [loading, setLoading] = useState(true);

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
            <div className="p-8 max-w-4xl mx-auto text-center text-gray-500">
                Loading category details...
            </div>
        );
    }

    if (!category) {
        return (
            <div className="p-8 max-w-4xl mx-auto text-center text-red-500 font-medium">
                Category not found.
            </div>
        );
    }

    return (
        <div className="p-8 max-w-4xl mx-auto">
            <h1 className="text-3xl font-bold mb-2 text-slate-800">{category.name}</h1>
            {category.description && (
                <p className="text-gray-600 mb-6 text-lg">{category.description}</p>
            )}

            <h2 className="text-2xl font-semibold mb-4 text-slate-700 border-b pb-2">Subcategories</h2>
            <div className="grid gap-4">
                {category.subcategories && category.subcategories.length > 0 ? (
                    category.subcategories.map((subcategory: any) => (
                        <div key={subcategory.id} className="border p-5 rounded-lg shadow-sm bg-white">
                            <h3 className="text-xl font-semibold text-slate-800 mb-1">{subcategory.name}</h3>
                            {subcategory.description && (
                                <p className="text-gray-500 mb-4 text-sm">{subcategory.description}</p>
                            )}
                            
                            <div>
                                <h4 className="font-semibold text-xs text-gray-400 uppercase tracking-wider mb-2">Products</h4>
                                {subcategory.products && subcategory.products.length > 0 ? (
                                    <div className="grid gap-2 sm:grid-cols-2">
                                        {subcategory.products.map((product: any) => (
                                            <div key={product.id} className="p-3 bg-slate-50 border border-slate-100 rounded-md flex justify-between items-center">
                                                <span className="font-medium text-slate-700">{product.name}</span>
                                                <span className="font-bold text-slate-900">${product.price}</span>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-gray-400 text-xs italic">No products available in this subcategory.</p>
                                )}
                            </div>
                        </div>
                    ))
                ) : (
                    <p className="text-gray-500 italic">No subcategories found for this category.</p>
                )}
            </div>
        </div>
    );
}
