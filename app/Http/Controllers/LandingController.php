<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class LandingController extends Controller
{
    public function __invoke() {
        return Inertia::render('Landing');
    }
}
