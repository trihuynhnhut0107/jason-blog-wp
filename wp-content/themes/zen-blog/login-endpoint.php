<?php
// Register a custom REST route for login
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/login', array(
        'methods' => 'POST',
        'callback' => 'custom_login_api',
        'permission_callback' => '__return_true',
    ));
    register_rest_route('custom/v1', '/secure-data', array(
        'methods' => 'GET',
        'callback' => 'get_secure_data',
    ));
});

// Callback function for login
function custom_login_api($request)
{
    $parameters = $request->get_json_params();
    $username = $parameters['username'];
    $password = $parameters['password'];

    if (empty($username) || empty($password)) {
        return new WP_Error('missing_fields', 'Username or password is missing', array('status' => 400));
    }

    $user = wp_authenticate($username, $password);

    if (is_wp_error($user)) {
        return new WP_Error('invalid_credentials', 'Invalid username or password', array('status' => 403));
    }

    // Generate an authentication cookie
    $expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user->ID, true);
    $cookie = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');
    setcookie(LOGGED_IN_COOKIE, $cookie, $expiration, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
    // Set the current user
    wp_set_current_user($user->ID);

    // Set the authentication cookie
    wp_set_auth_cookie($user->ID, true);

    $current_user = wp_get_current_user();

    $user_id = $current_user->ID;
    // if ($current_user->ID == $user->ID) {
    //     echo 'User successfully logged in and set as current user.';
    // } else {
    //     echo 'Failed to set current user.';
    // }
    // Check if the 'token' meta exists for the user
    $token_meta = get_user_meta($user_id, 'token', true);

    if (empty($token_meta)) {
        // Generate a new token or define a token value
        $new_token = 0; // Generating a token

        // Add the 'token' meta to the user
        update_user_meta($user_id, 'token', $new_token);
    }


    return array(
        'status' => 'success',
        'cookie' => $cookie,
        'user_id' => $user->ID,
        'username' => $user->user_login,
    );
}



function check_user_logged_in()
{
    $current_user = wp_get_current_user();
    
    if ($current_user->ID === 0) {
        return new WP_Error('not_logged_in', 'You must be logged in to access this data.', array('status' => 401));
    }

    return array(
        'ID' => $current_user->ID,
        'username' => $current_user->user_login,
        'email' => $current_user->user_email,
        'roles' => $current_user->roles,
    );
}

function get_secure_data()
{
    return wp_get_current_user();
}

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/signup', array(
        'methods' => 'POST',
        'callback' => 'handle_user_registration',
        'permission_callback' => '__return_true',
    ));
});

function handle_user_registration(WP_REST_Request $request)
{
    $username = sanitize_text_field($request->get_param('username'));
    $password = $request->get_param('password');
    $email = sanitize_email($request->get_param('email'));

    // Check for empty fields
    if (empty($username) || empty($password) || empty($email)) {
        return new WP_Error('empty_field', 'Please enter field ', array('status' => 400));
    }

    // Check if the username already exists
    if (username_exists($username)) {
        return new WP_Error('username_exists', 'Username existed', array('status' => 400));
    }

    // Check if the email is valid
    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'It is not an email', array('status' => 400));
    }

    // Check if the email already exists
    if (email_exists($email)) {
        return new WP_Error('email_exists', 'Email existed', array('status' => 400));
    }

    // Register the user
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        return new WP_Error('registration_failed', 'server error', array('status' => 500));
    }

    // Check if the 'token' meta exists for the user
    $token_meta = get_user_meta($user_id, 'token', true);

    if (empty($token_meta)) {
        // Generate a new token or define a token value
        $new_token = 0; // Generating a token

        // Add the 'token' meta to the user
        update_user_meta($user_id, 'token', $new_token);
    }




    return array(
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email,
        'status' => 200,
    );
}

function custom_user_logout()
{
    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Log the user out
        wp_logout();

        // Return a response indicating success
        return new WP_REST_Response([
            'success' => true,
            'message' => 'User has been logged out.'
        ], 200);
    }

    // If the user is not logged in
    return new WP_REST_Response([
        'success' => false,
        'message' => 'User is not logged in.'
    ], 403);
}

// Register the REST API endpoint
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/logout', array(
        'methods' => 'POST',
        'callback' => 'custom_user_logout',
        'permission_callback' => '__return_true', // Adjust permissions as needed
    ));
});