import { useEffect, useState } from "react";
import type { Product } from "../interfaces/global";

export interface ProductProps {
    id: number;
}
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Product(id: ProductProps) {
    const [product, setProduct] = useState<Product>();

    useEffect(() => {
        async function getProductById() {
            const response = await fetch(`${API_BASE_URL}/products/${id.id}`, {
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            });
            const data = await response.json();
            console.log(data);
            setProduct(data.data);
        }
        getProductById();
    }, []);

    async function addToCart(productId: number, variantId: number) {
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
            alert("Product added to cart successfully");
            setProduct((prevProduct) => {
                if (!prevProduct) {
                    return undefined;
                }

                // Find the variant that was just added
                const updatedVariants = prevProduct.variants.map(variant => {
                    if (variant.id === variantId) {
                        return { ...variant, is_in_cart: true };
                    }

                    return variant;
                });

                return { ...prevProduct, variants: updatedVariants };
            });
        } else {
            alert("Failed to add product to cart");
        }
    }

    return <>
        <div>{product?.name}</div>
        <div>{product?.description}</div>
        <div>{product?.price}</div>
        <div>{product?.variants?.map((variant) => (
            <div key={variant.id} className="m-1">
                {variant.variant_options_summary} - {variant.price}
                <button className="bg-white mx-1 px-2 py-1 text-black" onClick={() => addToCart(product?.id || 0, variant.id)}>
                    {variant.is_in_cart ? "In Cart" : "Add to Cart"}
                </button>
            </div>
        ))}</div>
    </>;
}
