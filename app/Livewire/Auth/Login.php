<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
     public $email, $password, $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];
    public function submitLogin(){
        $this->validate();
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // Redirect to the intended URL or a default page
            session()->flash('message', 'Login successful!');
            return $this->redirect(session()->pull('url.intended', route('home')), navigate: true);
        }
        // dd('error');

        // Authentication failed
        session()->flash('error', 'Email atau password salah');
        $this->password = '';
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}