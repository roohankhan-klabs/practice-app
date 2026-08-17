type SafepayFailedProps = {
    tracker?: string | null;
};

export default function SafepayFailed({
    tracker: initialTracker,
}: SafepayFailedProps) {
    const params = new URLSearchParams(
        window.location.search
    );

    const tracker =
        initialTracker ??
        params.get('tracker');

    return (
        <div className="mx-auto max-w-xl p-6">
            <h1 className="text-2xl font-semibold">
                Payment Cancelled
            </h1>

            <p className="mt-4 text-sm text-gray-700">
                The Safepay checkout was cancelled or did not complete.
            </p>

            {tracker && (
                <p className="mt-2 text-xs text-gray-500">
                    Tracker: {tracker}
                </p>
            )}
        </div>
    );
}
