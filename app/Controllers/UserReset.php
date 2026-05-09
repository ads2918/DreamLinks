<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class UserReset extends BaseController {

    // STEP 1: Show the "Forgot Password" Email Form
    public function forgotPassword(){
		if(!$this->session->user_id){
			return view('templates/header', ['title' => 'Forgot Password'])
				 . view('users/forget-password')
				 . view('templates/footer');
		}
    }

    // STEP 2: Process the Email and Send the Link
    public function sendResetLink() {
		if(!$this->session->user_id){
			$email = $this->request->getPost('email');
			$model = model(UserModel::class);
			$user  = $model->where('email', $email)->first();

			if ($user) {
				// Generate a secure token
				$token = bin2hex(random_bytes(16));
				$expiry = Time::now()->addHours(1);

				// Save token to users table (you'll need reset_token and reset_expires columns)
				$model->update($user['id'], [
					'reset_token'   => $token,
					'reset_expires' => $expiry->toDateTimeString()
				]);

				// Send Email
				$resetLink = base_url("user/reset-password/$token");
				$message = "Click here to reset your password: <a href='$resetLink'>$resetLink</a>";
				
				// Use your existing send_email method
				$this->send_email($email, 'Password Reset Request', $message, 1);
			}

			// Always show success to prevent email harvesting
			session()->setFlashdata('success', 'If that email exists, a reset link has been sent.');
			return redirect()->to(base_url('user/login'));
		}
    }

    // STEP 3: Show the "New Password" Form
    public function resetPassword($token = null) {
		if(!$this->session->user_id){
			$model = model(UserModel::class);
			$user = $model->where('reset_token', $token)
						  ->where('reset_expires >=', Time::now()->toDateTimeString())
						  ->first();

			if (!$user) {
				session()->setFlashdata('error', 'Invalid or expired token.');
				return redirect()->to(base_url('user/login'));
			}

			return view('templates/header', ['title' => 'New Password'])
				 . view('users/reset-form', ['token' => $token])
				 . view('templates/footer');
		}
    }

    // STEP 4: Save the New Password
    public function updatePassword() {
		if(!$this->session->user_id){
			$token = $this->request->getPost('token');
			$model = model(UserModel::class);

			$rules = [
				'password'  => 'required|min_length[8]|matches[password2]',
				'password2' => 'required',
			];

			if (!$this->validate($rules)) {
				// We MUST pass 'validation' manually here
				return view('templates/header', ['title' => 'New Password'])
					 . view('users/reset-form', [
						 'token'      => $this->request->getPost('token'),
						 'validation' => $this->validator // Crucial!
					   ])
					 . view('templates/footer');
			}

			$user = $model->where('reset_token', $token)->first();

		   if ($user) {
				$model->update($user['id'], [
					'password'      => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
					'reset_token'   => null, 
					'reset_expires' => null
				]);

				// --- THIS PART WAS MISSING ---
				session()->setFlashdata('success', 'Your password has been updated. Please login.');
				return redirect()->to(base_url('user/login')); 
				// -----------------------------
			} else {
				session()->setFlashdata('error', 'Session expired or invalid user.');
				return redirect()->to(base_url('user/login'));
			}
		}
	}
}