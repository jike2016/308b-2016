<?php

/**
 * @file
 *
 * OAuth2 Library PDO DB Implementation.
 *
 */

// Set these values to your database access info.

require_once '../config.php';

global $CFG;

define("PDO_DSN", "mysql:dbname=".$CFG->dbname.";host=".$CFG->dbhost);
define("PDO_USER", $CFG->dbuser);
define("PDO_PASS", $CFG->dbpass);

// 配置数据库
//define("PDO_DSN", "mysql:dbname=moodle;host=localhost");
//define("PDO_USER", "root");
//define("PDO_PASS", "root");

require_once 'lib/OAuth2.class.php';

/**
 * OAuth2 Library PDO DB Implementation.
 */
class ZWPDOOAuth2 extends OAuth2 {

    private $db;

//    保存数据库表名
    private $clients = 'mdl_clients';
    private $tokens = 'mdl_tokens';
    private $auth_codes = 'mdl_auth_codes';

    /**
     * Overrides OAuth2::__construct().
     */
    public function __construct() {
        parent::__construct();

        try {
            $this->db = new PDO(PDO_DSN, PDO_USER, PDO_PASS);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Release DB connection during destruct.
     */
    function __destruct() {
        $this->db = NULL; // Release db connection
    }

    /**
     * Handle PDO exceptional cases.
     */
    private function handleException($e) {
        echo "Database error: " . $e->getMessage();
        exit;
    }

    /**
     * Little helper function to add a new client to the database.
     *
     * Do NOT use this in production! This sample code stores the secret
     * in plaintext!
     *
     * @param $client_id
     *   Client identifier to be stored.
     * @param $client_secret
     *   Client secret to be stored.
     * @param $redirect_uri
     *   Redirect URI to be stored.
     */
    public function addClient($client_id, $client_secret, $redirect_uri) {
        try {
            $sql = "INSERT INTO ".$this->clients." (client_id, client_secret, redirect_uri) VALUES (:client_id, :client_secret, :redirect_uri)";
            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(":clients", $this->clients, PDO::PARAM_STR);
            $stmt->bindParam(":client_id", $client_id, PDO::PARAM_STR);
            $stmt->bindParam(":client_secret", $client_secret, PDO::PARAM_STR);
            $stmt->bindParam(":redirect_uri", $redirect_uri, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Implements OAuth2::checkClientCredentials().
     *
     * Do NOT use this in production! This sample code stores the secret
     * in plaintext!
     * 实现了OAuth2::检查客户端凭证（）。
     * 不要在生产中使用这个！此示例代码存储的秘密
     * 明文！
     *
     */
    protected function checkClientCredentials($client_id, $client_secret = NULL) {
        try {
            $sql = "SELECT client_secret FROM ".$this->clients." WHERE client_id = :client_id";
            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(":clients", $this->clients, PDO::PARAM_STR);
            $stmt->bindParam(":client_id", $client_id, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($client_secret === NULL)
                return $result !== FALSE;

            return $result["client_secret"] == $client_secret;
        } catch (PDOException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Implements OAuth2::getRedirectUri().
     */
    protected function getRedirectUri($client_id) {
        try {
            $sql = "SELECT redirect_uri FROM ".$this->clients." WHERE client_id = :client_id";
            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(":clients", $this->clients, PDO::PARAM_STR);
            $stmt->bindParam(":client_id", $client_id, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result === FALSE)
                return FALSE;

            return isset($result["redirect_uri"]) && $result["redirect_uri"] ? $result["redirect_uri"] : NULL;
        } catch (PDOException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Implements OAuth2::getAccessToken().
     */
    protected function getAccessToken($oauth_token) {
        try {
            $sql = "SELECT client_id, expires, scope FROM ".$this->tokens." WHERE oauth_token = :oauth_token";
            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(":tokens", $this->tokens, PDO::PARAM_STR);
            $stmt->bindParam(":oauth_token", $oauth_token, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result !== FALSE ? $result : NULL;
        } catch (PDOException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Implements OAuth2::setAccessToken().
     */
    protected function setAccessToken($oauth_token, $client_id, $expires, $scope = NULL) {
        try {
            $sql = "INSERT INTO ".$this->tokens." (oauth_token, client_id, expires, scope) VALUES (:oauth_token, :client_id, :expires, :scope)";
            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(":tokens", $this->tokens, PDO::PARAM_STR);
            $stmt->bindParam(":oauth_token", $oauth_token, PDO::PARAM_STR);
            $stmt->bindParam(":client_id", $client_id, PDO::PARAM_STR);
            $stmt->bindParam(":expires", $expires, PDO::PARAM_INT);
            $stmt->bindParam(":scope", $scope, PDO::PARAM_STR);

            $stmt->execute();
        } catch (PDOException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Overrides OAuth2::getSupportedGrantTypes().
     */
    protected function getSupportedGrantTypes() {
        return array(
            OAUTH2_GRANT_TYPE_AUTH_CODE, //授权码模式(即先登录获取code,再获取token)
//        OAUTH2_GRANT_TYPE_USER_CREDENTIALS,
//        OAUTH2_GRANT_TYPE_ASSERTION,
            OAUTH2_GRANT_TYPE_REFRESH_TOKEN, //刷新access_token
            OAUTH2_GRANT_TYPE_NONE
        );
    }

    /**
     * Overrides OAuth2::getAuthCode().
     */
    protected function getAuthCode($code) {
        try {
            $sql = "SELECT code, client_id, redirect_uri, expires, scope FROM ".$this->auth_codes." WHERE code = :code";
            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(":auth_codes", $this->auth_codes, PDO::PARAM_STR);
            $stmt->bindParam(":code", $code, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result !== FALSE ? $result : NULL;
        } catch (PDOException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Overrides OAuth2::setAuthCode().
     */
    protected function setAuthCode($code, $client_id, $redirect_uri, $expires, $scope = NULL) {
        try {
            $sql = "INSERT INTO ".$this->auth_codes." (code, client_id, redirect_uri, expires, scope) VALUES (:code, :client_id, :redirect_uri, :expires, :scope)";
            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(":auth_codes", $this->auth_codes, PDO::PARAM_STR);
            $stmt->bindParam(":code", $code, PDO::PARAM_STR);
            $stmt->bindParam(":client_id", $client_id, PDO::PARAM_STR);
            $stmt->bindParam(":redirect_uri", $redirect_uri, PDO::PARAM_STR);
            $stmt->bindParam(":expires", $expires, PDO::PARAM_INT);
            $stmt->bindParam(":scope", $scope, PDO::PARAM_STR);

            $stmt->execute();
        } catch (PDOException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Return supported scopes.
     *
     * If you want to support scope use, then have this function return a list
     * of all acceptable scopes (used to throw the invalid-scope error).
     *
     * @return
     *   A list as below, for example:
     * @code
     * return array(
     *   'my-friends',
     *   'photos',
     *   'whatever-else',
     * );
     * @endcode
     *
     * @ingroup oauth2_section_3
     */
    protected function getSupportedScopes() {
        return array('whatever-else');
    }

    /**
     * Check restricted authorization response types of corresponding Client
     * identifier.
     *
     * If you want to restrict clients to certain authorization response types,
     * override this function.
     *
     * 检查限制授权响应类型的相应客户端标识符。
     *
     * @param $client_id
     *   Client identifier to be check with.
     * @param $response_type
     *   Authorization response type to be check with, would be one of the
     *   values contained in OAUTH2_AUTH_RESPONSE_TYPE_REGEXP.
     *
     * @return
     *   TRUE if the authorization response type is supported by this
     *   client identifier, and FALSE if it isn't.
     *
     * @ingroup oauth2_section_3
     */
    protected function checkRestrictedAuthResponseType($client_id, $response_type) {
        return TRUE;
    }

    /**
     * Check restricted grant types of corresponding client identifier.
     *
     * 检查限制批类型相应的客户端标识符。
     *
     * If you want to restrict clients to certain grant types, override this
     * function.
     *
     * @param $client_id
     *   Client identifier to be check with.
     * @param $grant_type
     *   Grant type to be check with, would be one of the values contained in
     *   OAUTH2_GRANT_TYPE_REGEXP.
     *
     * @return
     *   TRUE if the grant type is supported by this client identifier, and
     *   FALSE if it isn't.
     *
     * @ingroup oauth2_section_4
     */
    protected function checkRestrictedGrantType($client_id, $grant_type) {
        return TRUE;
    }

    /**
     * Grant access tokens for basic user credentials.
     *
     * Check the supplied username and password for validity.
     *
     * You can also use the $client_id param to do any checks required based
     * on a client, if you need that.
     *
     * Required for OAUTH2_GRANT_TYPE_USER_CREDENTIALS.
     *
     * @param $client_id
     *   Client identifier to be check with.
     * @param $username
     *   Username to be check with.
     * @param $password
     *   Password to be check with.
     *
     * @return
     *   TRUE if the username and password are valid, and FALSE if it isn't.
     *   Moreover, if the username and password are valid, and you want to
     *   verify the scope of a user's access, return an associative array
     *   with the scope values as below. We'll check the scope you provide
     *   against the requested scope before providing an access token:
     * @code
     * return array(
     *   'scope' => <stored scope values (space-separated string)>,
     * );
     * @endcode
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-10#section-4.1.2
     *
     * @ingroup oauth2_section_4
     */
    protected function checkUserCredentials($client_id, $username, $password) {
        return FALSE;
    }

    /**
     * Grant access tokens for assertions.
     *
     * Check the supplied assertion for validity.
     *
     * You can also use the $client_id param to do any checks required based
     * on a client, if you need that.
     *
     * Required for OAUTH2_GRANT_TYPE_ASSERTION.
     *
     * @param $client_id
     *   Client identifier to be check with.
     * @param $assertion_type
     *   The format of the assertion as defined by the authorization server.
     * @param $assertion
     *   The assertion.
     *
     * @return
     *   TRUE if the assertion is valid, and FALSE if it isn't. Moreover, if
     *   the assertion is valid, and you want to verify the scope of an access
     *   request, return an associative array with the scope values as below.
     *   We'll check the scope you provide against the requested scope before
     *   providing an access token:
     * @code
     * return array(
     *   'scope' => <stored scope values (space-separated string)>,
     * );
     * @endcode
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-10#section-4.1.3
     *
     * @ingroup oauth2_section_4
     */
    protected function checkAssertion($client_id, $assertion_type, $assertion) {
        return FALSE;
    }

    /**
     * Grant refresh access tokens.
     *
     * 刷新 access tokens.
     *
     * Retrieve the stored data for the given refresh token.
     *
     * Required for OAUTH2_GRANT_TYPE_REFRESH_TOKEN.
     *
     * @param $refresh_token
     *   Refresh token to be check with.
     *
     * @return
     *   An associative array as below, and NULL if the refresh_token is
     *   invalid:
     *   - client_id: Stored client identifier.
     *   - expires: Stored expiration unix timestamp.
     *   - scope: (optional) Stored scope values in space-separated string.
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-10#section-4.1.4
     *
     * @ingroup oauth2_section_4
     */
    protected function getRefreshToken($refresh_token) {
        return NULL;
    }

    /**
     * Take the provided refresh token values and store them somewhere.
     *
     * This function should be the storage counterpart to getRefreshToken().
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * Required for OAUTH2_GRANT_TYPE_REFRESH_TOKEN.
     *
     * @param $refresh_token
     *   Refresh token to be stored.
     * @param $client_id
     *   Client identifier to be stored.
     * @param $expires
     *   expires to be stored.
     * @param $scope
     *   (optional) Scopes to be stored in space-separated string.
     *
     * @ingroup oauth2_section_4
     */
    protected function setRefreshToken($refresh_token, $client_id, $expires, $scope = NULL) {
//    protected function setAccessToken($oauth_token, $client_id, $expires, $scope = NULL) {
        return ;
    }

    /**
     * Expire a used refresh token.
     *
     * This is not explicitly required in the spec, but is almost implied.
     * After granting a new refresh token, the old one is no longer useful and
     * so should be forcibly expired in the data store so it can't be used again.
     *
     * If storage fails for some reason, we're not currently checking for
     * any sort of success/failure, so you should bail out of the script
     * and provide a descriptive fail message.
     *
     * @param $refresh_token
     *   Refresh token to be expirse.
     *
     * @ingroup oauth2_section_4
     */
    protected function unsetRefreshToken($refresh_token) {
        return;
    }

    /**
     * Grant access tokens for the "none" grant type.
     *
     * Not really described in the IETF Draft, so I just left a method
     * stub... Do whatever you want!
     *
     * Required for OAUTH2_GRANT_TYPE_NONE.
     *
     * @ingroup oauth2_section_4
     */
    protected function checkNoneAccess($client_id) {
        return FALSE;
    }

    /**
     * Get default authentication realm for WWW-Authenticate header.
     *
     * Change this to whatever authentication realm you want to send in a
     * WWW-Authenticate header.
     *
     * @return
     *   A string that you want to send in a WWW-Authenticate header.
     *
     * @ingroup oauth2_error
     */
    protected function getDefaultAuthenticationRealm() {
        return "Service";
    }

}
