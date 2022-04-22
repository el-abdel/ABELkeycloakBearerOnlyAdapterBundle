<?php


namespace ABEL\Bundle\keycloakBearerOnlyAdapterBundle\Security\User;


use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class KeycloakBearerUserProvider implements UserProviderInterface
{
    /**
     * @var string
     */
    private $issuer;
    /**
     * @var string
     */
    private $realm;
    /**
     * @var string
     */
    private $client_id;
    /**
     * @var string
     */
    private $client_secret;
    /**
     * @var mixed
     */
    private $sslVerification;

    /**
     * KeycloakBearerUserProvider constructor.
     * @param string $issuer
     * @param string $realm
     * @param string $client_id
     * @param string $client_secret
     */
    public function __construct(string $issuer, string $realm, string $client_id, string $client_secret, $sslVerification)
    {
        $this->issuer = $issuer;
        $this->realm = $realm;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->sslVerification = $sslVerification;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $accessToken The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($accessToken)
    {
        $client = new Client([
            'base_uri' => $this->issuer,
        ]);

        $response = $client->post('/realms/'.$this->realm.'/protocol/openid-connect/token/introspect', [
            'auth' => [$this->client_id, $this->client_secret],
            'form_params' => [
                'token' => $accessToken,
            ],
            'proxy' => [
                'http'  => '', // Use this proxy with "http"
                'https' => '', // Use this proxy with "https",
            ],
            'verify' => $this->sslVerification,
            'http_errors' => false
        ]);

        $jwt = json_decode($response->getBody(), true);

        if (!$jwt['active']) {
            throw new \UnexpectedValueException('The token does not exist or is not valid anymore');
        }

        if (!isset($jwt['resource_access'][$this->client_id])) {
            throw new \UnexpectedValueException('The token does not have the necessary permissions!');
        }

        return new KeycloakBearerUser(
            $jwt['sub'],
            $jwt['name'],
            $jwt['email'],
            $jwt['given_name'],
            $jwt['family_name'],
            $jwt['preferred_username'],
            $jwt['resource_access'][$this->client_id]['roles'],
            $accessToken
        );
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException  if the user is not supported
     * @throws UsernameNotFoundException if the user is not found
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof KeycloakBearerUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user = $this->loadUserByUsername($user->getAccessToken());

        if (!$user) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return KeycloakBearerUser::class === $class;
    }
}