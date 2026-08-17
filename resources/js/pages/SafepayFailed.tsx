type SafepayFailedProps = {
    tracker?: string | null;
    successful?: boolean;
    paymentId?: number | null;
    orderIds?: number[];
    trackerState?: string | null;
};

export default function SafepayFailed({
    tracker,
    successful = false,
    paymentId,
    orderIds = [],
    trackerState,
}: SafepayFailedProps) {
    return (
        <div className="mx-auto max-w-xl p-6">
            <h1 className="text-2xl font-semibold">
                Payment Result
            </h1>

            <p className="mt-4 text-sm text-gray-700">
                {successful
                    ? 'The payment had already been finalized successfully.'
                    : 'The Safepay payment was cancelled or did not complete.'}
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
