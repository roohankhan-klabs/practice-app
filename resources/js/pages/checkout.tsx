import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import { 
    ChevronLeft, ShoppingBag, CreditCard, MapPin, 
    Plus, Lock, ShieldCheck, ShoppingCart, Loader2 
} from "lucide-react";
import type { CheckoutData, Bill, CartItem, Address as AddressType } from "@/interfaces/global";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import StorefrontHeader from "@/components/storefront-header";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

interface Country {
    id: number;
    name: string;
    code: string;
}

interface State {
    id: number;
    name: string;
    country_id: number;
}

interface City {
    id: number;
    name: string;
    state_id: number;
}

export default function Checkout() {
    const [loading, setLoading] = useState<boolean>(true);
    const [checkoutProcessing, setCheckoutProcessing] = useState<boolean>(false);
    const [checkoutData, setCheckoutData] = useState<CheckoutData>();
    const [selectedCartItems, setSelectedCartItems] = useState<number[]>([]);
    const [selectedPaymentMethodId, setSelectedPaymentMethodId] = useState<number | null>(null);
    const [selectedAddressId, setSelectedAddressId] = useState<number | null>(null);
    const [bill, setBill] = useState<Bill | null>(null);

    // Address Creation Form State
    const [showAddAddress, setShowAddAddress] = useState<boolean>(false);
    const [addressLine1, setAddressLine1] = useState<string>("");
    const [addressLine2, setAddressLine2] = useState<string>("");
    const [preferredContact, setPreferredContact] = useState<string>("");
    const [postalCode, setPostalCode] = useState<string>("");
    const [countryId, setCountryId] = useState<number | "">("");
    const [stateId, setStateId] = useState<number | "">("");
    const [cityId, setCityId] = useState<number | "">("");
    const [isDefault, setIsDefault] = useState<boolean>(true);
    
    // Address dropdowns data
    const [countries, setCountries] = useState<Country[]>([]);
    const [states, setStates] = useState<State[]>([]);
    const [cities, setCities] = useState<City[]>([]);
    const [loadingStates, setLoadingStates] = useState<boolean>(false);
    const [loadingCities, setLoadingCities] = useState<boolean>(false);
    const [addingAddress, setAddingAddress] = useState<boolean>(false);

    async function fetchCheckoutData(showLoader = true) {
        if (showLoader) {
            setLoading(true);
        }
        const params = new URLSearchParams(window.location.search);
        const itemsParam = params.get('items');

        if (!itemsParam) {
            toast.error('No items found for checkout');
            setLoading(false);
            return;
        }

        const cartItemIds = itemsParam.split(',').map(Number);

        try {
            const response = await fetch(`${API_BASE_URL}/ready-for-checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
                body: JSON.stringify({ cart_item_ids: cartItemIds }),
            });
            const data = await response.json();

            if (data.success && data.data) {
                setCheckoutData(data.data);
                setBill(data.data.bill);
                setSelectedCartItems(cartItemIds);

                // Set default payment method safely
                if (data.data.payment_methods?.length > 0) {
                    const defaultPm = data.data.payment_methods[3] ?? data.data.payment_methods[0];
                    setSelectedPaymentMethodId(defaultPm.id);
                }

                // Set default address
                if (data.data.addresses?.length > 0) {
                    const defaultAddr = data.data.addresses.find((addr: AddressType) => addr.is_default) ?? data.data.addresses[0];
                    setSelectedAddressId(defaultAddr.id);
                }
            } else {
                toast.error(data.message || "Failed to load checkout details");
            }
        } catch (error) {
            console.error('Error fetching checkout details:', error);
            toast.error("Something went wrong");
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        fetchCheckoutData();
    }, []);

    // Load countries when user opens address form
    useEffect(() => {
        if (showAddAddress && countries.length === 0) {
            async function fetchCountries() {
                try {
                    const res = await fetch(`${API_BASE_URL}/countries`, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                            Accept: "application/json"
                        }
                    });
                    const result = await res.json();
                    if (res.ok) {
                        setCountries(result.data || []);
                    }
                } catch (e) {
                    console.error("Error fetching countries:", e);
                }
            }
            fetchCountries();
        }
    }, [showAddAddress]);

    // Load states when country changes
    useEffect(() => {
        if (countryId) {
            async function fetchStates() {
                setLoadingStates(true);
                setStateId("");
                setCityId("");
                setCities([]);
                try {
                    const res = await fetch(`${API_BASE_URL}/states/${countryId}`, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                            Accept: "application/json"
                        }
                    });
                    const result = await res.json();
                    if (res.ok) {
                        setStates(result.data || []);
                    }
                } catch (e) {
                    console.error("Error fetching states:", e);
                } finally {
                    setLoadingStates(false);
                }
            }
            fetchStates();
        } else {
            setStates([]);
            setStateId("");
            setCityId("");
            setCities([]);
        }
    }, [countryId]);

    // Load cities when state changes
    useEffect(() => {
        if (stateId) {
            async function fetchCities() {
                setLoadingCities(true);
                setCityId("");
                try {
                    const res = await fetch(`${API_BASE_URL}/cities/${stateId}`, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("token")}`,
                            Accept: "application/json"
                        }
                    });
                    const result = await res.json();
                    if (res.ok) {
                        setCities(result.data || []);
                    }
                } catch (e) {
                    console.error("Error fetching cities:", e);
                } finally {
                    setLoadingCities(false);
                }
            }
            fetchCities();
        } else {
            setCities([]);
            setCityId("");
        }
    }, [stateId]);

    async function handleAddAddress(e: React.FormEvent) {
        e.preventDefault();
        if (!addressLine1 || !preferredContact || !countryId || !stateId || !cityId) {
            toast.error("Please fill in all required fields.");
            return;
        }

        setAddingAddress(true);
        try {
            const response = await fetch(`${API_BASE_URL}/addresses`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
                body: JSON.stringify({
                    address_line_1: addressLine1,
                    address_line_2: addressLine2 || null,
                    preffered_contact_number: preferredContact,
                    postal_code: postalCode || null,
                    city_id: Number(cityId),
                    state_id: Number(stateId),
                    country_id: Number(countryId),
                    is_default: isDefault,
                })
            });

            const data = await response.json();
            if (response.ok && data.success) {
                toast.success("Address added successfully!");
                // Clear fields
                setAddressLine1("");
                setAddressLine2("");
                setPreferredContact("");
                setPostalCode("");
                setCountryId("");
                setStateId("");
                setCityId("");
                setShowAddAddress(false);
                
                // Select new address and reload checkout list
                setSelectedAddressId(data.data.id);
                fetchCheckoutData(false);
            } else {
                toast.error(data.message || "Failed to add address.");
            }
        } catch (error) {
            console.error("Error adding address:", error);
            toast.error("Something went wrong");
        } finally {
            setAddingAddress(false);
        }
    }

    async function checkout() {
        if (!selectedAddressId) {
            toast.error("Please select a shipping address.");
            return;
        }
        if (!selectedPaymentMethodId) {
            toast.error("Please select a payment method.");
            return;
        }

        setCheckoutProcessing(true);
        try {
            const response = await fetch(`${API_BASE_URL}/checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                },
                body: JSON.stringify({
                    cart_item_ids: selectedCartItems,
                    address_id: selectedAddressId,
                    payment_method_id: selectedPaymentMethodId,
                }),
            });
            const data = await response.json();

            if (data.success && data.data) {
                toast.success(data.message || "Checkout initiated!");
                const checkoutUrl = data.data.checkout_url;

                if (!checkoutUrl) {
                    toast.error("Checkout URL not available");
                    return;
                }

                window.location.href = checkoutUrl;
            } else {
                toast.error(data.message || "Failed to process checkout");
            }
        } catch (error) {
            console.error('Error checking out:', error);
            toast.error("Something went wrong during checkout");
        } finally {
            setCheckoutProcessing(false);
        }
    }

    if (loading) {
        return (
            <div className="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans pb-16">
                <StorefrontHeader />
                <div className="flex min-h-[400px] items-center justify-center">
                    <div className="flex flex-col items-center gap-4 text-slate-500">
                        <div className="h-10 w-10 animate-spin rounded-full border-4 border-indigo-600 border-t-transparent"></div>
                        <p className="text-sm font-medium animate-pulse">Loading checkout details...</p>
                    </div>
                </div>
            </div>
        );
    }

    if (!checkoutData) {
        return (
            <div className="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans pb-16">
                <StorefrontHeader />
                <div className="mx-auto max-w-md text-center py-20 px-4">
                    <h2 className="text-xl font-bold text-slate-900 dark:text-white">Checkout Details Missing</h2>
                    <p className="text-slate-500 mt-2 text-sm">Please return to your cart and make sure items are selected.</p>
                    <Link href="/cart">
                        <Button className="mt-4 bg-indigo-600 text-white">Back to Cart</Button>
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans pb-16">
            <StorefrontHeader />

            {/* Breadcrumbs */}
            <div className="border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-950 px-4 sm:px-6 lg:px-8 py-4">
                <div className="mx-auto max-w-7xl flex items-center justify-between">
                    <nav className="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <Link href="/cart" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            Cart
                        </Link>
                        <ChevronLeft className="h-4 w-4 rotate-180" />
                        <span className="text-slate-900 dark:text-white font-bold">Secure Checkout</span>
                    </nav>
                </div>
            </div>

            <main className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    {/* Information capture blocks */}
                    <div className="lg:col-span-2 space-y-6">
                        
                        {/* Step 1: Shipping Address */}
                        <Card className="border-slate-200 dark:border-slate-800 shadow-2xs bg-white dark:bg-slate-900">
                            <CardHeader className="flex flex-row items-center justify-between border-b border-slate-100 dark:border-slate-805 pb-4 space-y-0">
                                <div>
                                    <CardTitle className="text-sm font-bold flex items-center gap-2">
                                        <span className="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-950 text-[11px] font-bold text-indigo-600 dark:text-indigo-400">1</span>
                                        Shipping Address
                                    </CardTitle>
                                    <CardDescription className="text-xs">
                                        Select where you'd like your order delivered.
                                    </CardDescription>
                                </div>
                                <Button 
                                    variant="outline" 
                                    size="sm" 
                                    onClick={() => setShowAddAddress(!showAddAddress)}
                                    className="text-xs font-semibold shrink-0 cursor-pointer border-slate-200 dark:border-slate-800"
                                >
                                    <Plus className="mr-1 h-3.5 w-3.5" />
                                    Add New
                                </Button>
                            </CardHeader>
                            <CardContent className="pt-4">
                                {showAddAddress ? (
                                    /* Address Form */
                                    <form onSubmit={handleAddAddress} className="space-y-4 border rounded-xl p-4 bg-slate-50/50 dark:bg-slate-950/30 border-slate-200 dark:border-slate-800">
                                        <h3 className="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">New Shipping Address</h3>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div className="space-y-1">
                                                <Label htmlFor="addr1" className="text-xs font-semibold">Address Line 1 <span className="text-rose-500">*</span></Label>
                                                <Input 
                                                    id="addr1"
                                                    placeholder="Street address, P.O. box, company name"
                                                    value={addressLine1}
                                                    onChange={e => setAddressLine1(e.target.value)}
                                                    required
                                                />
                                            </div>
                                            <div className="space-y-1">
                                                <Label htmlFor="addr2" className="text-xs font-semibold">Address Line 2 (Optional)</Label>
                                                <Input 
                                                    id="addr2"
                                                    placeholder="Apartment, suite, unit, building, floor"
                                                    value={addressLine2}
                                                    onChange={e => setAddressLine2(e.target.value)}
                                                />
                                            </div>
                                            <div className="space-y-1">
                                                <Label htmlFor="contact" className="text-xs font-semibold">Preferred Phone <span className="text-rose-500">*</span></Label>
                                                <Input 
                                                    id="contact"
                                                    placeholder="e.g. +92 300 1234567"
                                                    value={preferredContact}
                                                    onChange={e => setPreferredContact(e.target.value)}
                                                    required
                                                />
                                            </div>
                                            <div className="space-y-1">
                                                <Label htmlFor="postal" className="text-xs font-semibold">Postal Code (Optional)</Label>
                                                <Input 
                                                    id="postal"
                                                    placeholder="e.g. 75500"
                                                    value={postalCode}
                                                    onChange={e => setPostalCode(e.target.value)}
                                                />
                                            </div>
                                            
                                            {/* Dropdowns */}
                                            <div className="space-y-1">
                                                <Label className="text-xs font-semibold">Country <span className="text-rose-500">*</span></Label>
                                                <select 
                                                    value={countryId} 
                                                    onChange={e => setCountryId(e.target.value ? Number(e.target.value) : "")}
                                                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring dark:bg-slate-900 border-slate-200 dark:border-slate-800"
                                                    required
                                                >
                                                    <option value="" disabled className="dark:bg-slate-900">Select Country</option>
                                                    {countries.map(c => <option key={c.id} value={c.id} className="dark:bg-slate-900">{c.name}</option>)}
                                                </select>
                                            </div>

                                            <div className="space-y-1">
                                                <Label className="text-xs font-semibold">
                                                    State / Region <span className="text-rose-500">*</span>
                                                    {loadingStates && <Loader2 className="ml-1 inline h-3 w-3 animate-spin text-slate-400" />}
                                                </Label>
                                                <select 
                                                    value={stateId} 
                                                    onChange={e => setStateId(e.target.value ? Number(e.target.value) : "")}
                                                    disabled={!countryId || loadingStates}
                                                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:opacity-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800"
                                                    required
                                                >
                                                    <option value="" disabled className="dark:bg-slate-900">Select State</option>
                                                    {states.map(s => <option key={s.id} value={s.id} className="dark:bg-slate-900">{s.name}</option>)}
                                                </select>
                                            </div>

                                            <div className="space-y-1">
                                                <Label className="text-xs font-semibold">
                                                    City <span className="text-rose-500">*</span>
                                                    {loadingCities && <Loader2 className="ml-1 inline h-3 w-3 animate-spin text-slate-400" />}
                                                </Label>
                                                <select 
                                                    value={cityId} 
                                                    onChange={e => setCityId(e.target.value ? Number(e.target.value) : "")}
                                                    disabled={!stateId || loadingCities}
                                                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:opacity-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800"
                                                    required
                                                >
                                                    <option value="" disabled className="dark:bg-slate-900">Select City</option>
                                                    {cities.map(c => <option key={c.id} value={c.id} className="dark:bg-slate-900">{c.name}</option>)}
                                                </select>
                                            </div>

                                            <div className="flex items-center gap-2 pt-5 select-none">
                                                <input 
                                                    type="checkbox" 
                                                    id="is_default"
                                                    checked={isDefault}
                                                    onChange={e => setIsDefault(e.target.checked)}
                                                    className="h-4.5 w-4.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                                />
                                                <Label htmlFor="is_default" className="text-xs font-semibold cursor-pointer">Set as default address</Label>
                                            </div>
                                        </div>

                                        <div className="flex justify-end gap-2 pt-2">
                                            <Button 
                                                type="button" 
                                                variant="ghost" 
                                                size="sm" 
                                                className="text-xs font-semibold cursor-pointer"
                                                onClick={() => setShowAddAddress(false)}
                                            >
                                                Cancel
                                            </Button>
                                            <Button 
                                                type="submit" 
                                                variant="default"
                                                size="sm" 
                                                className="text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer"
                                                disabled={addingAddress}
                                            >
                                                {addingAddress ? "Saving..." : "Save Address"}
                                            </Button>
                                        </div>
                                    </form>
                                ) : checkoutData.addresses?.length === 0 ? (
                                    <div className="text-center py-6 border-2 border-dashed rounded-xl bg-slate-50/50 dark:bg-slate-950/20 border-slate-250 dark:border-slate-800 space-y-2">
                                        <MapPin className="h-6 w-6 text-slate-400 mx-auto" />
                                        <p className="text-xs font-bold text-slate-700 dark:text-slate-350">No Shipping Address Saved</p>
                                        <p className="text-[11px] text-slate-500">Please click 'Add New' to insert a shipping address.</p>
                                    </div>
                                ) : (
                                    /* Address List Cards */
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        {checkoutData.addresses?.map((address) => (
                                            <label 
                                                key={address.id}
                                                className={`border rounded-xl p-4 flex gap-3 items-start cursor-pointer transition-all select-none relative hover:border-slate-300 dark:hover:border-slate-700 ${
                                                    selectedAddressId === address.id
                                                    ? 'border-indigo-600 dark:border-indigo-500 bg-indigo-50/10 dark:bg-indigo-950/10 ring-1 ring-indigo-600 dark:ring-indigo-500'
                                                    : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900'
                                                }`}
                                            >
                                                <input 
                                                    type="radio" 
                                                    name="address" 
                                                    value={address.id} 
                                                    checked={selectedAddressId === address.id} 
                                                    onChange={() => setSelectedAddressId(address.id)}
                                                    className="mt-1 h-4 w-4 border-slate-350 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                                />
                                                <div className="space-y-1 flex-1">
                                                    <div className="flex items-center gap-1.5">
                                                        <span className="text-xs font-bold text-slate-800 dark:text-slate-250">
                                                            {address.address_line_1}
                                                        </span>
                                                        {address.is_default && (
                                                            <Badge variant="secondary" className="text-[9px] font-bold px-1.5 py-0 bg-slate-100 dark:bg-slate-800 border-none">
                                                                Default
                                                            </Badge>
                                                        )}
                                                    </div>
                                                    {address.address_line_2 && (
                                                        <p className="text-[11px] text-slate-500 dark:text-slate-400">
                                                            {address.address_line_2}
                                                        </p>
                                                    )}
                                                    <p className="text-[11px] text-slate-400">
                                                        Contact: {address.preffered_contact_number}
                                                    </p>
                                                </div>
                                            </label>
                                        ))}
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Step 2: Payment Method */}
                        <Card className="border-slate-200 dark:border-slate-800 shadow-2xs bg-white dark:bg-slate-900">
                            <CardHeader className="border-b border-slate-100 dark:border-slate-805 pb-4">
                                <CardTitle className="text-sm font-bold flex items-center gap-2">
                                    <span className="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-950 text-[11px] font-bold text-indigo-600 dark:text-indigo-400">2</span>
                                    Payment Method
                                </CardTitle>
                                <CardDescription className="text-xs">
                                    Select how you'd like to pay for your order.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="pt-4">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    {checkoutData.payment_methods?.map((payment_method) => {
                                        const isSelected = selectedPaymentMethodId === payment_method.id;
                                        // Display name mapping or icons
                                        const isCard = payment_method.code?.toLowerCase().includes("card") || payment_method.code?.toLowerCase().includes("safepay");
                                        
                                        return (
                                            <label 
                                                key={payment_method.id}
                                                className={`border rounded-xl p-4 flex gap-3.5 items-start cursor-pointer transition-all select-none hover:border-slate-300 dark:hover:border-slate-700 ${
                                                    isSelected
                                                    ? 'border-indigo-600 dark:border-indigo-500 bg-indigo-50/10 dark:bg-indigo-950/10 ring-1 ring-indigo-600 dark:ring-indigo-500'
                                                    : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900'
                                                }`}
                                            >
                                                <input 
                                                    type="radio" 
                                                    name="payment_method" 
                                                    value={payment_method.id} 
                                                    checked={isSelected} 
                                                    onChange={() => setSelectedPaymentMethodId(payment_method.id)}
                                                    className="mt-1 h-4 w-4 border-slate-350 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                                />
                                                <div className="flex items-center gap-3">
                                                    <div className={`p-2 rounded-lg ${isSelected ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'bg-slate-100 dark:bg-slate-950 text-slate-400'}`}>
                                                        {isCard ? <CreditCard className="h-5 w-5" /> : <ShoppingBag className="h-5 w-5" />}
                                                    </div>
                                                    <div className="space-y-0.5">
                                                        <span className="text-xs font-bold text-slate-800 dark:text-slate-250">
                                                            {payment_method.name}
                                                        </span>
                                                        <p className="text-[10px] text-slate-450 dark:text-slate-500">
                                                            {isCard ? "Pay securely via credit card" : "Pay in cash upon arrival"}
                                                        </p>
                                                    </div>
                                                </div>
                                            </label>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Step 3: Review Items */}
                        <Card className="border-slate-200 dark:border-slate-800 shadow-2xs bg-white dark:bg-slate-900">
                            <CardHeader className="border-b border-slate-100 dark:border-slate-805 pb-4">
                                <CardTitle className="text-sm font-bold flex items-center gap-2">
                                    <span className="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-950 text-[11px] font-bold text-indigo-600 dark:text-indigo-400">3</span>
                                    Review Items
                                </CardTitle>
                                <CardDescription className="text-xs">
                                    Double check the items you are purchasing.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="pt-4">
                                <div className="divide-y divide-slate-100 dark:divide-slate-800">
                                    {checkoutData.cart_items?.map((cart_item: CartItem) => {
                                        const finalPrice = cart_item.variant?.final_price ?? cart_item.product.final_price ?? cart_item.product.price;
                                        
                                        return (
                                            <div key={cart_item.id} className="py-3.5 flex gap-4 items-center justify-between first:pt-0 last:pb-0">
                                                <div className="flex items-center gap-3">
                                                    {/* Small Thumbnail placeholder */}
                                                    <div className="h-10 w-10 bg-slate-50 dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-850 flex items-center justify-center text-slate-350 dark:text-slate-850 shrink-0">
                                                        <ShoppingBag className="h-5 w-5" />
                                                    </div>
                                                    <div>
                                                        <h4 className="text-xs font-bold text-slate-850 dark:text-slate-200 line-clamp-1">
                                                            {cart_item.product.name}
                                                        </h4>
                                                        {cart_item.variant && cart_item.variant.variant_options_summary && (
                                                            <span className="text-[10px] text-slate-450 dark:text-slate-500">
                                                                Options: {cart_item.variant.variant_options_summary}
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="text-right shrink-0">
                                                    <p className="text-xs font-extrabold text-slate-900 dark:text-white">
                                                        Rs {finalPrice * cart_item.quantity}
                                                    </p>
                                                    <p className="text-[10px] text-slate-450 dark:text-slate-500">
                                                        Qty: {cart_item.quantity} × Rs {finalPrice}
                                                    </p>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Order Summary Column */}
                    <div className="lg:sticky lg:top-20 space-y-4">
                        <Card className="border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
                            <CardHeader className="border-b border-slate-100 dark:border-slate-800 pb-4">
                                <CardTitle className="text-md font-bold text-slate-900 dark:text-white">Billing Summary</CardTitle>
                                <CardDescription className="text-xs">
                                    Grand total computations for items.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4 pt-4">
                                {bill && (
                                    <div className="space-y-2.5 text-sm">
                                        <div className="flex justify-between text-slate-500 dark:text-slate-400">
                                            <span>Subtotal</span>
                                            <span className="font-medium text-slate-900 dark:text-white">Rs {bill.subtotal}</span>
                                        </div>
                                        
                                        {bill.discount > 0 && (
                                            <div className="flex justify-between text-slate-500 dark:text-slate-400">
                                                <span>Total Savings</span>
                                                <span className="font-semibold text-emerald-600 dark:text-emerald-450">-Rs {bill.discount}</span>
                                            </div>
                                        )}

                                        <div className="flex justify-between text-slate-500 dark:text-slate-400">
                                            <span>Shipping & Handling</span>
                                            <span className="font-medium text-slate-900 dark:text-white">Rs {bill.shipping_fees}</span>
                                        </div>

                                        <div className="flex justify-between text-slate-500 dark:text-slate-400">
                                            <span>Estimated Tax</span>
                                            <span className="font-medium text-slate-900 dark:text-white">Rs {bill.tax}</span>
                                        </div>

                                        <div className="h-px bg-slate-100 dark:bg-slate-800 my-1" />

                                        <div className="flex justify-between text-md font-extrabold text-slate-900 dark:text-white pt-1">
                                            <span>Grand Total</span>
                                            <span className="text-lg font-black text-indigo-600 dark:text-indigo-400">Rs {bill.total_amount}</span>
                                        </div>
                                    </div>
                                )}

                                {/* Place Order button */}
                                <Button
                                    className="w-full py-6 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/15 cursor-pointer flex items-center justify-center gap-2"
                                    onClick={checkout}
                                    disabled={checkoutProcessing || !selectedAddressId || !selectedPaymentMethodId}
                                >
                                    {checkoutProcessing ? (
                                        <>
                                            <Loader2 className="h-4 w-4 animate-spin text-white" />
                                            Processing Order...
                                        </>
                                    ) : (
                                        <>
                                            <Lock className="h-3.5 w-3.5" />
                                            Confirm and Pay
                                        </>
                                    )}
                                </Button>
                            </CardContent>
                        </Card>

                        {/* Security indicators */}
                        <div className="bg-slate-100/50 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-800/50 rounded-xl p-3.5 flex items-start gap-3">
                            <ShieldCheck className="h-5 w-5 text-emerald-500 shrink-0 mt-0.5" />
                            <div className="space-y-0.5">
                                <p className="text-[11px] font-bold text-slate-700 dark:text-slate-350">SSL Certified Safe Checkout</p>
                                <p className="text-[10px] text-slate-450 dark:text-slate-500 leading-normal">
                                    Your personal information is secure. Payments are verified through encrypted channels.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
}
