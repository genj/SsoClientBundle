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

Add the following pararmeters to your config.yml

```
genj_sso_client:
    broker_identifier: ABrokerIdentifierKnownOnTheServer
    broker_secret: SomeVerySecretKey
    server_url: http://urltoserver.ext/sso/command # Replace 
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


