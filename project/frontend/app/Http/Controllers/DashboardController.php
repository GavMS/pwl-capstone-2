<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    private function getUser(): array
    {
        return Session::get('user', []);
    }

    public function admin()
    {
        return view('dashboard.admin', ['user' => $this->getUser()]);
    }

    public function kalab()
    {
        return view('dashboard.kalab', ['user' => $this->getUser()]);
    }

    public function kaprodi()
    {
        return view('dashboard.kaprodi', ['user' => $this->getUser()]);
    }

    public function stafadmin()
    {
        return view('dashboard.stafadmin', ['user' => $this->getUser()]);
    }

    public function staflab()
    {
        return view('dashboard.staflab', ['user' => $this->getUser()]);
    }
}
