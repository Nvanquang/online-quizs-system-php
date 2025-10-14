<?php

class HomeController extends Controller
{
    public function index()
    {

        // Get users list for debug (optional)
        $users = [];
        try {
            $userRepo = new UserRepository();
            $users = $userRepo->findAll([], 'created_at DESC', 20);
        } catch (Exception $e) {
            // Database error
        }
        
        echo $this->render('home/index', [
            'title' => 'Trang Chủ - Quiz System',
            'users' => $users,
        ]);
    }

}


