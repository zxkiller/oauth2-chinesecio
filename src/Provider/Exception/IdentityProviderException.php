<?php
/**
 * This exception is adapt for IdentityProviderException in league/oauth2-client library
 */
namespace zhangxiao\OAuth2\Client\Provider\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException as OriginIdentityProviderException;

/**
 * Exception thrown if the provider response contains errors.
 */
class IdentityProviderException extends OriginIdentityProviderException
{}
