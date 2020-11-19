ABELkeycloakBearerOnlyAdapterBundle
===================================

This Symfony bundle is an adapter that allows securing API using keycloak Bearer Only clients.

## Installation

With composer:

```
$ composer require abel/keycloak-bearer-only-adapter-bundle
```

## Configuration

If you want to set up keycloak locally you can download it [here](https://www.keycloak.org/downloads) and follow instructions from [the official documentation](https://www.keycloak.org/docs/latest/server_installation/index.html).

### Bundle configuration

Having a running keycloak locally or in Docker and already configured a client with **Access Type = bearer-only**
here is the configuration to use:

```yaml
# config/packages/abel_keycloak_bearer_only_adapter.yaml
abel_keycloak_bearer_only_adapter:
    issuer: '%env(OAUTH_KEYCLOAK_ISSUER)%' # your accessible keycloak url
    realm: '%env(OAUTH_KEYCLOAK_REALM)%' # your keycloak realm name
    client_id: '%env(OAUTH_KEYCLOAK_CLIENT_ID)%' # your keycloak client id
    client_secret: '%env(OAUTH_KEYCLOAK_CLIENT_SECRET)%' # your keycloak client secret
```
The best practice is to load your configuration from **.env** file.

```
# .env
...
###> Keycloak ###
KEYCLOAK_ISSUER=http://keycloak.local:8080
KEYCLOAK_REALM=my_realm
KEYCLOAK_CLIENT_ID=my_bearer_client
KEYCLOAK_CLIENT_SECRET=my_bearer_client_secret
###< Keycloak ###
...
```

In case of using Keycloak with Docker locally replace **issuer** value with your keycloak container reference in the network

For example, you can use the container IPAdresse, that you can get using this command:

```
$ docker inspect <container id> | grep "IPAddress"
```
### Symfony security configuration

To secure your API with Keycloak you must change the default security configuration in symfony.

Here is a simple configuration that restrict access to ```/api/*``` routes only to user with role **ROLE_API** :

```yaml
# config/packages/security.yaml
security:
    providers:
        keycloak_bearer_user_provider:
            id: ABEL\Bundle\keycloakBearerOnlyAdapterBundle\Security\User\KeycloakBearerUserProvider
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        api:
            pattern: ^/api/
            guard:
                provider: keycloak_bearer_user_provider
                authenticators:
                    - ABEL\Bundle\keycloakBearerOnlyAdapterBundle\Security\Authenticator\KeycloakBearerAuthenticator
            stateless: true
        main:
            anonymous: ~
    access_control:
        - { path: ^/api/, roles: ROLE_API }
```
### Keycloak configuration

To configure keycloak to work with this bundle, [here](./Resources/docs/keycloak-config-guide.md) is a step by step describing for a basic configuration of keycloak.