<?php

namespace App\Controllers\Api;

use App\Filters\ApiAuth;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

/**
 * Base for the mobile app API (api/v1/...). Every response is JSON. Signed-in routes run
 * behind the 'apiauth' filter, which puts the user in ApiAuth::$user.
 */
abstract class ApiController extends Controller
{
	use ResponseTrait;

	protected $format = 'json';

	protected function user(): array
	{
		return ApiAuth::$user;
	}

	protected function uid(): int
	{
		return (int) ApiAuth::$user['id'];
	}

	/** The request body as an array: JSON when the app sends JSON, form fields otherwise. */
	protected function input(): array
	{
		$json = str_contains($this->request->getHeaderLine('Content-Type'), 'json')
			? json_decode((string) $this->request->getBody(), true)
			: null;
		return is_array($json) ? $json : (array) $this->request->getPost() + (array) $this->request->getRawInput();
	}

	protected function invalid(array $errors)
	{
		return $this->respond(['error' => reset($errors) ?: 'Check the form.', 'fields' => $errors], 422);
	}

	/** Public profile picture URL, or null for the default picture. */
	protected static function avatar(?string $image): ?string
	{
		return $image ? base_url('uploads/' . $image) : null;
	}

	/** A dream as the app sees it. Expects a row from DreamModel::visible_to() run through DreamFeed::decorate(). */
	protected function dream(array $d): array
	{
		return [
			'id'         => (int) $d['id'],
			'title'      => $d['title'],
			'text'       => $d['full_description'],
			'date'       => $d['date'],
			'visibility' => $d['visibility'],
			'owner'      => ['id' => (int) $d['created_by'], 'username' => $d['username'], 'avatar' => self::avatar($d['image'])],
			'own'        => (int) $d['created_by'] === $this->uid(),
			'tags'       => $d['tags'],
			'symbols'    => $d['symbols'],
			'moon'       => $d['moon'],
			'resonance'  => (int) $d['resonance'],
			'resonated'  => (bool) $d['resonated'],
		];
	}

	/** page / per_page query values, kept within sane limits. @return array{int, int} limit, offset */
	protected function paging(int $per_page = 20): array
	{
		$per_page = max(1, min(50, (int) ($this->request->getGet('per_page') ?: $per_page)));
		$page = max(1, (int) ($this->request->getGet('page') ?: 1));
		return [$per_page, ($page - 1) * $per_page];
	}
}
