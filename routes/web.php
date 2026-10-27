<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Auth\RegistrationController;


Route::view('/', 'pages.home')->name('home');

Route::view('/about', 'pages.about')->name('about');

Route::view('/contact', 'pages.contact')->name('contact');

Route::view('/jobs', 'pages.jobs.index')->name('jobs.index');

Route::post('/contact', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'email', 'max:255'],
        'message' => ['required', 'string', 'max:5000'],
    ]);

    // Contact submission is validated, but not yet saved or emailed.
    // Connect a mail service or database model to process it.

    return back()->with(
        'success',
        'Your message has been validated successfully.'
    );
})->name('contact.submit');


Route::view('/login', 'auth.login')->name('login');

Route::get('/register', [RegistrationController::class, 'create'])
    ->middleware('throttle:10,1')
    ->name('register');

Route::post('/register', [RegistrationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('register.store');

Route::get('/register/verify', [RegistrationController::class, 'showVerification'])
    ->middleware('throttle:20,1')
    ->name('register.verify.form');

Route::post('/register/verify', [RegistrationController::class, 'verify'])
    ->middleware('throttle:10,1')
    ->name('register.verify');

Route::post('/register/resend', [RegistrationController::class, 'resend'])
    ->middleware('throttle:3,1')
    ->name('register.resend');
