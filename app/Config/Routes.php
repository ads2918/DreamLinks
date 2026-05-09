<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\User;
use App\Controllers\Dream;
use App\Controllers\Link;
use App\Controllers\Block;
use App\Controllers\MoonCycle;
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
$routes->get('/learn', 'learn::index');
$routes->get('contact', 'Contact::index');
$routes->post('contact/send', 'Contact::send');

$routes->get('user/forgot-password', 'UserReset::forgotPassword');
$routes->post('user/sendResetLink', 'UserReset::sendResetLink');
$routes->get('user/reset-password/(:any)', 'UserReset::resetPassword/$1');
$routes->post('user/updatePassword', 'UserReset::updatePassword');

/* user logged in */
$routes->get('user/logout',[User::class,'logout'],['filter' => 'auth']);
$routes->get('user/edit',[User::class,'edit'],['filter' => 'auth']);
$routes->post('user/edit',[User::class,'editSubmit'],['filter' => 'auth']);
$routes->post('user/request-link/(:segment)',[Link::class,'requestLink'],['filter' => 'auth']);
$routes->post('user/accept-link/(:segment)',[Link::class,'acceptLink'],['filter' => 'auth']);
$routes->post('user/decline-link/(:segment)',[Link::class,'declineLink'],['filter' => 'auth']);
$routes->get('user/block-link/(:segment)',[Block::class,'blockLink'],['filter' => 'auth']);
$routes->get('user/(:segment)/blocks',[User::class,'blocks'],['filter' => 'auth']);
$routes->get('user/(:segment)/unblock',[Block::class,'unblock'],['filter' => 'auth']);


$routes->get('user/(:segment)/dreams',[User::class,'dreams'],['filter' => 'auth']);
$routes->post('user/(:segment)/dreams',[User::class,'dreamsTable'],['filter' => 'auth']);
$routes->get('user/(:segment)/unlink',[Link::class,'unlink'],['filter' => 'auth']);
$routes->get('user/(:segment)/analysis',[User::class,'dream_analysis'],['filter' => 'auth']);
$routes->get('user/(:segment)/links',[User::class,'links'],['filter' => 'auth']);
$routes->get('user/(:segment)/account',[User::class,'accountMenu'],['filter' => 'auth']);
$routes->get('user/(:segment)',[User::class,'profile'],['filter' => 'auth']);
$routes->get('search', [User::class, 'search'],['filter' => 'auth']); 
$routes->post('search', [User::class, 'searchTable'],['filter' => 'auth']); 

$routes->get('dream/add',[Dream::class,'add'],['filter' => 'auth']);
$routes->post('dream/add',[Dream::class,'addSubmit'],['filter' => 'auth']);
$routes->get('dream/delete/(:segment)',[Dream::class,'delete'],['filter' => 'auth']); 
$routes->get('dream/edit/(:segment)', [Dream::class, 'edit'],['filter' => 'auth']); 
$routes->post('dream/edit/(:segment)', [Dream::class, 'editSubmit'],['filter' => 'auth']); 

/* admin routes -- if admin in method name filter will require filter role */
$routes->get('admin',[User::class,'admin'],['filter' => 'auth']);
$routes->get('admin/users',[User::class,'admin_users'],['filter' => 'auth']);
$routes->get('admin/approve-user/(:segment)',[User::class,'admin_user_approve'],['filter' => 'auth']);

/* cron jobs */
$routes->cli('cron/ai-user-dream-run',[User::class,'aiUserDreamRun']);
$routes->cli('cron/moon/logcycles',[MoonCycle::class,'logcycles']);

$routes->set404Override('App\Controllers\Errors::show404');





