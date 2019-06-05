# ChineseCIO Provider for OAuth 2.0 Client

## Installation

To install, use composer:

```
composer require zxkiller/oauth2-chinesecio
```

## Usage

### Authorization Code Flow

```php
$provider = new \zhangxiao\OAuth2\Client\Provider\WebProvider([
        'clientId' => '{wechat-client-id}',
        'clientSecret' => '{wechat-client-secret}',
        'redirect_uri' => 'https://example.com/callback-url',
        'verify' => false,
    ]);

if (!isset($_GET['code'])) {
    $options = [
        'scope' => ['basic', 'email']
        //'scope' => ['basic']
    ];
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl($options);
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Optional: Now you have a token you can look up a users profile data
    try {
        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getUsername());

    } catch (Exception $e) {

        // Failed to get user details
        exit($e->getMessage());
    }

    // Use this to interact with an API on the users behalf
    //echo $token->getToken();
}
```


### Refreshing a Token

Once your application is authorized, you can refresh an expired token using a refresh token rather than going through the entire process of obtaining a brand new token. To do so, simply reuse this refresh token from your data store to request a refresh.

_This example uses [Brent Shaffer's](https://github.com/bshaffer) demo OAuth 2.0 application named **Lock'd In**. See authorization code example above, for more details._

```php
$provider = new \zhangxiao\OAuth2\Client\Provider\WebProvider([
        'clientId' => '{wechat-client-id}',
        'clientSecret' => '{wechat-client-secret}',
        'redirect_uri' => 'https://example.com/callback-url',
        'verify' => false,
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