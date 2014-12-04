# genjSsoClientBundle

The client side bundle to add Single Sign-On login functionality to your site

## Requirements

* Curl

## Installation

Add the bundle to your composer.json

```
"require": {
    ...
    "genj/sso-client-bundle": "dev-master"
}
```

Add the bundle to your AppKernel.php

```
public function registerBundles() {
        $bundles = array(
            ...
            new Genj\SsoClientBundle\GenjSsoClientBundle(),
        );
```

Make sure you have set the following parameters in your parameters.yml.
During the composer install it will prompt you for these settings.
the server url should be the full domain, including the base path of your SSO Server routings

```
    genj_sso_client_server_url: http://webservice.dev/sso/command
    genj_sso_client_broker_secret: 6I3xRWQ4MAMppTvO3nm5
```

Add the following pararmeters to your config.yml

```
genj_sso_client:
    broker_identifier: ABrokerIdentifierKnownOnTheServer
    broker_secret: SomeVerySecretKey
    server_url: %genj_sso_client_server_url% # Replace 
```

Add the following routes to your routing.yml

```
genj_sso_client_login:
    pattern:  /login
    defaults: { _controller: GenjSsoClientBundle:Login:index }

genj_sso_client_logout:
    pattern:  /logout
    defaults: { _controller: GenjSsoClientBundle:Login:logout }
```


