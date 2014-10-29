<?php

namespace Genj\SsoClientBundle\Sso;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class Broker {
    /**
     * Pass 401 http response of the server to the client
     */
    public $pass401 = true;

    /**
     * Url of SSO server
     * @var string
     */
    public $url;

    /**
     * My identifier, given by SSO provider.
     * @var string
     */
    public $broker;

    /**
     * My secret word, given by SSO provider.
     * @var string
     */
    public $secret;

    /**
     * Need to be shorter than session expire of SSO server
     * @var string
     */
    public $sessionExpire = 1800;

    /**
     * Session hash
     * @var string
     */
    protected $sessionToken;

    /**
     * User info recieved from the server.
     * @var array
     */
    protected $userinfo;

    /**
     * @var Request
     */
    protected $request;


    /**
     * Class constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request, array $config)
    {
        $this->request = $request;

        $this->setConfig($config);

        var_dump($this->secret);
        if (isset($_COOKIE['session_token'])) {
            $this->sessionToken = $_COOKIE['session_token'];
        }

    }

    /**
     *
     * @param $redirectUrl
     *
     * @return bool|RedirectResponse
     */
    public function autoAttach($redirectUrl = '') {
        if (!isset($this->sessionToken)) {
            $attachUrl = $this->getAttachUrl() . "&redirect=" . urlencode($redirectUrl);

            return new RedirectResponse($attachUrl, 307);
        }

        return false;
    }

    /**
     *
     * @return bool
     */
    public function isAttached() {
        if (isset($this->sessionToken)) {
            return true;
        }

        return false;
    }

    /**
     * Get session token
     *
     * @return string
     */
    public function getSessionToken()
    {
        if (!isset($this->sessionToken)) {
            $this->sessionToken = md5(uniqid(rand(), true));
            setcookie('session_token', $this->sessionToken, time() + $this->sessionExpire);
        }

        return $this->sessionToken;
    }

    /**
     * Generate session id from session key
     *
     * @return string
     */
    protected function getSessionId()
    {
        if (!isset($this->sessionToken)) return null;
        return "SSO-{$this->broker}-{$this->sessionToken}-" . md5('session' . $this->sessionToken . $this->request->getClientIp() . $this->secret);
    }

    /**
     * Get URL to attach session at SSO server
     *
     * @return string
     */
    public function getAttachUrl()
    {
        $token = $this->getSessionToken();
        $checksum = md5("attach{$token}{$this->request->getClientIp()}{$this->secret}");
        return "{$this->url}?cmd=attach&broker={$this->broker}&token=$token&checksum=$checksum";
    }


    /**
     * Login at sso server.
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login($username=null, $password=null)
    {
        if (!isset($username) && $this->request->get('username')) {
            $username = $this->request->get('username');
        }
        if (!isset($password) && $this->request->get('password')) {
            $password = $this->request->get('password');
        }

        list($ret, $body) = $this->serverCmd('login', array('username'=>$username, 'password'=>$password));

        return $body;
    }

    /**
     * Logout at sso server.
     */
    public function logout()
    {
        list($ret, $body) = $this->serverCmd('logout');
        if ($ret != 200) throw new \Exception("SSO failure: The server responded with a $ret status" . (!empty($body) ? ': "' . substr(str_replace("\n", " ", trim(strip_tags($body))), 0, 256) .'".' : '.'));

        return true;
    }

    /**
     * Get user information.
     */
    public function getInfo()
    {
        if (!isset($this->userinfo)) {
            list($ret, $body) = $this->serverCmd('info');

            return $body;
        }

        return $this->userinfo;
    }

    /**
     * Execute on SSO server.
     *
     * @param string $cmd   Command
     * @param array  $vars  Post variables
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function serverCmd($cmd, $vars=null)
    {
        $curl = curl_init($this->url . '?cmd=' . urlencode($cmd) .'&PHPSESSID=' . $this->getSessionId());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIE, "PHPSESSID=" . $this->getSessionId());

        if (isset($vars)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $vars);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $body = curl_exec($curl);
        $ret = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl) != 0) {
            throw new \Exception("SSO failure: HTTP request to server failed. " . curl_error($curl));
        }

        return array($ret, $body);
    }

    public function setConfig($config)
    {
        $this->broker = $config['broker_identifier'];
        $this->secret = $config['broker_secret'];
        $this->url    = $config['server_url'];
    }
}