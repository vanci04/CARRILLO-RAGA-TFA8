<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public static function samples(): array
    {
        return [
            ['full_name' => 'Isabel Santos', 'email' => 'isabel@example.com', 'phone' => '0917 555 0101'],
            ['full_name' => 'Miguel Reyes', 'email' => 'miguel@example.com', 'phone' => '0917 555 0102'],
            ['full_name' => 'Sofia Cruz', 'email' => 'sofia@example.com', 'phone' => '0917 555 0103'],
            ['full_name' => 'Lucas Garcia', 'email' => 'lucas@example.com', 'phone' => '0917 555 0104'],
            ['full_name' => 'Amara Flores', 'email' => 'amara@example.com', 'phone' => '0917 555 0105'],
            ['full_name' => 'Ethan Lim', 'email' => 'ethan@example.com', 'phone' => '0917 555 0106'],
        ];
    }

    public function index()
    {
        $records = config('cafe.data_source') === 'arrays'
            ? self::samples()
            : Customer::orderBy('full_name')->get()->toArray();

        return view('accounts', ['records' => $records, 'kind' => 'customers']);
    }

    public function create()
    {
        return view('account-form', ['record' => new Customer(), 'kind' => 'customers']);
    }

    public function store(Request $request)
    {
        Customer::create($this->validated($request));

        return redirect()->route('customers.index')->with('success', 'Customer account created.');
    }

    public function edit(Customer $customer)
    {
        return view('account-form', ['record' => $customer, 'kind' => 'customers']);
    }

    public function show(Customer $customer)
    {
        return redirect()->route('customers.edit', $customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request, $customer));

        return redirect()->route('customers.index')->with('success', 'Customer account updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer account deleted.');
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers')->ignore($customer)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
        $data['phone'] = $data['phone'] ?? '';

        return $data;
    }
}
