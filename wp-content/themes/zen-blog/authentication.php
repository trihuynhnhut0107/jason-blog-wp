<?php

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/login', array(
        'methods' => 'POST',
        'callback' => 'login',
        'permission_callback' => '__return_true'
    ));
    register_rest_route('custom/v1', '/permission', array(
        'methods' => 'GET',
        'callback' => 'permissionCheck',
    ));
    register_rest_route('custom/v1', '/logout', array(
        'methods' => 'POST',
        'callback' => 'logout'
    ));
    register_rest_route('custom/v1', '/signup', array(
        'methods' => 'POST',
        'callback' => 'signup',
    ));
});

function login($request)
{
    $parameters = $request->get_json_params();
    $username = isset($parameters['username']) ? sanitize_text_field($parameters['username']) : '';
    $password = isset($parameters['password']) ? $parameters['password'] : '';

    if (empty($username) || empty($password)) {
        return new WP_Error('missing_fields', 'Username or password is missing', array('status' => 400));
    }

    $user = wp_authenticate($username, $password);

    if (is_wp_error($user)) {
        return new WP_Error('invalid_credentials', 'Invalid username or password', array('status' => 403));
    }

    wp_set_auth_cookie($user->ID, true);
    wp_set_current_user($user->ID);

    $currentUser = wp_get_current_user();
    $userID = $currentUser->ID;
    
    

    $userToken = get_user_meta($userID, 'token', true);
    if (empty($userToken)) {
        update_user_meta($userID, 'token', 0);
        $userToken = 0;
    }
    

    return array(
        'status' => 'success',
        'userID' => $user->ID,
        'username' => $user->user_login,
        'token' => $userToken,
    );
}
function permissionCheck()
{
    if (isset($_COOKIE[LOGGED_IN_COOKIE]) && wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in')) {

        wp_set_current_user(wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in'));
        return array('loggedIn' => true);
    } else {
        return array('loggedIn' => false);
    }
}

function signup(WP_REST_Request $request)
{
    $username = sanitize_text_field($request->get_param('username'));
    $password = $request->get_param('password');
    $email = sanitize_email($request->get_param('email'));

    if (empty($username) || empty($password) || empty($email)) {
        return new WP_Error('empty_field', 'Please enter field ', array('status' => 400));
    }

    if (username_exists($username)) {
        return new WP_Error('username_exists', 'Username existed', array('status' => 400));
    }

    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'It is not an email', array('status' => 400));
    }

    if (email_exists($email)) {
        return new WP_Error('email_exists', 'Email existed', array('status' => 400));
    }

    $userID = wp_create_user($username, $password, $email);

    if (is_wp_error($userID)) {
        return new WP_Error('registration_failed', 'server error', array('status' => 500));
    }

    $token_meta = get_user_meta($userID, 'token', true);

    if (empty($token_meta)) {
        update_user_meta($userID, 'token', 0);
    }

    return array(
        'userID' => $userID,
        'username' => $username,
        'email' => $email,
        'status' => 200,
    );
}

function logout()
{
    if (isset($_COOKIE[LOGGED_IN_COOKIE]) && wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in')) {
        wp_logout();

        setcookie(LOGGED_IN_COOKIE, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        setcookie(LOGGED_IN_COOKIE, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN);

        return array('loggedIn' => false, 'message' => 'User successfully logged out');
    } else {
        return array('loggedIn' => false, 'message' => 'User is not logged in');
    }
}
