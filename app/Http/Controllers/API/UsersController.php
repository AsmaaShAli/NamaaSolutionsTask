<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UsersService;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    private $request;
    private $usersService;
    public function __construct(Request $request, UsersService $usersService)
    {
        $this->request = $request;
        $this->usersService = $usersService;
    }
    /**
     * Display a listing of the resource.
     */
    public function __invoke()
    {
        $all_providers = ['DataProviderX','DataProviderY'];
        $providers = $this->request->provider ? Arr::wrap($this->request->provider) : $all_providers;

        $request = $this->request->all();
        $filtered_users = [];
        foreach($providers as $provider) {
            $type = $this->usersService->providerMapping($provider);
            $users = collect(json_decode($this->usersService->readFile($provider)))
                ->map(function ($user) use ($type) {
                    return (new UserTransformer())->transform($user, $type);
                })->filter(function ($user) use ($request) {
                    if (array_key_exists('statusCode', $request)) {
                        return $user['status'] == $request['statusCode'];
                    }
                    return true;
                })->filter(function ($user) use ($request) {
                    if (array_key_exists('balanceMin', $request) && array_key_exists('balanceMax', $request)) {
                        return $user['balance'] >= $request['balanceMin'] && $user['balance'] <= $request['balanceMax'];
                    }
                    return true;
                })->filter(function ($user) use ($request) {
                if(array_key_exists('currency', $request)) {
                        return $user['currency'] == $request['currency'];
                    }
                    return true;
                });

            $filtered_users = array_merge($filtered_users,$users->toArray());
        }

        return responder()->success([
                'users' => $filtered_users
            ])->respond(Response::HTTP_OK);
    }
}
