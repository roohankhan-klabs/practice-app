import { useEffect, useState } from 'react';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

type PaymentStatusResponse = {
    success: boolean;
    message: string;
    data?: {
        payment?: {
            id: number;
            status: string;
            amount: string;
            currency: string;
            tracker: string;
        };
        tracker?: {
            token: string;
            state: string;
        };
    };
};

type SafepaySuccessProps = {
    tracker?: string | null;
    signature?: string | null;
    reference?: string | null;
};

export default function SafepaySuccess({
    tracker: initialTracker,
}: SafepaySuccessProps) {
    const [message, setMessage] =
        useState('Confirming payment...');

    useEffect(() => {
        async function loadStatus() {
            const params =
                new URLSearchParams(
                    window.location.search
                );

            const tracker =
                initialTracker ??
                params.get('tracker');

            if (!tracker) {
                setMessage(
                    'Payment tracker is missing.'
                );

                return;
            }

            try {
                const response = await fetch(
                    `${API_BASE_URL}/payments/tracker/${tracker}`,
                    {
                        headers: {
                            Accept: 'application/json',
                            Authorization: `Bearer ${localStorage.getItem('token')}`,
                        },
                    }
                );

                const result: PaymentStatusResponse =
                    await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(
                        result.message ??
                            'Unable to verify payment.'
                    );
                }

                const trackerState =
                    result.data?.tracker?.state;

                if (
                    trackerState ===
                    'TRACKER_ENDED'
                ) {
                    setMessage(
                        'Payment completed successfully.'
                    );

                    return;
                }

                setMessage(
                    `Payment returned with tracker state: ${trackerState ?? 'unknown'}.`
                );
            } catch (error) {
                setMessage(
                    error instanceof Error
                        ? error.message
                        : 'Unable to verify payment.'
                );
            }
        }

        void loadStatus();
    }, [initialTracker]);

    return (
        <div className="mx-auto max-w-xl p-6">
            <h1 className="text-2xl font-semibold">
                Safepay Result
            </h1>

            <p className="mt-4 text-sm text-gray-700">
                {message}
            </p>
        </div>
    );
}
