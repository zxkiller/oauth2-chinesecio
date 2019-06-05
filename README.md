# ChineseCIO Provider for OAuth 2.0 Client

## Installation

To install, use composer:

```
composer require zxkiller/oauth2-chinesecio
```

## Usage

### Authorization Code Flow

```php
$provider = new \Oakhope\OAuth2\Client\Provider\WebProvider([
        'appid' => '{wechat-client-id}',
        'secret' => '{wechat-client-secret}',
        'redirect_uri' => 'https://example.com/callback-url'
    ]);

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to the authorization URL.
    header('Location: '.$authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== rtrim($_SESSION['oauth2state'], '#wechat_redirect'))) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken(
            'authorization_code',
            [
                'code' => $_GET['code'],
            ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo "token: ".$accessToken->getToken()."<br/>";
        echo "refreshToken: ".$accessToken->getRefreshToken()."<br/>";
        echo "Expires: ".$accessToken->getExpires()."<br/>";
        echo ($accessToken->hasExpired() ? 'expired' : 'not expired')."<br/><br/>";

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        var_export($resourceOwner->toArray());
        
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        echo "error:";
        exit($e->getMessage());
    }
}
```


### Refreshing a Token

Once your application is authorized, you can refresh an expired token using a refresh token rather than going through the entire process of obtaining a brand new token. To do so, simply reuse this refresh token from your data store to request a refresh.

_This example uses [Brent Shaffer's](https://github.com/bshaffer) demo OAuth 2.0 application named **Lock'd In**. See authorization code example above, for more details._

```php
$provider = new \Oakhope\OAuth2\Client\Provider\WebProvider([
        'appid' => '{wechat-client-id}',
        'secret' => '{wechat-client-secret}',
        'redirect_uri' => 'https://example.com/callback-url'
    ]);

$existingAccessToken = getAccessTokenFromYourDataStore();

if ($existingAccessToken->hasExpired()) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $existingAccessToken->getRefreshToken()
    ]);

    // Purge old access token and store new access token to your data store.
}
```


## Credits

- [Zhang Xiao](https://github.com/zxkiller)


## License

The MIT License (MIT). Please see [License File](https://github.com/zxkiller/oauth2-chinesecio/blob/master/LICENSE) for more information.