<?php
require_once 'models/categoriesmodel.php';
require_once 'models/expensesmodel.php';

class Dashboard extends SessionController{
    private $user;

    function __construct(){
        parent::__construct();
       $this->user = $this->getUserSessionData();
        error_log('Dashboard::construct -> inicio de Dashboard');
        
        //parent::__construct();
    }
    function render(){
        error_log('Dashboard::render -> carga el index del dashboard');

        $expensesModel          = new ExpensesModel();
        $expenses               = $this->getExpenses(5);
        $totalThisMonth         = $expensesModel->getTotalAmountThisMonth($this->user->getId());
        $maxExpensesThisMonth   = $expensesModel->getMaxExpensesThisMonth($this->user->getId());
        $categories             = $this->getCategories();


        error_log('Total This Month: ' . print_r($totalThisMonth, true));
        error_log('Max Expense This Month: ' . print_r($maxExpensesThisMonth, true));
        
    $this->view->render('dashboard/index', [
        'user'                 => $this->user,
        'expenses'             => $expenses,
        'totalAmountThisMonth' => $totalThisMonth,
        'maxExpensesThisMonth' => $maxExpensesThisMonth,
        'categories'           => $categories
    ]);

    }
// cambios 29-25-2024
    private function getExpenses($n = 0){
        if($n < 0) return NULL;
        //error_log("Dashboard::getExpenses() id = " . $this->user->getId());
        $expenses = new ExpensesModel();
        return $expenses->getByUserIdAndLimit($this->user->getId(), $n);

    }

    private function getCategories(){


        $res = [];
        $categoriesModel = new CategoriesModel();
        $expensesModel = new ExpensesModel();

        $categories = $categoriesModel->getAll();

        foreach ($categories as $category) {
            $categoryArray = [];
            //obtenemos la suma de amount de expenses por categoria
            $total = $expensesModel->getTotalByCategoryThisMonth($category->getId(), $this->user->getId());
            // obtenemos el número de expenses por categoria por mes
            $numberOfExpenses = $expensesModel->getNumberOfExpensesByCategoryThisMonth($category->getId(), $this->user->getId());
            
            if($numberOfExpenses > 0){
                $categoryArray['total'] = $total;
                $categoryArray['count'] = $numberOfExpenses;
                $categoryArray['category'] = $category;
                array_push($res, $categoryArray);
            }
            
        }
        return $res;

        
    }

}
?>
