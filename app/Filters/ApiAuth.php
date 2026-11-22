<?php

namespace App\Filters;

use App\Models\ApiTokenModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Lets an API request through only with "Authorization: Bearer <token>" from a signed-in app. */
class ApiAuth implements FilterInterface
{
	/** The signed-in user for this request, read by the API controllers. */
	public static ?array $user = null;

	public function before(RequestInterface $request, $arguments = null)
	{
		self::$user = null;
		$header = $request->getHeaderLine('Authorization');
		if (preg_match('/^Bearer\s+(\S+)$/i', $header, $m)) {
			self::$user = model(ApiTokenModel::class)->user_for($m[1]);
		}
		if (! self::$user) {
			return service('response')->setStatusCode(401)->setJSON(['error' => 'Sign in again.']);
		}
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
	}
}
