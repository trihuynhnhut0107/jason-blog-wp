<?php
// Register a custom REST route for login
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/login', array(
        'methods' => 'POST',
        'callback' => 'custom_login_api',
        'permission_callback' => '__return_true',
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
	if ($user_id == 11) {
		$new_token = 25;
		update_user_meta($user_id, 'token', $new_token);
	}
	
	return array(
        'status' => 'success',
        'cookie' => $cookie,
        'user_id' => $user->ID,
        'username' => $user->user_login,
    );
}

// Example endpoint that requires authentication
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/secure-data', array(
        'methods' => 'GET',
        'callback' => 'get_secure_data',
        'permission_callback' => 'check_user_logged_in',
    ));
});

function check_user_logged_in()
{
    $cookie = isset($_COOKIE['user']) ? $_COOKIE['user'] : '';
    wp_set_current_user(wp_validate_auth_cookie($cookie, 'logged_in'));
    $current_user = wp_get_current_user();
    if (wp_validate_auth_cookie($cookie, 'logged_in')) {
        return true;
    } else {
        return new WP_Error('not_logged_in', 'You are not logged in', array('status' => 403));
    }

}

function get_secure_data()
{
    return array('data' => 'This is secure data',
				'status' => '200');
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
function check_for_cookie() {
    // Check if the specific cookie exists
    if (isset($_COOKIE['user'])) {
        $cookie_value = $_COOKIE['user'];

        // Do something with the cookie value
        return 'Cookie value: ' . $cookie_value;
    } else {
        // Handle the case where the cookie is not set
        return 'Cookie not found';
    }
}

// Example usage within a REST API endpoint
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/check-cookie', array(
        'methods' => 'GET',
        'callback' => 'check_for_cookie',
    ));
});




