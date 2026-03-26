<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AddressController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get([
                'id',
                'label',
                'full_name',
                'address',
                'region',
                'province',
                'city',
                'barangay',
                'is_default',
            ]);

        return Inertia::render('shop/Addresses', [
            'addresses' => $addresses,
        ]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $shouldBeDefault = (bool) ($validated['is_default'] ?? false) || ! $user->addresses()->exists();

        DB::transaction(function () use ($user, $validated, $shouldBeDefault): void {
            if ($shouldBeDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            $user->addresses()->create([
                ...$validated,
                'is_default' => $shouldBeDefault,
            ]);
        });

        return back()->with('success', 'Address saved successfully.');
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $this->ensureOwnership($request, $address);

        $validated = $request->validated();
        $shouldBeDefault = (bool) ($validated['is_default'] ?? false);

        DB::transaction(function () use ($request, $address, $validated, $shouldBeDefault): void {
            if ($shouldBeDefault) {
                $request->user()->addresses()->update(['is_default' => false]);
            }

            $address->update([
                ...$validated,
                'is_default' => $shouldBeDefault || $address->is_default,
            ]);
        });

        return back()->with('success', 'Address updated successfully.');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        $this->ensureOwnership($request, $address);

        DB::transaction(function () use ($request, $address): void {
            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $nextAddress = $request->user()->addresses()->latest('id')->first();

                if ($nextAddress) {
                    $nextAddress->update(['is_default' => true]);
                }
            }
        });

        return back()->with('success', 'Address removed.');
    }

    public function setDefault(Request $request, Address $address): RedirectResponse
    {
        $this->ensureOwnership($request, $address);

        DB::transaction(function () use ($request, $address): void {
            $request->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return back()->with('success', 'Default address updated.');
    }

    private function ensureOwnership(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403);
    }
}
