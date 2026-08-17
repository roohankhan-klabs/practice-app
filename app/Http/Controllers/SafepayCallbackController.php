<?php

namespace App\Http\Controllers;

use App\Services\SafePayService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use RuntimeException;

class SafepayCallbackController extends Controller
{
    public function __construct(
        private readonly SafePayService $safepayService
    ) {}

    public function success(Request $request)
    {
        $validated = $request->validate([
            'tracker' => 'required|string',
        ]);

        try {
            $result = $this->safepayService
                ->finalizeSuccessfulCallback(
                    $validated['tracker']
                );
        } catch (RuntimeException) {
            abort(404);
        }

        return Inertia::render('SafepaySuccess', [
            'tracker' => $validated['tracker'],
            'successful' => $result['successful'],
            'alreadyProcessed' => $result['already_processed'] ?? false,
            'paymentId' => $result['payment']->id,
            'orderIds' => $result['payment']->orders->pluck('id')->all(),
            'trackerState' => data_get(
                $result['tracker'],
                'state'
            ),
        ]);
    }

    public function failed(Request $request)
    {
        $validated = $request->validate([
            'tracker' => 'required|string',
        ]);

        try {
            $result = $this->safepayService
                ->finalizeFailedCallback(
                    $validated['tracker']
                );
        } catch (RuntimeException) {
            abort(404);
        }

        return Inertia::render('SafepayFailed', [
            'tracker' => $validated['tracker'],
            'successful' => $result['successful'],
            'paymentId' => $result['payment']->id,
            'orderIds' => $result['payment']->orders->pluck('id')->all(),
            'trackerState' => data_get(
                $result['tracker'],
                'state'
            ),
        ]);
    }
}
