<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use Override;
use Throwable;


final class DashboardController extends Controller {
    
    public function __construct(View $view)
    {
        return parent::__construct($view);
    }

    public function showDashboard(){
        return $this->render('dashboard/index' , ['data' => 'Welcome! You are logged in!']);
    }

}