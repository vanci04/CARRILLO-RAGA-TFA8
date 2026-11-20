<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    public static function samples(): array
    {
        return [
            ['username' => 'ella.manager', 'full_name' => 'Ella Mendoza', 'role' => 'Manager'],
            ['username' => 'nico.barista', 'full_name' => 'Nico Ramos', 'role' => 'Barista'],
            ['username' => 'mia.cashier', 'full_name' => 'Mia Torres', 'role' => 'Cashier'],
            ['username' => 'leo.barista', 'full_name' => 'Leo Bautista', 'role' => 'Barista'],
            ['username' => 'ava.kitchen', 'full_name' => 'Ava Tan', 'role' => 'Kitchen'],
            ['username' => 'noah.cashier', 'full_name' => 'Noah Villanueva', 'role' => 'Cashier'],
        ];
    }

    public function index()
    {
        $records = config('cafe.data_source') === 'arrays'
            ? self::samples()
            : User::orderBy('full_name')->get()->toArray();

        return view('accounts', ['records' => $records, 'kind' => 'users']);
    }

    public function create()
    {
        return view('account-form', ['record' => new User(['role' => 'Barista']), 'kind' => 'users']);
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('users.index')->with('success', 'User account created.');
    }

    public function edit(User $user)
    {
        return view('account-form', ['record' => $user, 'kind' => 'users']);
    }

    public function show(User $user)
    {
        return redirect()->route('users.edit', $user);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->storeAvatar($request, $user);
        }
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User account updated.');
    }

    public function destroy(User $user)
    {
        abort_if($user->is(auth()->user()), 422, 'You cannot delete the account you are currently using.');
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User account deleted.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $passwordRules = [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()];

        return $request->validate([
            'username' => ['required', 'string', 'max:80', Rule::unique('users')->ignore($user)],
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => $passwordRules,
            'role' => ['required', Rule::in(['Manager', 'Barista', 'Cashier', 'Kitchen'])],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function storeAvatar(Request $request, User $user): string
    {
        $image = (new ImageManager(new Driver()))
            ->read($request->file('avatar')->getRealPath())
            ->cover(240, 240)
            ->toJpeg(85);
        $path = 'avatars/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, (string) $image);
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        return $path;
    }
}
