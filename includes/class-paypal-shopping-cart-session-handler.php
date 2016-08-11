<?php

if (!defined('ABSPATH')) {
    exit;
}

class PSC_Session_Handler {

    public $_sess_encrypt_cookie = FALSE;
    public $_sess_use_database = FALSE;
    public $_sess_table_name = '';
    public $_sess_expiration = 7200;
    public $_sess_expire_on_close = FALSE;
    public $_sess_match_ip = FALSE;
    public $_ip_address = FALSE;
    public $_user_agent = FALSE;
    public $_sess_match_useragent = TRUE;
    public $_sess_cookie_name = 'psc_session';
    public $_cookie_prefix = '';
    public $_cookie_path = '/';
    public $_cookie_domain = '';
    public $_cookie_secure = FALSE;
    public $_sess_time_to_update = 300;
    public $_encryption_key = '';
    public $_flashdata_key = 'flash';
    public $_time_reference = 'time';
    public $_gc_probability = 5;
    public $_userdata = array();
    public $_now;
    public $_PSC_Encrypt_Obj;

    public function __construct($params = array()) {


        // Do we need encryption? If so, load the encryption class
        if ($this->_sess_encrypt_cookie == TRUE) {
            $this->_PSC_Encrypt_Obj = new PSC_Encrypt();
        }

        // Set the "now" time.  Can either be GMT or server time, based on the
        // config prefs.  We use this to set the "last activity" time
        $this->_now = $this->_get_time();

        // Set the session length. If the session expiration is
        // set to zero we'll set the expiration two years from now.
        if ($this->_sess_expiration == 0) {
            $this->_sess_expiration = (60 * 60 * 24 * 365 * 2);
        }

        // Set the cookie name
        $this->_sess_cookie_name = $this->_cookie_prefix . $this->_sess_cookie_name;

        // Run the Session routine. If a session doesn't exist we'll
        // create a new one.  If it does, we'll update it.
        if (!$this->sess_read()) {
            $this->sess_create();
        } else {
            $this->sess_update();
        }

        // Delete 'old' flashdata (from last request)
        $this->_flashdata_sweep();

        // Mark all new flashdata as old (data will be deleted before next request)
        $this->_flashdata_mark();
    }

    public function sess_read() {
        // Fetch the cookie
        $session = $this->cookie($this->_sess_cookie_name);
        $session = str_replace('\\', '', $session);
        // No cookie?  Goodbye cruel world!...
        if ($session === FALSE) {
            return FALSE;
        }

        // Decrypt the cookie data
        if ($this->_sess_encrypt_cookie == TRUE) {
            $this->_PSC_Encrypt_Obj = new PSC_Encrypt();
            $session = $this->_PSC_Encrypt_Obj->decode($session);
        } else {
            // encryption was not used, so we need to check the md5 hash
            $hash = substr($session, strlen($session) - 32); // get last 32 chars
            $session = substr($session, 0, strlen($session) - 32);

            // Does the md5 hash match?  This is to prevent manipulation of session data in userspace
            $match_hash = md5($session . $this->_encryption_key);
            if ($hash != $match_hash) {
                $this->sess_destroy();
                return FALSE;
            }
        }

        // Unserialize the session array
        $session = $this->_unserialize($session);

        // Is the session data we unserialized an array with the correct format?
        if (!is_array($session) OR !isset($session['session_id']) OR !isset($session['ip_address']) OR !isset($session['user_agent']) OR !isset($session['last_activity'])) {
            $this->sess_destroy();
            return FALSE;
        }

        // Is the session current?
        if (($session['last_activity'] + $this->_sess_expiration) < $this->_now) {
            $this->sess_destroy();
            return FALSE;
        }

        // Does the IP Match?
        if ($this->_sess_match_ip == TRUE AND $session['ip_address'] != $this->ip_address()) {
            $this->sess_destroy();
            return FALSE;
        }

        // Does the User Agent Match?
        if ($this->_sess_match_useragent == TRUE AND trim($session['user_agent']) != trim(substr($this->user_agent(), 0, 120))) {
            $this->sess_destroy();
            return FALSE;
        }
        // Session is valid!
        $this->_userdata = $session;
        unset($session);

        return TRUE;
    }

    public function cookie($index = '', $xss_clean = FALSE) {
        return $this->_fetch_from_array($_COOKIE, $index, $xss_clean);
    }

    public function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE) {
        if (!isset($array[$index])) {
            return FALSE;
        }

        if ($xss_clean === TRUE) {
            //return $this->security->xss_clean($array[$index]);
        }

        return $array[$index];
    }

    public function _get_time() {
        if (strtolower($this->_time_reference) == 'gmt') {
            $now = time();
            $time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
        } else {
            $time = time();
        }
        return $time;
    }

    public function sess_destroy() {
        // Kill the cookie
        if (!headers_sent()) {
            setcookie($this->_sess_cookie_name, addslashes(serialize(array())), ($this->_now - 31500000), $this->_cookie_path, $this->_cookie_domain, 0);
        } else {
        ob_start();
        setcookie($this->_sess_cookie_name, addslashes(serialize(array())), ($this->_now - 31500000), $this->_cookie_path, $this->_cookie_domain, 0);
        ob_get_clean();
        }
        // Kill session data
        $this->_userdata = array();
    }

    public function _unserialize($data) {
        $data = @unserialize(strip_slashes($data));

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_string($val)) {
                    $data[$key] = str_replace('{{slash}}', '\\', $val);
                }
            }

            return $data;
        }

        return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
    }

    public function ip_address() {
        if ($this->_ip_address !== FALSE) {
            return $this->_ip_address;
        }

        //   $proxy_ips = config_item('proxy_ips');
        if (!empty($proxy_ips)) {
            $proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
            foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header) {
                if (($spoof = $this->server($header)) !== FALSE) {
                    // Some proxies typically list the whole chain of IP
                    // addresses through which the client has reached us.
                    // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                    if (strpos($spoof, ',') !== FALSE) {
                        $spoof = explode(',', $spoof, 2);
                        $spoof = $spoof[0];
                    }

                    if (!$this->valid_ip($spoof)) {
                        $spoof = FALSE;
                    } else {
                        break;
                    }
                }
            }

            $this->_ip_address = ($spoof !== FALSE && in_array($_SERVER['REMOTE_ADDR'], $proxy_ips, TRUE)) ? $spoof : $_SERVER['REMOTE_ADDR'];
        } else {
            $this->_ip_address = $_SERVER['REMOTE_ADDR'];
        }



        return $this->_ip_address;
    }

    public function user_agent() {
        if ($this->_user_agent !== FALSE) {
            return $this->_user_agent;
        }

        $this->_user_agent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];

        return $this->_user_agent;
    }

    public function sess_create() {
        $sessid = '';
        while (strlen($sessid) < 32) {
            $sessid .= mt_rand(0, mt_getrandmax());
        }

        // To make the session ID even more secure we'll combine it with the user's IP
        $sessid .= $this->ip_address();

        $this->_userdata = array(
            'session_id' => md5(uniqid($sessid, TRUE)),
            'ip_address' => $this->ip_address(),
            'user_agent' => substr($this->user_agent(), 0, 120),
            'last_activity' => $this->_now,
            'user_data' => ''
        );
        // Write the cookie
        $this->_set_cookie();
    }

    public function sess_update() {
        // We only update the session every five minutes by default
        if (($this->_userdata['last_activity'] + $this->_sess_time_to_update) >= $this->_now) {
            return;
        }

        // Save the old session id so we know which record to
        // update in the database if we need it
        $old_sessid = $this->_userdata['session_id'];
        $new_sessid = '';
        while (strlen($new_sessid) < 32) {
            $new_sessid .= mt_rand(0, mt_getrandmax());
        }

        // To make the session ID even more secure we'll combine it with the user's IP
        $new_sessid .= $this->ip_address();

        // Turn it into a hash
        $new_sessid = md5(uniqid($new_sessid, TRUE));

        // Update the session data in the session data array
        $this->_userdata['session_id'] = $new_sessid;
        $this->_userdata['last_activity'] = $this->_now;

        // _set_cookie() will handle this for us if we aren't using database sessions
        // by pushing all userdata to the cookie.
        $cookie_data = NULL;

        // Write the cookie
        $this->_set_cookie($cookie_data);
    }

    public function _flashdata_sweep() {
        $this->_userdata = $this->all_userdata();
        foreach ($this->_userdata as $key => $value) {
            if (strpos($key, ':old:')) {
                $this->unset_userdata($key);
            }
        }
    }

    public function all_userdata() {
        return $this->_userdata;
    }

    public function _flashdata_mark() {
        $this->_userdata = $this->all_userdata();
        foreach ($this->_userdata as $name => $value) {
            $parts = explode(':new:', $name);
            if (is_array($parts) && count($parts) === 2) {
                $new_name = $this->_flashdata_key . ':old:' . $parts[1];
                $this->set_userdata($new_name, $value);
                $this->unset_userdata($name);
            }
        }
    }

    public function _set_cookie($cookie_data = NULL) {

        if (is_null($cookie_data)) {
            $cookie_data = $this->_userdata;
        }

        // Serialize the userdata for the cookie
        $cookie_data = $this->_serialize($cookie_data);

        if ($this->_sess_encrypt_cookie == TRUE) {
            $cookie_data = $this->_PSC_Encrypt_Obj->encode($cookie_data);
        } else {

            $cookie_data = $cookie_data . md5($cookie_data . $this->_encryption_key);
        }

        $expire = ($this->_sess_expire_on_close === TRUE) ? 0 : $this->_sess_expiration + time();
        if (!headers_sent()) {
            setcookie($this->_sess_cookie_name, $cookie_data, $expire, $this->_cookie_path, $this->_cookie_domain, $this->_cookie_secure);
        } else {
        // Set the cookie
        ob_start();
        setcookie($this->_sess_cookie_name, $cookie_data, $expire, $this->_cookie_path, $this->_cookie_domain, $this->_cookie_secure);
        ob_get_clean();
    }
    }

    public function _serialize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_string($val)) {
                    $data[$key] = str_replace('\\', '{{slash}}', $val);
                }
            }
        } else {
            if (is_string($data)) {
                $data = str_replace('\\', '{{slash}}', $data);
            }
        }

        return serialize($data);
    }

    public function unset_userdata($newdata = array()) {
        if (is_string($newdata)) {
            $newdata = array($newdata => '');
        }

        if (count($newdata) > 0) {
            foreach ($newdata as $key => $val) {
                unset($this->_userdata[$key]);
            }
        }

        $this->sess_write();
    }

    public function set_userdata($newdata = array(), $newval = '') {
        if (is_string($newdata)) {
            $newdata = array($newdata => $newval);
        }

        if (count($newdata) > 0) {
            foreach ($newdata as $key => $val) {
                $this->_userdata[$key] = $val;
            }
        }

        $this->sess_write();
    }

    public function sess_write() {

        $this->_set_cookie();
        return;
    }

    public function userdata($item) {
        return (!isset($this->_userdata[$item])) ? FALSE : $this->_userdata[$item];
    }

}