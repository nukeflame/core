<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\ProfileUpdateRequest;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::find(auth()->user()->id)->select('name', 'email', 'first_name')->first();

        $fullname = $user?->name;

        return view('settings.profile', [
            'user' => $user,
            'fullname' => $fullname
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // $validatedData = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:profiles,email',
        //     'phone' => 'nullable|string|max:20',
        //     'designation' => 'nullable|string|max:255',
        //     'age' => 'nullable|integer|min:18|max:100',
        //     'experience' => 'nullable|integer|min:0|max:50',
        //     'professional_bio' => 'nullable|string',
        //     'skills' => 'nullable|array',
        //     'social_networks' => 'nullable|array'
        // ]);

        // $profile = Profile::create([
        //     'name' => $validatedData['name'],
        //     'email' => $validatedData['email'],
        //     'phone' => $validatedData['phone'] ?? null,
        //     'designation' => $validatedData['designation'] ?? null,
        //     'age' => $validatedData['age'] ?? null,
        //     'experience' => $validatedData['experience'] ?? null,
        //     'professional_bio' => $validatedData['professional_bio'] ?? null,
        //     'skills' => json_encode($validatedData['skills'] ?? []),
        //     'social_networks' => json_encode($validatedData['social_networks'] ?? [])
        // ]);

        // Schema::create('profiles', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('email')->unique();
        //     $table->string('phone')->nullable();
        //     $table->string('designation')->nullable();
        //     $table->integer('age')->nullable();
        //     $table->integer('experience')->nullable();
        //     $table->text('professional_bio')->nullable();
        //     $table->json('skills')->nullable();
        //     $table->json('social_networks')->nullable();
        //     $table->timestamps();
        // });

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/login');
    }

    public function myProfile(Request $request)
    {
        $user = User::with('role', 'department')->where('user_name', Auth::user()->user_name)->first();
        return view('profile.myprofile', [
            'user' => $user,

        ]);
    }

    public function getChangepwd(Request $request)
    {
        $user = User::where('user_name', Auth::user()->user_name)->first();
        return view('profile.changepwd', [
            'user' => $user,

        ]);
    }

    public function changeMyPass(Request $request)
    {
        // dd($request);
        $current_pass = $request->current_pass;
        $new_pass = $request->new_pass;
        $confirm_pass = $request->confirm_pass;

        DB::beginTransaction();
        try {
            $user = User::where('user_name', Auth::user()->user_name)->first();
            // dd(Hash::check($current_pass, $user->password));
            // Check if the provided current password matches the hashed password stored in the database
            if (Hash::check($current_pass, $user->password)) {
                if ($new_pass == $confirm_pass) {
                    // Update the user's password and set the expiry date
                    $user->update([
                        'password' => Hash::make($new_pass),
                        'pass_expiry_date' => Carbon::now()->addDays(30),
                    ]);
                    DB::commit();
                    return Redirect::to('/login');
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'New and Confirm password does not match'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Current password is wrong'
                ]);
            }
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save data'
            ]);
        }
    }

    public function generatePassword($length = 8)
    {
        return Str::random($length);
    }
}
