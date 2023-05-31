<?php

namespace App\Providers\Vatsim;

use Illuminate\Http\Request;
use League\OAuth2\Client\Token;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Exception\IdentityProviderException;

class VatauthProvider extends GenericProvider
{


    private $_provider;

    private $_redirectAfterAuth = 'vatauth.login';

    function __construct()
    {
        parent::__construct(
            [
                'clientId'                => config('vatsim_auth.id'),    // The client ID assigned to you by the provider
                'clientSecret'            => config('vatsim_auth.secret'),   // The client password assigned to you by the provider
                'redirectUri'             => route($this->_redirectAfterAuth),
                'urlAuthorize'            => config('vatsim_auth.base').'/oauth/authorize',
                'urlAccessToken'          => config('vatsim_auth.base').'/oauth/token',
                'urlResourceOwnerDetails' => config('vatsim_auth.base').'/api/user',
                'scopes'                  => config('vatsim_auth.scopes'),
                'scopeSeparator'          => ' '
            ]
        );
    }

    public static function updateToken($token)
    {
        $controller = new VatauthProvider;

        try {
            return $controller->getAccessToken(
                'refresh_token',
                [
                    'refresh_token' => $token->getRefreshToken()
                ]
            );
        } catch (IdentityProviderException $e) {
            return null;
        }
    }

}
