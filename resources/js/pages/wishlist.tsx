import { useState } from "react";

const API_BASE_URL =
    import.meta.env.VITE_API_BASE_URL ?? '/api/v1';

function buildApiUrl(path: string) {
    if (/^https?:\/\//.test(API_BASE_URL)) {
        return `${API_BASE_URL}${path}`;
    }

    return `${window.location.origin}${API_BASE_URL}${path}`;
}

export default function Wishlist() {
    const [wishlist, setWishlist] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(false);

    async function getWishlist() {
        setLoading(true);
        setError(false);
        try {
            response = await fetch(buildApiUrl('/wishlists'), {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`,
                }
            })
        } catch (error) {
            console.error(error);
            setError(true);
        } finally {
            setLoading(false);
        }

        if (response.ok) {
            return response.json();
        }

        throw new Error('Failed to fetch wishlist');
    }

    return <>
        <h1>Wishlist</h1>
    </>;
}
