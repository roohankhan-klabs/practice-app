export interface Category {
    id: number;
    name: string;
    description: string;
    image: string;
    sort_order: number;
    status: string;
    created_at: string;
    updated_at: string;
}
export interface SubCategory {
    id: number;
    name: string;
    description: string;
    category_id: number;
    sort_order: number;
    status: string;
    created_at: string;
    updated_at: string;
}
export interface Product {
    created_at: string;
    description: string;
    discount_type: string;
    discount_value: number;
    id: number;
    is_featured: number;
    low_stock_threshold: number;
    name: string;
    price: number;
    rejection_reason: string | null;
    reviewed_at: string;
    reviewed_by: string;
    shipping_price: number;
    shop_id: number;
    show_in_app: number;
    slug: string;
    specifications: string;
    status: string;
    stock: number;
    sub_category_id: number;
    updated_at: string;
    is_in_cart?: boolean;
}
export interface Shop {
    id: number;
    products: Product[];
    address_id: string;
    avg_rating: string;
    commission_percentage: string;
    contact_number: string;
    cover_image: string;
    created_at: string;
    description: string;
    estimated_delivery_time: string;
    facebook: string;
    google_maps_link: string;
    instagram: string;
    is_featured: string;
    logo: string;
    privacy_policy: string;
    refund_policy: string;
    return_policy: string;
    shipping_fee_amount: string;
    shipping_fee_type: string;
    shipping_policy: string;
    shop_name: string;
    status: string;
    terms_of_service: string;
    tiktok: string;
    total_reviews: string;
    user_id: number;
    whatsapp_number: string;
}
