<?php

class HomeController extends Controller
{
    private $quizService;

    public function __construct()
    {
        parent::__construct();
        $this->quizService = QuizServiceImpl::getInstance();
    }

    public function index()
    {
        // Get current user
        $user = null;
        try {
            $auth = Auth::getInstance();
            $user = $auth->user();
        } catch (Exception $e) {
            // User not logged in or error
        }

        // Get all quizzes
        $quizzes = $this->quizService->getAll();

        $login_success = null;
        if (isset($_SESSION['login_success'])) {
            $login_success = $_SESSION['login_success'];
            unset($_SESSION['login_success']);  // Xóa để tránh lặp nếu refresh
        }

        echo $this->renderPartial('home/index', [
            'title' => 'Trang Chủ - Quiz System',
            'login_success' => $login_success,
            'user' => $user,
            'quizzes' => $quizzes,
        ]);
    }

    public function test()
    {
        echo $this->renderPartial('home/test', [
            'title' => 'Test PHP Info',
        ]);
    }

    public function study(){
        $link=@mysqli_connect("localhost","root","27072004","quiz_system");
        mysqli_select_db($link,"quiz_system");
        $sl="select * from quiz_system.game_sessions";
        $result=mysqli_query($link,$sl);
        // echo "<table border=1; width=500; align=center ; cellspacing=0>";
        // echo "<tr><th>ID</th><th>Ten</th><th>User name</th><th>Email</th></tr>";
        // while($row=mysqli_fetch_array($result)){
        //     echo "<tr>";
        //     echo "<td>".$row['id']."</td>";
        //     echo "<td>".$row['full_name']."</td>";
        //     echo "<td>".$row['username']."</td>";
        //     echo "<td>".$row['email']."</td>";
        //     echo "</tr>";
        // }
        // echo "</table>";
        echo $this->renderPartial('home/study', ['result' => $result]);
    }

    public function studyDetail($id){
         $link=@mysqli_connect("localhost","root","27072004","quiz_system");
        mysqli_select_db($link,"quiz_system");
        $sl="select * from quiz_system.game_sessions where id = $id";
        $result=mysqli_query($link,$sl);
        echo $this->renderPartial('home/studyDetail', ['result' => $result]);
    }
}
