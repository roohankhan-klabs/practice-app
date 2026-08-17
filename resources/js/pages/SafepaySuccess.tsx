type SafepaySuccessProps = {
    tracker?: string | null;
    successful?: boolean;
    alreadyProcessed?: boolean;
    paymentId?: number | null;
    orderIds?: number[];
    trackerState?: string | null;
};

export default function SafepaySuccess({
    tracker,
    successful = false,
    alreadyProcessed = false,
    paymentId,
    orderIds = [],
    trackerState,
}: SafepaySuccessProps) {
    return (
        <div className="mx-auto max-w-xl p-6">
            <h1 className="text-2xl font-semibold">
                Safepay Result
            </h1>

            <p className="mt-4 text-sm text-gray-700">
                {successful
                    ? alreadyProcessed
                        ? 'This payment was already verified successfully.'
                        : 'Payment completed successfully.'
                    : 'Payment could not be verified as successful.'}
            </p>

            {paymentId && (
                <p className="mt-2 text-xs text-gray-500">
                    Payment ID: {paymentId}
                </p>
            )}

            {orderIds.length > 0 && (
                <p className="mt-2 text-xs text-gray-500">
                    Orders: {orderIds.join(', ')}
                </p>
            )}

            {tracker && (
                <p className="mt-2 text-xs text-gray-500">
                    Tracker: {tracker}
                </p>
            )}

            {trackerState && (
                <p className="mt-2 text-xs text-gray-500">
                    Tracker state: {trackerState}
                </p>
            )}
        </div>
    );
}
