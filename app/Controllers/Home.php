<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function __construct()
    {
        $this->usersModel = model('UsersModel');
    }

    public function get_users(): string
    {
        $data['title'] = 'Users';
        $data['page_title'] = 'Users';
        $data['users'] = $this->usersModel->findAll();
        return view('users', $data);
    }

    public function get_user_by_id($id): string
    {
        $user = $this->usersModel->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
        }

        $data['title'] = 'User';
        $data['page_title'] = 'User';
        $data['users'] = [$user];

        return view('users', $data);
    }

    public function get_user_by_username($username): string
    {
        $user = $this->usersModel->where('username', $username)->first();

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
        }

        $data['title'] = 'User';
        $data['page_title'] = 'User';
        $data['users'] = [$user];

        return view('users', $data);
    }

    public function create_user(): mixed
    {
        if ($this->request->getMethod() === 'POST') {
            $validation = $this->validate([
                'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
                'email' => 'required|valid_email|max_length[150]|is_unique[users.email]',
                'first_name' => 'required|max_length[100]',
                'last_name' => 'required|max_length[100]',
                'password' => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
            ]);
    
            if (!$validation) {
                $errors = $this->validator->getErrors();
                log_message('debug', 'Validation failed. Errors: ' . json_encode($errors));
                log_message('debug', 'Post data: ' . json_encode($this->request->getPost()));
                $data['errors'] = $errors;
                return view('create_user', $data);
            }
    
            // Get form data
            $postdata = $this->request->getPost();
            
            // Hash the password before storing
            $postdata['password'] = password_hash($postdata['password'], PASSWORD_DEFAULT);
            
            // Remove password_confirm as it's not a database field
            unset($postdata['password_confirm']);
    
            // Try to insert the user
            try {
                // Log the data being inserted (without password)
                $logData = $postdata;
                unset($logData['password']);
                log_message('debug', 'Attempting to insert user: ' . json_encode($logData));
                
                $insertId = $this->usersModel->insert($postdata);
                
                if ($insertId) {
                    log_message('info', 'User created successfully with ID: ' . $insertId);
                    $data['page_title'] = "Users Page";
                    $data['message'] = 'User created successfully';
                    $data['users'] = $this->usersModel->findAll();
                    return redirect()->to('/users');
                } else {
                    // Get model errors
                    $errors = $this->usersModel->errors();
                    log_message('error', 'Failed to insert user. Errors: ' . json_encode($errors));
                    $data['page_title'] = "Users Page";
                    $data['errors'] = $errors ?: ['Unknown error occurred while creating user'];
                    return view('create_user', $data);
                }
            } catch (\Exception $e) {
                log_message('error', 'Exception while creating user: ' . $e->getMessage());
                $data['page_title'] = "Users Page";
                $data['errors'] = ['Database error: ' . $e->getMessage()];
                return view('create_user', $data);
            }
        } else {
            $data['page_title'] = "Insert New User";
            return view('create_user', $data);
        }
    }

    public function update_user($id = null) {
        if ($this->request->getMethod() === 'POST') {
            // Get the user first
            $user = $this->usersModel->find($id);
            if (!$user) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
            }

            // Validation rules - password is optional
            $validationRules = [
                'username' => "required|min_length[3]|max_length[100]|is_unique[users.username,id,{$id}]",
                'email' => "required|valid_email|max_length[150]|is_unique[users.email,id,{$id}]",
                'first_name' => 'required|max_length[100]',
                'last_name' => 'required|max_length[100]',
            ];

            // Only validate password if it's provided
            $postdata = $this->request->getPost();
            if (!empty($postdata['password'])) {
                $validationRules['password'] = 'min_length[8]';
                $validationRules['password_confirm'] = 'matches[password]';
            }

            $validation = $this->validate($validationRules);

            if (!$validation) {
                $errors = $this->validator->getErrors();
                log_message('debug', 'Validation failed. Errors: ' . json_encode($errors));
                $data['errors'] = $errors;
                $data['title'] = 'Edit User';
                $data['page_title'] = 'Edit User';
                $data['user'] = $user;
                return view('edit_user', $data);
            }

            // Get form data
            $updateData = [
                'username' => $postdata['username'],
                'email' => $postdata['email'],
                'first_name' => $postdata['first_name'],
                'last_name' => $postdata['last_name'],
            ];

            // Only update password if provided
            if (!empty($postdata['password'])) {
                $updateData['password'] = password_hash($postdata['password'], PASSWORD_DEFAULT);
            }

            // Try to update the user
            try {
                $updated = $this->usersModel->update($id, $updateData);

                if ($updated) {
                    log_message('info', 'User updated successfully with ID: ' . $id);
                    return redirect()->to('/users')->with('message', 'User updated successfully');
                } else {
                    // Get model errors
                    $errors = $this->usersModel->errors();
                    log_message('error', 'Failed to update user. Errors: ' . json_encode($errors));
                    $data['errors'] = $errors ?: ['Unknown error occurred while updating user'];
                    $data['title'] = 'Edit User';
                    $data['page_title'] = 'Edit User';
                    $data['user'] = $user;
                    return view('edit_user', $data);
                }
            } catch (\Exception $e) {
                log_message('error', 'Exception while updating user: ' . $e->getMessage());
                $data['errors'] = ['Database error: ' . $e->getMessage()];
                $data['title'] = 'Edit User';
                $data['page_title'] = 'Edit User';
                $data['user'] = $user;
                return view('edit_user', $data);
            }
        } else {
            // Show edit form
            $user = $this->usersModel->find($id);
            if (!$user) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
            }
            $data['page_title'] = 'User Edit';
            $data['user'] = $user;
            return view('edit_user', $data);
        }
    }

    public function delete_user($id = null) {
        if ($id) {
            $this->usersModel->delete($id);
            return redirect()->to('/users')->with('message', 'User deleted successfully');
        }
        return redirect()->to('/users');
    }
}