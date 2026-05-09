<?php 
namespace App\Controllers;

use CodeIgniter\Controller;

class Contact extends BaseController {
    public function index() {
		$data['title'] = 'Contact Us';
		helper(['form']);
        return view('templates/header', $data).
			   view('pages/contactForm').
			   view('templates/footer', $data);
    }

    public function send() {
        // Validate user input
		helper(['form']);
        if (!$this->validate([
            'name'    => 'required|min_length[3]',
            'email'   => 'required|valid_email',
            'message' => 'required'
        ])) {
            return view('contact_form', ['validation' => $this->validator]);
        }

        // Send Email Logic (Simplified)
        $email = \Config\Services::email();
        $email->setTo('dreamlinks1111@gmail.com');
        $email->setFrom($this->request->getPost('email'), $this->request->getPost('name'));
        $email->setSubject('Contact Form Submission');
        $email->setMessage('<b>Name:</b>'. $this->request->getPost('name') .'<br><b>Email:</b>'. $this->request->getPost('email') .'<br><b>Message:</b>'. $this->request->getPost('message'));

        if ($email->send()) {
            return redirect()->to('/contact')->with('status', 'Message sent successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to send message.');
        }
    }
}