<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\LazyCollection;

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
        $providers = Arr::wrap($this->request->provider) ?? $all_providers;

        $request = $this->request->all();
        $filtered_users = [];
        foreach($providers as $provider) {
            $type = $this->usersService->providerMapping($provider);
            $users = collect(json_decode($this->usersService->readFile($provider)))
                ->map(function ($user) use ($type) {
                    switch ($type) {
                        case 'x':
                            $item = [
                                'id'                => $user->parentIdentification,
                                'status'            => $this->usersService->mapStatus($user->statusCode, $type),
                                'balance'           => $user->parentAmount,
                                'currency'          => $user->Currency,
                                'registration_date' => date('Y-m-d', strtotime($user->registerationDate)),
                                'email'             => $user->parentEmail,
                            ];
                            break;
                        case 'y':
                            $item = [
                                'id' => $user->id,
                                'status'            => $this->usersService->mapStatus($user->status, $type),
                                'balance'           => $user->balance,
                                'currency'          => $user->currency,
                                'registration_date' => date('Y-m-d',
                                    strtotime(str_replace('/', '-', $user->created_at))),
                                'email'             => $user->email,
                            ];
                            break;
                    }
                    return $item;
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

        dd($filtered_users);
    }
}
