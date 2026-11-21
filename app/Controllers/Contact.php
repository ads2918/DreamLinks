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
            'name'    => 'required|min_length[3]|max_length[100]',
            'email'   => 'required|valid_email',
            'message' => 'required|max_length[5000]'
        ])) {
            return $this->index();
        }

        // Send from our own address (Gmail rejects spoofed senders); replies go to the visitor.
        $name = $this->request->getPost('name');
        $from = $this->request->getPost('email');
        $email = \Config\Services::email();
        $email->setTo($this->admin_email);
        $email->setFrom($this->admin_email, 'DreamLinks contact form');
        $email->setReplyTo($from, $name);
        $email->setSubject('Contact Form Submission');
        $email->setMessage('<b>Name:</b> '. esc($name) .'<br><b>Email:</b> '. esc($from) .'<br><b>Message:</b><br>'. nl2br(esc($this->request->getPost('message'))));

        if ($email->send()) {
            return redirect()->to('/contact')->with('status', 'Message sent successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to send message.');
        }
    }
}