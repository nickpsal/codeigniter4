<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Models\UserIdentityModel;

class UserController extends BaseController
{
    public function __construct()
    {
        //$this->usersModel = model('UsersModel');
    }

    public function get_users(): string
    {
        $data = [
            'title'      => 'Users List',
            'page_title' => 'Users List',
        ];

        // Shield user provider (αντί για UsersModel)
        $provider = auth()->getProvider();

        // φέρνει όλους τους users ως User Entities
        $data['users'] = $provider->findAll();

        return view('users', $data);
    }

    public function create_user(): mixed
    {
        if ($this->request->getMethod() !== 'POST') {
            return view('create_user', [
                'page_title' => 'Insert New User',
            ]);
        }

        // 1️⃣ Validation
        $rules = [
            'email'             => 'required|valid_email|max_length[150]',
            'username'          => 'required|min_length[3]|max_length[100]',
            'password'          => 'required|min_length[8]',
            'password_confirm'  => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return view('create_user', [
                'page_title' => 'Insert New User',
                'errors'     => $this->validator->getErrors(),
            ]);
        }

        // 2️⃣ Create User Entity
        $user = new User([
            'email'      => $this->request->getPost('email'),
            'username'   => $this->request->getPost('username'),
            'active'     => 1,
        ]);

        $users = new UserModel();

        $users->insert($user);

        $userId = $users->getInsertID();

        $identities = new UserIdentityModel();

        $identities->insert([
            'user_id' => $userId,
            'type'    => 'password',
            'secret'  => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
        ]);

        // 5️⃣ Auto login (optional)
        $createdUser = $users->findById($userId);
        auth()->login($createdUser);

        return redirect()->to('/users')->with('message', 'User created successfully');
    }

    public function update_user($id = null)
    {
        $users = new UserModel();

        // -------------------------
        // GET -> show edit form
        // -------------------------
        if ($this->request->getMethod() !== 'POST') {

            $user = $users->findById((int) $id);

            if (! $user) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
            }

            return view('edit_user', [
                'page_title' => 'User Edit',
                'userId'     => (int) $id, // το χρειάζεσαι για το JSON fill στο view
            ]);
        }

        // -------------------------
        // POST -> update
        // -------------------------
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|max_length[150]',
        ];

        // ✅ Αν δώσει password -> κάνε extra validation
        $newPassword = trim((string) $this->request->getPost('password'));
        if ($newPassword !== '') {
            $rules['password'] = 'min_length[8]';
            $rules['password_confirm'] = 'required|matches[password]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $user = $users->findById((int) $id);

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
        }

        // update users table (username)
        $user->username = $this->request->getPost('username');
        $users->save($user);

        $identities = new UserIdentityModel();

        // update email identity
        $identities->where('user_id', (int) $id)
            ->where('type', 'email_password')
            ->set('secret', $this->request->getPost('email'))
            ->update();

        // ✅ update password ONLY if provided
        if ($newPassword !== '') {
            $identities->where('user_id', (int) $id)
                ->where('type', 'password')
                ->set('secret', password_hash($newPassword, PASSWORD_DEFAULT))
                ->update();
        }

        return redirect()->to('/users')
            ->with('message', 'User updated successfully');
    }

    public function delete_user($id = null)
    {
        if ($id) {
            $users = new UserModel();
            $users->delete((int) $id);
            return redirect()->to('/users')->with('message', 'User deleted successfully');
        }
        return redirect()->to('/users');
    }
}
