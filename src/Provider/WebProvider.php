<?php 
namespace zhangxiao\OAuth2\Client\Provider;

use GuzzleHttp\Psr7\Uri;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use zhangxiao\OAuth2\Client\Provider\Exception\IdentityProviderException;

class WebProvider extends AbstractProvider
{

    //use BearerAuthorizationTrait;

    /**
     * Default scopes
     *
     * @var array
     */
    public $defaultScopes = [];

    /**
     * Base url for authorization.
     *
     * @var string
     */
    protected $urlAuthorize = 'https://passport.chineseplus.net/oauth2/Authcode/authorize';

    /**
     * Base url for access token.
     *
     * @var string
     */
    protected $urlAccessToken = 'https://passport.chineseplus.net/oauth2/Authcode/accesstoken';

    /**
     * Base url for resource owner.
     *
     * @var string
     */
    protected $urlResourceOwnerDetails = 'https://passport.chineseplus.net/resource/Api/getuserinfo';

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->urlAuthorize;
    }

    /**
     * Get access token url to retrieve token
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->urlAccessToken;
    }

    /**
     * Get default scopes
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return $this->defaultScopes;
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw new IdentityProviderException(
                (isset($data['message']) ? $data['message'].(isset($data['hint']) ? '('.$data['hint'].')' : '') : $response->getReasonPhrase()),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return MicrosoftResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new WebResourceOwner($response);
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $uri = new Uri($this->urlResourceOwnerDetails);

        return (string) Uri::withQueryValue($uri, 'access_token', (string) $token);
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }

    protected function getAllowedClientOptions(array $options)
    {
        return ['timeout', 'proxy', 'verify'];
    }

    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        $url = $this->getResourceOwnerDetailsUrl($token);


        $body = ['access_token' => $token->getToken()];

        $options['body'] = json_encode($body);
        $options['headers']['content-type'] = 'application/json';

        $request = $this->getAuthenticatedRequest(self::METHOD_POST, $url, $token, $options);

        $response = $this->getParsedResponse($request);

        if (false === is_array($response)) {
            throw new UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }

        return $response;
    }
}
