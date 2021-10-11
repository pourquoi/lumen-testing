<?php

namespace App\Http\Controllers;

use App\Models\User;
use GrahamCampbell\Flysystem\FlysystemManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Stripe\StripeClient;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Hub;


class UserController extends Controller
{
    protected $stripeClient;
    protected $flysystem;

    public function __construct(StripeClient $stripeClient, FlysystemManager $flysystem)
    {
        $this->stripeClient = $stripeClient;
        $this->flysystem = $flysystem;

        $this->middleware('auth', ['except' => ['register']]);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }

    public function me(Hub $hub)
    {
        $user = Auth::user();

        $builder = new Builder();
        $token = $builder->withClaim('mercure', [
            'subscribe' => ['http://example.com/user/' . $user->id]
        ])->getToken(new Sha256(), new Key(env('MERCURE_JWT_SECRET')));

        //$token = $hub->getFactory()->create(['http://example.com/user/' . $user->id]);

        return response()->json(Auth::user(), 200, [
            'X-Subscription-Token' => $token
        ]);
    }

    public function createStripeCustomer(Request $request, $userId)
    {
        /** @var User $user */
        $user = User::find($userId);

        if (!$user) {
            abort(404);
        }

        if (\Illuminate\Support\Facades\Gate::denies('create-stripe-customer', $user)) {
            abort(403);
        }

        $customer = $this->stripeClient->customers->create([
            'description' => $user->name,
            'email' => $user->email
        ]);

        return response()->json($customer->email, 201);
    }

    public function uploadProfilePicture(Request $request, $userId)
    {
        /** @var User $user */
        $user = User::find($userId);

        if (!$user) {
            abort(404);
        }

        if (\Illuminate\Support\Facades\Gate::denies('upload-profile-picture', $user)) {
            abort(403);
        }

        if (!$request->hasFile('picture')) {
            abort(404);
        }

        $file = $request->file('picture');

        $original_filename = $file->getClientOriginalName();
        $original_filename_arr = explode('.', $original_filename);
        $file_ext = end($original_filename_arr);

        $destination_path = "/users/{$user->id}";

        $file->move(base_path('public/uploads') . $destination_path, "profile.tmp.$file_ext");

        dispatch(new \App\Jobs\ProcessPictureJob($user, "$destination_path/profile.tmp.$file_ext"));

        return response()->json(null, 201);
    }
}
