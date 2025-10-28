<?php

namespace App\Http\Controllers\Api;

use App\Application\UserManagement\Commands\AttachEmailToUser;
use App\Application\UserManagement\Commands\CreateUser;
use App\Domain\User\ValueObjects\PasswordHash;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\TokenResource;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\One\User;

class OAuthController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)
            ->stateless()
            ->redirect();
    }

    public function callback(string $provider)
    {
        /** @var User $oauthUser */
        $oauthUser = Socialite::driver($provider)
            ->stateless()
            ->user();

        $user = auth()->user();
        if (!$user) {
            $user = $this->commands->dispatch(new CreateUser(
                $oauthUser->getName() ?? 'NEW USER',
                $oauthUser->getEmail(),
                PasswordHash::generateRandom()
            ));
        }

        $this->commands->dispatch(new AttachEmailToUser($user->email, $user->id));

        // If you’re using JWT guard for APIs:
        $token = auth()->login($user);

        return ApiResponse::success(
            messageKey: 'messages.user_logged_in',
            data: TokenResource::fromToken($token)
        );
    }
}
