<?php


class DBConnection
{
    const NAME = 'name';
    const USERNAME = 'username';
    const AGE = 'age';
    const BIRTH_DATE = 'birth_date';
    const BIO = 'bio';
    const PROFILE_IMAGE = 'profile_image_path';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const SERVER_NAME = 'localhost';
    const DB_USERNAME_LOCAL = 'root';
    const DB_PASSWORD_LOCAL = 'root';
    const SCHEMA_LOCAL = 'prax4';
    const DB_USERNAME = 'st2014';
    const DB_PASSWORD = 'progress';
    const SCHEMA = 'st2014';

    private $connection;

    /**
     * Establish the connection with the database.
     *
     * DBConnection constructor.
     */
    function __construct()
    {
        try {
            $this->connection = new PDO(
                'mysql:host=' . self::SERVER_NAME . ';dbname=' . self::SCHEMA_LOCAL . ';charset=utf8',
                self::DB_USERNAME_LOCAL,self::DB_PASSWORD_LOCAL
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Check whether there is already db row with specified value.
     *
     * @param string $value - Value to check.
     * @param string $to_check - Value to search from the db.
     * @return bool - true if exists, false otherwise.
     */
    function exists(string $value, string $to_check) {
        $statement = "SELECT $to_check FROM user_info WHERE $to_check = ?";
        $response = $this->make_query($statement, [$value]);
        return !empty($response);
    }

    /**
     * Prepare and perform the database query in order to obtain
     * the credentials for the provided username if such exist.
     *
     * @param string $username - Username for which to perform the query.
     * @return array - Array with credentials if provided username exists.
     */
    function perform_login_query(string $username) {
        $statement = 'SELECT id, username, password FROM user_info WHERE username = ?';
        $response = $this->make_query($statement, [$username]);

        if (!$response) {
            return [];
        }
        return $response;
    }

    /**
     * Prepare and perform the database query in order to insert the data
     * provided by the register form and therefore register the user.
     *
     * @param string $name - Name of the new user.
     * @param string $username - Username of the new user, must be unique.
     * @param string $email - Email of the new user, must be unique.
     * @param string $password_hash - Password hash created from the user password.
     */
    function perform_register_query(string $name, string $username, string $email, string $password_hash) {
        $statement = 'INSERT INTO user_info (
                             name, username, email, password, profile_image_path
                             ) VALUES (?, ?, ?, ?, ?)';
        $options = [
            $name, $username, $email, $password_hash, get_random_file()
        ];
        $this->make_query($statement, $options);
    }

    /**
     * Get information about the user.
     *
     * @param string $id - User id.
     * @param $info_type - The type of information.
     * Acceptable types are:
     * 1. User profile image
     * 2. User real name
     * 3. Username
     * 4. User bio
     * 5. User birth date.
     * 6. User password.
     * 7. User age.
     *
     * @return string - Some information about the user requested by caller.
     */
    function retrieve_user_information(string $id, $info_type) {
        $statement = "SELECT id, $info_type FROM user_info WHERE id = ?";
        $response =  $this->make_query($statement, [$id])[0][$info_type];
        return $response == null ? '' : $response;
    }

    /**
     * Save updated user information to the db.
     *
     * @param string $id - User id.
     * @param $info_type - Type of the information.
     * Acceptable types are:
     * 1. User profile image
     * 2. User real name
     * 3. Username
     * 4. User bio
     * 5. User birth date.
     * 6. User password.
     * 7. User age.
     *
     * @param $new_value - A new value to insert into the db.
     */
    function update_user_information(string $id, $info_type, $new_value) {
        $statement = "UPDATE user_info SET $info_type = ? WHERE id = ?";
        $this->make_query($statement, [$new_value, $id]);
    }

    /**
     * Delete all information associated with the user.
     * Delete both the account of the user and all posted switts,
     * information about followers and following etc.
     *
     * @param string $id - User id.
     */
    function delete_account(string $id) {
        $statement = "DELETE FROM user_info WHERE id = ?";
        $this->make_query($statement, [$id]);
        $statement = "DELETE FROM switts1 WHERE author_id = ?";
        $this->make_query($statement, [$id]);
    }

    /**
     * Save the image uploaded by the user to the db.
     *
     * @param string $id - User id.
     * @param string $path - Path to the newly uploaded image.
     */
    function save_profile_image(string $id, string $path) {
        $statement = "UPDATE user_info SET profile_image_path = ? WHERE id = ?";
        $this->make_query($statement, [$path, $id]);
    }

    /**
     * Save the new switt to the db.
     *
     * @param string $author - Author of the switt.
     * @param string $author_id - Author id.
     * @param string $content - Switt content.
     */
    function create_new_switt(string $author, string $author_id, string $content) {
        $date = date('Y-m-d H:i:s');
        $statement = "INSERT INTO switts1 (author, author_id, post_date, content) VALUES (?, ?, ?, ?)";
        $this->make_query($statement, [$author, $author_id, $date, $content]);
    }

    /**
     * Get all switts posted by the user with specified id.
     *
     * @param string $id - User id.
     * @return array - Array with all user switts.
     */
    function get_all_user_switts(string $id) {
        $statement = "SELECT author, post_date, content, author_id FROM switts1 WHERE author_id = ?";
        return $this->make_query($statement, [$id]);
    }

    /**
     * Find all users whose username contains the key.
     *
     * @param string $key - Key to search in username.
     * @return array - Array of users.
     */
    function get_all_users_by_username(string $key) {
        $statement = "SELECT id, name, username, profile_image_path FROM user_info WHERE username LIKE '%{$key}%'";
        return $this->make_query($statement);
    }

    /**
     * Find all users who are followed by the user with provided id.
     *
     * @param string $id - Id of the user.
     * @return array - IDs of all users who are followed by this user.
     */
    function get_following_users(string $id) {
        $statement = "SELECT following FROM switter_followers WHERE follower = ?";
        return $this->make_query($statement, [$id]);
    }

    /**
     * Find all users who follow the user with provided id.
     *
     * @param string $id - Id of the user.
     * @return array - IDs of all users who follow this user.
     */
    function get_user_followers(string $id) {
        $statement = "SELECT follower FROM switter_followers WHERE following = ?";
        return $this->make_query($statement, [$id]);
    }

    function follow_user(string $user_id, string $user_id_to_follow) {
        $statement = "INSERT INTO switter_followers (follower, following) VALUES (?, ?)";
        $this->make_query($statement, [$user_id, $user_id_to_follow]);
    }

    function unfollow_user(string $user_id, string $user_id_to_unfollow) {
        $statement = "DELETE FROM prax4.switter_followers WHERE follower = ? AND following = ?";
        $this->make_query($statement, [$user_id, $user_id_to_unfollow]);
    }

    /**
     * Make the query to the db.
     *
     * @param string $statement - Statement to make.
     * @param array $options - Additional parameters to include while executing the query.
     * @return array - Response from the db.
     */
    private function make_query(string $statement, array $options = []) {
        $query = $this->connection->prepare($statement);
        $query->execute($options);
        $response = $query->fetchAll();
        $statement = null;
        return gettype($response) == 'array' ? $response : [];
    }
}
