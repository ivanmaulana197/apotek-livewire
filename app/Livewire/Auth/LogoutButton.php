<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LogoutButton extends Component
{
    public $tipe;

    public function logout() {
        Auth::logout();
        // Invalidate the session and regenerate the CSRF token
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // Redirect to the home page or login page
        return $this->redirect(route('login'),navigate:true); // Update route as needed
    }

    public function render()
    {
        return view('livewire.auth.logout-button');
    }
}