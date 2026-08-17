import { useEffect, useRef, useState } from 'react';

import { loadFlexScript } from '@/lib/cybersource-flex';
import type {
    CybersourceFlex,
    CybersourceMicroform,
} from '@/types/cybersource';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

export default function Pay() {
    const cardNumberRef =
        useRef<HTMLDivElement | null>(null);

    const securityCodeRef =
        useRef<HTMLDivElement | null>(null);

    const flexRef =
        useRef<CybersourceFlex | null>(null);

    const microformRef =
        useRef<CybersourceMicroform | null>(null);

    const [paymentId, setPaymentId] =
        useState<number | null>(null);

    const [loadingPayment, setLoadingPayment] =
        useState(false);

    const [cardReady, setCardReady] =
        useState(false);

    const [paymentError, setPaymentError] =
        useState<string | null>(null);

    const [expirationMonth, setExpirationMonth] =
        useState('12');

    const [expirationYear, setExpirationYear] =
        useState('2030');

    useEffect(() => {
        const params = new URLSearchParams(
            window.location.search
        );

        const id = params.get('payment_id');

        if (!id) {
            setPaymentError(
                'Payment ID is missing.'
            );

            return;
        }

        const currentPaymentId = Number(id);

        if (!Number.isInteger(currentPaymentId)) {
            setPaymentError(
                'Invalid payment ID.'
            );

            return;
        }

        setPaymentId(currentPaymentId);
        void loadPayment(currentPaymentId);
    }, []);

    async function loadPayment(
        currentPaymentId: number
    ) {
        setLoadingPayment(true);
        setPaymentError(null);
        setCardReady(false);

        try {
            const response = await fetch(
                `${API_BASE_URL}/payments/${currentPaymentId}/capture-context`,
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
                    result.message ??
                        'Unable to load payment.'
                );
            }

            const jwt =
                result.data?.capture_context?.data?.action?.flex?.capture_context_jwt;

            if (!jwt) {
                throw new Error(
                    'Capture context is missing.'
                );
            }

            await initializeFlex(jwt);
        } catch (error) {
            console.error(error);

            setPaymentError(
                error instanceof Error
                    ? error.message
                    : 'Unable to load payment.'
            );
        } finally {
            setLoadingPayment(false);
        }
    }

    async function initializeFlex(
        captureContextJwt: string
    ) {
        await loadFlexScript();

        if (!window.Flex) {
            throw new Error(
                'Cybersource Flex SDK is unavailable.'
            );
        }

        const flex =
            new window.Flex(captureContextJwt);

        flexRef.current = flex;

        const microform =
            flex.microform({
                styles: {
                    input: {
                        'font-size': '16px',
                        'font-family': 'Arial, sans-serif',
                    },
                },
            });

        microformRef.current = microform;

        const cardNumber =
            microform.createField(
                'number',
                {
                    placeholder: 'Card number',
                }
            );

        const securityCode =
            microform.createField(
                'securityCode',
                {
                    placeholder: 'CVV',
                }
            );

        if (
            !cardNumberRef.current ||
            !securityCodeRef.current
        ) {
            throw new Error(
                'Payment fields are unavailable.'
            );
        }

        cardNumber.load(cardNumberRef.current);
        securityCode.load(securityCodeRef.current);

        setCardReady(true);
    }

    async function submitPayment() {
        if (!microformRef.current || !paymentId) {
            setPaymentError(
                'Payment form is not ready.'
            );

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
                    setPaymentError(
                        'Unable to tokenize card.'
                    );
                    setLoadingPayment(false);

                    return;
                }

                const transientToken =
                    response.token ??
                    response.data?.transientToken;

                if (!transientToken) {
                    setPaymentError(
                        'Transient token was not returned.'
                    );
                    setLoadingPayment(false);

                    return;
                }

                try {
                    const tokenResponse = await fetch(
                        `${API_BASE_URL}/payments/${paymentId}/transient-token`,
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type':
                                    'application/json',
                                Accept: 'application/json',
                                Authorization: `Bearer ${localStorage.getItem('token')}`,
                            },
                            body: JSON.stringify({
                                transient_token_jwt:
                                    transientToken,
                            }),
                        }
                    );

                    const result =
                        await tokenResponse.json();

                    if (
                        !tokenResponse.ok ||
                        !result.success
                    ) {
                        throw new Error(
                            result.message ??
                                'Unable to process payment.'
                        );
                    }

                    const tracker =
                        result.data?.data?.tracker?.token ??
                        result.data?.tracker?.token;

                    const trackerState =
                        result.data?.data?.tracker?.state ??
                        result.data?.tracker?.state;

                    if (
                        tracker &&
                        trackerState ===
                            'TRACKER_ENDED'
                    ) {
                        window.location.href =
                            `/safepay/success?tracker=${tracker}`;

                        return;
                    }

                    setPaymentError(
                        'This payment requires additional Safepay authentication that is not yet completed in this browser flow.'
                    );
                } catch (requestError) {
                    console.error(requestError);
                    setPaymentError(
                        requestError instanceof Error
                            ? requestError.message
                            : 'Payment failed.'
                    );
                } finally {
                    setLoadingPayment(false);
                }
            }
        );
    }

    return (
        <div className="mx-auto max-w-xl space-y-4 p-6">
            <h1 className="text-2xl font-semibold">
                Complete Payment
            </h1>

            {paymentError && (
                <div className="rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-700">
                    {paymentError}
                </div>
            )}

            <div>
                <label className="mb-2 block">
                    Card number
                </label>

                <div
                    ref={cardNumberRef}
                    className="rounded-md border p-3"
                />
            </div>

            <div>
                <label className="mb-2 block">
                    Security code
                </label>

                <div
                    ref={securityCodeRef}
                    className="rounded-md border p-3"
                />
            </div>

            <div className="grid grid-cols-2 gap-4">
                <label className="block">
                    <span className="mb-2 block">
                        Expiry month
                    </span>

                    <input
                        value={expirationMonth}
                        onChange={(event) =>
                            setExpirationMonth(
                                event.target.value
                            )
                        }
                        className="w-full rounded-md border p-3"
                    />
                </label>

                <label className="block">
                    <span className="mb-2 block">
                        Expiry year
                    </span>

                    <input
                        value={expirationYear}
                        onChange={(event) =>
                            setExpirationYear(
                                event.target.value
                            )
                        }
                        className="w-full rounded-md border p-3"
                    />
                </label>
            </div>

            <button
                type="button"
                onClick={submitPayment}
                disabled={!cardReady || loadingPayment}
                className="rounded-md bg-black px-4 py-3 text-sm font-medium text-white disabled:opacity-60"
            >
                {loadingPayment
                    ? 'Processing...'
                    : 'Pay now'}
            </button>

            {paymentId && (
                <p className="text-xs text-gray-500">
                    Payment #{paymentId}
                </p>
            )}
        </div>
    );
}
