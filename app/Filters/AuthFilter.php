<?php 
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
	public function before(RequestInterface $request, $arguments = null)
	{
		
		$router = service('router');
        $controller = $router->controllerName();
        $method = $router->methodName();	
		
		if (!session()->get('logged_in')) {
			return redirect()->to('user/login'); // Redirect to login page
		}
		
		if(strstr($method,'admin')){
			if(session()->get('role') != 'admin'){
				return redirect()->to('user/login'); // Redirect to login page
			}
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		// Do nothing after the request
	}
}