<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\User;
use App\Controllers\Dream;
use App\Controllers\Link;
use App\Controllers\Block;
use App\Controllers\MoonCycle;
use App\Controllers\Dreamscape;
use App\Controllers\Reminder;
/**
 * @var RouteCollection $routes
 */


/* prior to authentication */
$routes->get('/', 'Home::index');
$routes->get('user', [User::class, 'index']);
$routes->get('user/register',[User::class,'register']);
$routes->post('user',[User::class,'create']);
$routes->get('user/login',[User::class,'login']);
$routes->post('user/login',[User::class,'loginSubmit']);
$routes->get('user/confirm/(:segment)',[User::class,'confirm']);
$routes->get('/learn', 'learn::index');
$routes->get('contact', 'Contact::index');
$routes->post('contact/send', 'Contact::send');

$routes->get('user/forgot-password', 'UserReset::forgotPassword');
$routes->post('user/sendResetLink', 'UserReset::sendResetLink');
$routes->get('user/reset-password/(:any)', 'UserReset::resetPassword/$1');
$routes->post('user/updatePassword', 'UserReset::updatePassword');

/* deleting an account works signed in (password) or signed out (emailed link) */
$routes->get('account/delete', 'Account::delete');
$routes->post('account/delete', 'Account::deleteSubmit');
$routes->get('account/delete/confirm/(:segment)', 'Account::confirm/$1');
$routes->post('account/delete/confirm', 'Account::confirmSubmit');
$routes->get('account/deleted', 'Account::deleted');

/* user logged in -- anything that changes data is POST so the CSRF filter covers it */
$routes->post('user/logout',[User::class,'logout'],['filter' => 'auth']);
$routes->get('user/edit',[User::class,'edit'],['filter' => 'auth']);
$routes->post('user/edit',[User::class,'editSubmit'],['filter' => 'auth']);
$routes->post('user/request-link/(:segment)',[Link::class,'requestLink'],['filter' => 'auth']);
$routes->post('user/accept-link/(:segment)',[Link::class,'acceptLink'],['filter' => 'auth']);
$routes->post('user/decline-link/(:segment)',[Link::class,'declineLink'],['filter' => 'auth']);
$routes->post('user/block-link/(:segment)',[Block::class,'blockLink'],['filter' => 'auth']);
$routes->get('user/(:segment)/blocks',[User::class,'blocks'],['filter' => 'auth']);
$routes->post('user/(:segment)/unblock',[Block::class,'unblock'],['filter' => 'auth']);


$routes->get('user/(:segment)/dreams',[User::class,'dreams'],['filter' => 'auth']);
$routes->post('user/(:segment)/dreams',[User::class,'dreamsTable'],['filter' => 'auth']);
$routes->post('user/(:segment)/unlink',[Link::class,'unlink'],['filter' => 'auth']);
$routes->get('user/(:segment)/analysis',[User::class,'dream_analysis'],['filter' => 'auth']);
$routes->post('readings/now',[User::class,'readNow'],['filter' => 'auth']);
$routes->get('user/(:num)/dreamscape',[Dreamscape::class,'index'],['filter' => 'auth']);
$routes->get('user/(:segment)/links',[User::class,'links'],['filter' => 'auth']);
$routes->get('user/(:segment)/account',[User::class,'accountMenu'],['filter' => 'auth']);
$routes->get('user/(:segment)',[User::class,'profile'],['filter' => 'auth']);
$routes->get('report', 'Report::index', ['filter' => 'auth']);
$routes->post('report', 'Report::submit', ['filter' => 'auth']);
$routes->get('search', [User::class, 'search'],['filter' => 'auth']);
$routes->post('search', [User::class, 'searchTable'],['filter' => 'auth']);

$routes->get('dream/add',[Dream::class,'add'],['filter' => 'auth']);
$routes->post('dream/add',[Dream::class,'addSubmit'],['filter' => 'auth']);
$routes->post('dream/delete/(:segment)',[Dream::class,'delete'],['filter' => 'auth']);
$routes->post('dream/(:num)/resonate',[Dream::class,'resonate'],['filter' => 'auth']);
$routes->get('dream/edit/(:segment)', [Dream::class, 'edit'],['filter' => 'auth']);
$routes->post('dream/edit/(:segment)', [Dream::class, 'editSubmit'],['filter' => 'auth']);

/* admin routes -- if admin in method name filter will require filter role */
$routes->get('admin',[User::class,'admin'],['filter' => 'auth']);
$routes->get('admin/users',[User::class,'admin_users'],['filter' => 'auth']);
$routes->post('admin/approve-user/(:segment)',[User::class,'admin_user_approve'],['filter' => 'auth']);

/* cron jobs */
$routes->cli('cron/ai-user-dream-run',[User::class,'aiUserDreamRun']);
$routes->cli('cron/moon/logcycles',[MoonCycle::class,'logcycles']);
$routes->cli('cron/reminders',[Reminder::class,'send']);


/* mobile app API: JSON only, Bearer token sign-in (see app/Filters/ApiAuth.php) */
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
	$routes->post('auth/login', 'Auth::login');

	$routes->group('', ['filter' => 'apiauth'], static function ($routes) {
		$routes->post('auth/logout', 'Auth::logout');
		$routes->get('me', 'Me::show');
		$routes->post('me', 'Me::update');
		$routes->post('me/delete', 'Me::delete');

		$routes->get('feed', 'Dreams::feed');
		$routes->get('dreams', 'Dreams::index');
		$routes->post('dreams', 'Dreams::create');
		$routes->get('dreams/(:num)', 'Dreams::show/$1');
		$routes->put('dreams/(:num)', 'Dreams::update/$1');
		$routes->delete('dreams/(:num)', 'Dreams::delete/$1');
		$routes->post('dreams/(:num)/resonate', 'Dreams::resonate/$1');

		$routes->get('users/(:num)', 'People::show/$1');
		$routes->post('users/(:num)/link', 'People::requestLink/$1');
		$routes->get('search', 'People::search');
		$routes->get('links', 'People::links');
		$routes->post('links/(:num)/(accept|decline)', 'People::answer/$1/$2');
		$routes->delete('links/(:num)', 'People::unlink/$1');
		$routes->post('users/(:num)/block', 'People::block/$1');
		$routes->get('blocks', 'People::blocks');
		$routes->delete('blocks/(:num)', 'People::unblock/$1');
		$routes->post('reports', 'People::report');

		$routes->get('dreamscape', 'Insights::dreamscape');
		$routes->get('readings', 'Insights::readings');
		$routes->post('readings/now', 'Insights::readNow');
		$routes->get('symbols', 'Insights::symbols');
	});
});
$routes->set404Override('App\Controllers\Errors::show404');
