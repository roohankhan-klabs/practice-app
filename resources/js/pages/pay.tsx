import { useEffect, useRef, useState } from 'react';
import { loadFlexScript } from '@/lib/cybersource-flex';
import type {
    CybersourceFlex,
    CybersourceMicroform,
} from '@/types/cybersource';
import { 
    CreditCard, Lock, ShieldCheck, ChevronLeft, 
    Loader2, AlertCircle, ShoppingBag 
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import StorefrontHeader from "@/components/storefront-header";

const API_BASE_URL =
    import.meta.env.VITE_API_BASE_URL ?? '/api/v1';

function buildApiUrl(path: string) {
    if (/^https?:\/\//.test(API_BASE_URL)) {
        return `${API_BASE_URL}${path}`;
    }
    return `${window.location.origin}${API_BASE_URL}${path}`;
}

function extractTransientToken(response: unknown) {
    if (typeof response === 'string' && response !== '') {
        return response;
    }

    if (!response || typeof response !== 'object') {
        return null;
    }

    const payload = response as Record<string, unknown>;
    const data =
        payload.data && typeof payload.data === 'object'
            ? (payload.data as Record<string, unknown>)
            : null;

    const candidates = [
        payload.token,
        payload.transientToken,
        payload.transient_token,
        payload.jwt,
        payload.tokenJwt,
        payload.transientTokenJwt,
        data?.token,
        data?.transientToken,
        data?.transient_token,
        data?.jwt,
        data?.tokenJwt,
        data?.transientTokenJwt,
    ];

    for (const candidate of candidates) {
        if (typeof candidate === 'string' && candidate !== '') {
            return candidate;
        }
    }

    return null;
}

export default function Pay() {
    const cardNumberRef = useRef<HTMLDivElement | null>(null);
    const securityCodeRef = useRef<HTMLDivElement | null>(null);
    const flexRef = useRef<CybersourceFlex | null>(null);
    const microformRef = useRef<CybersourceMicroform | null>(null);

    const [paymentId, setPaymentId] = useState<number | null>(null);
    const [loadingPayment, setLoadingPayment] = useState(false);
    const [cardReady, setCardReady] = useState(false);
    const [paymentError, setPaymentError] = useState<string | null>(null);
    const [expirationMonth, setExpirationMonth] = useState('12');
    const [expirationYear, setExpirationYear] = useState('2030');

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const id = params.get('payment_id');

        if (!id) {
            setPaymentError('Payment ID is missing.');
            return;
        }

        const currentPaymentId = Number(id);

        if (!Number.isInteger(currentPaymentId)) {
            setPaymentError('Invalid payment ID.');
            return;
        }

        setPaymentId(currentPaymentId);
        void loadPayment(currentPaymentId);
    }, []);

    async function loadPayment(currentPaymentId: number) {
        setLoadingPayment(true);
        setPaymentError(null);
        setCardReady(false);

        try {
            const response = await fetch(
                buildApiUrl(`/payments/${currentPaymentId}/capture-context`),
                {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                    },
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ?? 'Unable to load payment.'
                );
            }

            const jwt = result.data?.capture_context?.data?.action?.flex?.capture_context_jwt;

            if (!jwt) {
                throw new Error('Capture context is missing.');
            }

            await initializeFlex(jwt);
        } catch (error) {
            console.error(error);
            setPaymentError(
                error instanceof Error ? error.message : 'Unable to load payment.'
            );
        } finally {
            setLoadingPayment(false);
        }
    }

    async function initializeFlex(captureContextJwt: string) {
        await loadFlexScript(captureContextJwt);

        if (!window.Flex) {
            throw new Error('Cybersource Flex SDK is unavailable.');
        }

        const flex = new window.Flex(captureContextJwt);
        flexRef.current = flex;

        const isDark = document.documentElement.classList.contains('dark');
        const inputTextColor = isDark ? '#ffffff' : '#0f172a';

        const microform = flex.microform({
            styles: {
                input: {
                    'font-size': '15px',
                    'font-family': 'system-ui, -apple-system, sans-serif',
                    'color': inputTextColor,
                },
            },
        });

        microformRef.current = microform;

        const cardNumber = microform.createField('number', {
            placeholder: '•••• •••• •••• ••••',
        });

        const securityCode = microform.createField('securityCode', {
            placeholder: '•••',
        });

        if (!cardNumberRef.current || !securityCodeRef.current) {
            throw new Error('Payment fields are unavailable.');
        }

        cardNumber.load(cardNumberRef.current);
        securityCode.load(securityCodeRef.current);

        setCardReady(true);
    }

    async function submitPayment() {
        if (!microformRef.current || !paymentId) {
            setPaymentError('Payment form is not ready.');
            return;
        }

        setLoadingPayment(true);
        setPaymentError(null);

        microformRef.current.createToken(
            {
                expirationMonth,
                expirationYear,
            },
            async (error, response) => {
                if (error) {
                    console.error(error);
                    setPaymentError('Unable to tokenize card. Please check your card inputs.');
                    setLoadingPayment(false);
                    return;
                }

                const transientToken = extractTransientToken(response);

                if (!transientToken) {
                    console.error(
                        'Cybersource token response did not include a recognized transient token field.',
                        response
                    );
                    setPaymentError('Transient token was not returned.');
                    setLoadingPayment(false);
                    return;
                }

                try {
                    const tokenResponse = await fetch(
                        buildApiUrl(`/payments/${paymentId}/transient-token`),
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                Authorization: `Bearer ${localStorage.getItem('token')}`,
                            },
                            body: JSON.stringify({
                                transient_token_jwt: transientToken,
                            }),
                        }
                    );

                    const result = await tokenResponse.json();

                    if (!tokenResponse.ok || !result.success) {
                        throw new Error(
                            result.message ?? 'Unable to process payment.'
                        );
                    }

                    const tracker =
                        result.data?.data?.tracker?.token ??
                        result.data?.tracker?.token;

                    const trackerState =
                        result.data?.data?.tracker?.state ??
                        result.data?.tracker?.state;

                    if (tracker && trackerState === 'TRACKER_ENDED') {
                        window.location.href = `/safepay/success?tracker=${tracker}`;
                        return;
                    }

                    setPaymentError(
                        'This payment requires additional Safepay authentication that is not yet completed in this browser flow.'
                    );
                } catch (requestError) {
                    console.error(requestError);
                    setPaymentError(
                        requestError instanceof Error ? requestError.message : 'Payment failed.'
                    );
                } finally {
                    setLoadingPayment(false);
                }
            }
        );
    }

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans pb-16">
            <StorefrontHeader />

            {/* Breadcrumb nav */}
            <div className="border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-950 px-4 sm:px-6 lg:px-8 py-4">
                <div className="mx-auto max-w-xl flex items-center justify-between">
                    <nav className="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <span className="text-slate-500">Checkout</span>
                        <ChevronLeft className="h-4 w-4 rotate-180" />
                        <span className="text-slate-900 dark:text-white font-bold">Secure Card Payment</span>
                    </nav>
                </div>
            </div>

            <main className="mx-auto max-w-xl px-4 sm:px-6 py-12">
                <Card className="border-slate-200 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-900 overflow-hidden">
                    <CardHeader className="border-b border-slate-100 dark:border-slate-805 pb-5 flex flex-row items-start justify-between gap-4">
                        <div className="space-y-1">
                            <CardTitle className="text-lg font-bold flex items-center gap-2">
                                <CreditCard className="h-5 w-5 text-indigo-600 dark:text-indigo-400 shrink-0" />
                                Secure Card Payment
                            </CardTitle>
                            <CardDescription className="text-xs">
                                Enter your card details below to complete your checkout transaction.
                            </CardDescription>
                        </div>
                        {paymentId && (
                            <Badge variant="outline" className="text-[10px] font-extrabold bg-slate-50 dark:bg-slate-950 py-0.5 px-2 border-slate-250 dark:border-slate-800 text-slate-500 shrink-0">
                                Order #{paymentId}
                            </Badge>
                        )}
                    </CardHeader>
                    <CardContent className="pt-6 space-y-5">
                        
                        {/* Error Alert */}
                        {paymentError && (
                            <div className="rounded-xl border border-rose-200 bg-rose-50/50 dark:bg-rose-950/20 p-3.5 flex gap-2.5 items-start text-xs text-rose-700 dark:text-rose-400">
                                <AlertCircle className="h-4.5 w-4.5 shrink-0 mt-0.5" />
                                <div className="space-y-0.5 leading-relaxed">
                                    <p className="font-bold">Payment Error</p>
                                    <p>{paymentError}</p>
                                </div>
                            </div>
                        )}

                        {/* Card Number Input */}
                        <div className="space-y-1.5">
                            <Label className="text-xs font-bold text-slate-700 dark:text-slate-300">
                                Card Number
                            </Label>
                            <div
                                ref={cardNumberRef}
                                className="h-10 bg-white dark:bg-slate-950 border border-slate-250 dark:border-slate-800 rounded-lg px-3 py-2 shadow-2xs focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-colors flex items-center justify-between"
                            />
                        </div>

                        {/* Expiry and CVV Grid */}
                        <div className="grid grid-cols-3 gap-4">
                            <div className="space-y-1.5 col-span-1">
                                <Label className="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Expiry Month
                                </Label>
                                <Input
                                    value={expirationMonth}
                                    placeholder="MM"
                                    maxLength={2}
                                    onChange={(event) => setExpirationMonth(event.target.value)}
                                    className="bg-white dark:bg-slate-950 border-slate-250 dark:border-slate-800 text-sm h-10 text-center font-semibold"
                                />
                            </div>

                            <div className="space-y-1.5 col-span-1">
                                <Label className="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Expiry Year
                                </Label>
                                <Input
                                    value={expirationYear}
                                    placeholder="YYYY"
                                    maxLength={4}
                                    onChange={(event) => setExpirationYear(event.target.value)}
                                    className="bg-white dark:bg-slate-950 border-slate-250 dark:border-slate-800 text-sm h-10 text-center font-semibold"
                                />
                            </div>

                            <div className="space-y-1.5 col-span-1">
                                <Label className="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Security CVV
                                </Label>
                                <div
                                    ref={securityCodeRef}
                                    className="h-10 bg-white dark:bg-slate-950 border border-slate-250 dark:border-slate-800 rounded-lg px-3 py-2 shadow-2xs focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-colors flex items-center"
                                />
                            </div>
                        </div>

                        {/* Submit Button */}
                        <Button
                            type="button"
                            onClick={submitPayment}
                            disabled={!cardReady || loadingPayment}
                            className="w-full py-6 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/15 cursor-pointer flex items-center justify-center gap-2 mt-4"
                        >
                            {loadingPayment ? (
                                <>
                                    <Loader2 className="h-4 w-4 animate-spin text-white" />
                                    Processing Transaction...
                                </>
                            ) : (
                                <>
                                    <Lock className="h-3.5 w-3.5" />
                                    Pay securely
                                </>
                            )}
                        </Button>
                    </CardContent>
                </Card>

                {/* Trust Seal */}
                <div className="mt-6 bg-slate-100/50 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-800/50 rounded-xl p-3.5 flex items-start gap-3 max-w-xl">
                    <ShieldCheck className="h-5 w-5 text-emerald-500 shrink-0 mt-0.5" />
                    <div className="space-y-0.5">
                        <p className="text-[11px] font-bold text-slate-700 dark:text-slate-350">Cybersource Secure Payments</p>
                        <p className="text-[10px] text-slate-450 dark:text-slate-500 leading-normal">
                            Your payment details are sent directly to Cybersource processors via encrypted frames. No credit card records are stored on our servers.
                        </p>
                    </div>
                </div>
            </main>
        </div>
    );
}
