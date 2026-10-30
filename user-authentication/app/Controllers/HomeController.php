<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use Throwable;

final class HomeController extends Controller
{
    // View and Database are autowired by the container.
    public function __construct(View $view, private readonly Database $db)
    {
        parent::__construct($view);
    }

    public function index(Request $request): Response
    {
        return $this->render('home/index', ['title' => 'Welcome']);
    }

    /** Verifies the database connection works. */
    public function health(Request $request): Response
    {
        try {
            $this->db->fetchOne('SELECT 1 AS ok');
            return $this->json(['status' => 'ok', 'database' => 'connected']);
        } catch (Throwable $e) {
            return $this->json(['status' => 'error', 'database' => 'unreachable'], 503);
        }
    }
}
