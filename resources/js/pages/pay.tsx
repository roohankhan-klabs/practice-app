import { useEffect, useRef, useState } from 'react';

import {
    loadFlexScript,
} from '@/lib/cybersource-flex';
import type {
    CybersourceFlex,
    CybersourceMicroform
} from '@/types/cybersource';

export default function Pay() {
    const [paymentId, setPaymentId] =
        useState<number | null>(null);

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

    const [captureContext, setCaptureContext] =
        useState<string | null>(null);

    const [loadingPayment, setLoadingPayment] =
        useState(false);

    const [cardReady, setCardReady] =
        useState(false);

    const [paymentError, setPaymentError] =
        useState<string | null>(null);

    async function initializePayment() {
        setLoadingPayment(true);
        setPaymentError(null);

        try {
            const response = await fetch(
                '/api/v1/payments/safepay',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },

                    body: JSON.stringify({
                        order_id: order.id,
                    }),
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ??
                    'Unable to initialize payment.'
                );
            }

            const payment =
                result.data.payment;

            const context =
                result.data.capture_context;

            /*
             * The response currently contains:
             *
             * capture_context.tracker
             * capture_context.action
             *
             * We need the JWT inside action.flex.
             */

            const jwt =
                context?.action?.flex?.capture_context_jwt;

            if (!jwt) {
                throw new Error(
                    'Safepay did not return a capture context.'
                );
            }

            setPaymentId(payment.id);

            setCaptureContext(jwt);

            await initializeFlex(jwt);

        } catch (error) {
            console.error(error);

            setPaymentError(
                error instanceof Error
                    ? error.message
                    : 'Payment initialization failed.'
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

                    ':focus': {
                        color: '#333',
                    },

                    ':disabled': {
                        cursor: 'not-allowed',
                    },

                    valid: {
                        color: 'green',
                    },

                    invalid: {
                        color: 'red',
                    },
                },
            });

        microformRef.current = microform;

        const cardNumber =
            microform.createField(
                'number',
                {
                    placeholder:
                        'Card number',
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

        cardNumber.load(
            cardNumberRef.current
        );

        securityCode.load(
            securityCodeRef.current
        );

        setCardReady(true);
    }
    async function submitPayment() {
        if (!microformRef.current) {
            setPaymentError(
                'Payment form is not ready.'
            );

            return;
        }

        if (!paymentId) {
            setPaymentError(
                'Payment has not been initialized.'
            );

            return;
        }

        setLoadingPayment(true);
        setPaymentError(null);

        try {
            const microform =
                microformRef.current;

            microform.createToken(
                {
                    expirationMonth: '12',
                    expirationYear: '2030',
                },

                async (
                    error,
                    response
                ) => {
                    if (error) {
                        console.error(
                            'Flex tokenization error:',
                            error
                        );

                        setPaymentError(
                            'Unable to tokenize card.'
                        );

                        setLoadingPayment(false);

                        return;
                    }

                    console.log(
                        'Flex token response:',
                        response
                    );

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

                    await processTransientToken(
                        transientToken
                    );
                }
            );
        } catch (error) {
            console.error(error);

            setPaymentError(
                error instanceof Error
                    ? error.message
                    : 'Payment failed.'
            );

            setLoadingPayment(false);
        }
    }
    async function processTransientToken(
        transientToken: string
    ) {
        try {
            const response = await fetch(
                '/api/v1/safepay/transient-token',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },

                    body: JSON.stringify({
                        payment_id: paymentId,
                        transient_token_jwt:
                            transientToken,
                    }),
                }
            );

            const result =
                await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ??
                    'Unable to process payment.'
                );
            }

            console.log(
                'Transient token processed:',
                result
            );

            /*
             * IMPORTANT:
             *
             * Don't immediately assume payment is complete.
             *
             * Safepay's next_actions tells us what comes next.
             */

            const nextAction =
                result.data?.tracker
                    ?.next_actions
                    ?.CYBERSOURCE
                    ?.kind;

            console.log(
                'Next Safepay action:',
                nextAction
            );

            if (
                nextAction ===
                'AUTHORIZATION'
            ) {
                await authorizePayment();
            }

        } catch (error) {
            console.error(error);

            setPaymentError(
                error instanceof Error
                    ? error.message
                    : 'Payment failed.'
            );
        } finally {
            setLoadingPayment(false);
        }
    }
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

        const currentPaymentId =
            Number(id);

        if (!Number.isInteger(currentPaymentId)) {
            setPaymentError(
                'Invalid payment ID.'
            );

            return;
        }

        setPaymentId(currentPaymentId);

        loadPayment(currentPaymentId);
    }, []);

    async function loadPayment(
        currentPaymentId: number
    ) {
        setLoadingPayment(true);
        setPaymentError(null);

        try {
            const response = await fetch(
                `/api/v1/payments/${currentPaymentId}`,
                {
                    headers: {
                        Accept:
                            'application/json',
                    },
                }
            );

            const result =
                await response.json();

            if (
                !response.ok ||
                !result.success
            ) {
                throw new Error(
                    result.message ??
                    'Unable to load payment.'
                );
            }

            const payment =
                result.data;

            const jwt =
                payment
                    ?.capture_context
                    ?.action
                    ?.flex
                    ?.capture_context_jwt;

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

    return <>
        <div>
            <label>
                Card number
            </label>

            <div
                ref={cardNumberRef}
                className="border rounded-md p-3"
            />
        </div>

        <div>
            <label>
                Security code
            </label>

            <div
                ref={securityCodeRef}
                className="border rounded-md p-3"
            />
        </div>
    </>;
}
