<?php 
namespace zhangxiao\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class WebResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response
     *
     * @var array
     */
    protected $response;

    /**
     * Creates new resource owner.
     *
     * @param array  $response
     */
    public function __construct(array $response = array())
    {
        $this->response = $response;
    }

    /**
     * Get user id ( ident code )
     *
     * @return string|null
     */
    public function getId()
    {
        return isset($this->response['ident_code']) ? $this->response['ident_code'] : null;
    }

    /**
     * Get user email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return isset($this->response['email']) ? $this->response['email'] : null;
    }

    /**
     * Get username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->response['username'] ?: null;
    }

    /**
     * Get user lastname
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->response['status']) ? $this->response['status'] : null;
    }


    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
