<?php

require_once __DIR__ . '/Database.php';

class User
{
    private $db;
    private $username;
    private $password;
    private $user_id;
    private $user_data;

    public function __construct($username = null, $password = null)
    {
        $this->db = new Database();
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Login user
     */
    public function login($username = null, $password = null)
    {
        if ($username !== null) $this->username = $username;
        if ($password !== null) $this->password = $password;

        // Cek user di tabel 'user' (bukan 'users')
        $users = $this->db->get('user', "username='" . $this->db->escape($this->username) . "'");
        
        if (!empty($users)) {
            $user = $users[0];
            
            // Password masih plain text (admin123, vivi123, ari123)
            if ($this->password === $user['password']) {
                $this->user_id = $user['id_user'];
                $this->user_data = $user;
                return $user;
            }
        }
        
        return false;
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return $this->db->get('user', null);
    }

    /**
     * Get user by ID
     */
    public function getUserById($id_user)
    {
        $users = $this->db->get('user', "id_user='" . $this->db->escape($id_user) . "'");
        return !empty($users) ? $users[0] : false;
    }

    /**
     * Get user by username
     */
    public function getUserByUsername($username)
    {
        $users = $this->db->get('user', "username='" . $this->db->escape($username) . "'");
        return !empty($users) ? $users[0] : false;
    }

    /**
     * Add new user
     */
    public function addUser($username, $password, $additionalData = [])
    {
        $data = array_merge([
            'username' => $username,
            'password' => $password, // Plain text - bisa dihash nanti
            'created_at' => date('Y-m-d H:i:s')
        ], $additionalData);
        
        return $this->db->insert('user', $data);
    }

    /**
     * Update user
     */
    public function updateUser($id_user, $data)
    {
        return $this->db->update('user', $data, "id_user='" . $this->db->escape($id_user) . "'");
    }

    /**
     * Delete user
     */
    public function deleteUser($id_user)
    {
        return $this->db->delete('user', "id_user='" . $this->db->escape($id_user) . "'");
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username)
    {
        $users = $this->db->get('user', "username='" . $this->db->escape($username) . "'");
        return !empty($users);
    }

    /**
     * Change password
     */
    public function changePassword($id_user, $newPassword)
    {
        $data = [
            'password' => $newPassword // Plain text - bisa dihash
        ];
        return $this->updateUser($id_user, $data);
    }

    /**
     * Get total users count
     */
    public function getTotalUsers()
    {
        return $this->db->count('user');
    }

    /**
     * Verify user credentials
     */
    public function verifyCredentials($username, $password)
    {
        return $this->login($username, $password);
    }

    /**
     * Get current user ID
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Get current user data
     */
    public function getUserData()
    {
        return $this->user_data;
    }
}
?>