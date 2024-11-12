<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\NcmController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FormaPaymentsController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\BanckController;
use Illuminate\Support\Facades\Route;


/*Routas de Login*/
Route::post('login', [LoginController::class, 'login'])->name('login');
/*Rota publica porem secreta diponibilizar apenas para o dono do sistema*/
Route::post('enterprise-create', [EnterpriseController::class, 'store']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    ### Rotas login
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    ### Fim

    ### Rotas para empresa nossos clientes
    /*Rotas supoer admim*/
    route::delete('enterprise-delete/{enterprise}', [EnterpriseController::class, 'destroy']);
    Route::put('enterprise-edit-super/{enterprise}', [EnterpriseController::class, 'updateSuper']);
    route::put('enterprise-validate/{enterprise}', [EnterpriseController::class, 'updateValidate']);
    Route::get('enterprises', [EnterpriseController::class, 'show']);
    /*fim*/

    /*Routas para administradores*/
    Route::get('enterprise-profile', [EnterpriseController::class, 'showProfile']);
    Route::put('enterprise-edit/{enterprise}', [EnterpriseController::class, 'update']);
    /*fim*/
    ### FIM


    ### Routas para gereciar os usuarios
    //Routas de Usuarios
    Route::post('user-create', [UserController::class, 'store']);
    Route::put('user-edit/{user}', [UserController::class, 'update']);
    Route::put('user-password/{user}', [UserController::class, 'updatePassoword']);
    Route::get('user-profile', [UserController::class, 'showProfile']);
    Route::get('users', [UserController::class, 'show']);
    Route::get('user/{user}', [UserController::class, 'showUser']);
    Route::delete('user-delete/{user}', [UserController::class, 'destroy']);

    /*FIM*/
    ###Fim

    ## Rotas de clientes
    Route::get('clients', [ClientController::class, 'show']);
    Route::get('providers', [ClientController::class, 'showProviders']);
    Route::get('inactive', [ClientController::class, 'showInactive']);
    Route::get('clients/{client}', [ClientController::class, 'showClient']);
    Route::post('client-create', [ClientController::class, 'store']);
    Route::put('client-edit/{client}', [ClientController::class, 'update']);
    Route::put('client-status/{client}', [ClientController::class, 'updateStatus']);
    ##Fim

    ## Rota de categorias
    Route::get('categories', [CategoryController::class, 'show']);
    Route::get('categories-inactive', [CategoryController::class, 'showInctive']);
    Route::get('category/{category}', [CategoryController::class, 'showCategory']);
    Route::put('category-edit/{category}', [CategoryController::class, 'update']);
    Route::put('inactive-category/{category}', [CategoryController::class, 'updateInactive']);
    Route::post('category-create', [CategoryController::class, 'store']);
    ##FIM

    ## Rota de Unidade de Medidas
    Route::get('units', [UnitController::class, 'show']);
    Route::get('unit/{unit}', [UnitController::class, 'showUnit']);
    Route::get('units-inative', [UnitController::class, 'showInative']);
    Route::post('unit-create', [UnitController::class, 'store']);
    Route::put('unit-edit/{unit}', [UnitController::class, 'update']);
    Route::put('unit-edit-status/{unit}', [UnitController::class, 'updateStatus']);
    ##

    ## Rota de Unidade NCM
    Route::get('ncms', [NcmController::class, 'show']);
    Route::get('ncms-inactive', [NcmController::class, 'showInativo']);
    Route::post('ncm-create', [NcmController::class, 'store']);
    Route::put('ncm-edit/{ncm}', [NcmController::class, 'update']);
    Route::put('ncm-edit-status/{ncm}', [NcmController::class, 'updateStatus']);
    ##FIM

    ## Rota para cadastro de produtos
    Route::get('products', [ProductController::class, 'show']);
    Route::get('product/{product}', [ProductController::class, 'showProduct']);
    Route::get('products-inactive', [ProductController::class, 'showInativo']);
    Route::post('product-create', [ProductController::class, 'store']);
    Route::put('product-edit/{product}', [ProductController::class, 'update']);
    Route::put('product-edit-status/{product}', [ProductController::class, 'updateStatus']);
    Route::put('product-edit-price/{product}', [ProductController::class, 'updatePrice']);
    #FIM

    ##Rota Cadastro de Bancos
    Route::get('banks', [BanckController::class, 'show']);
    Route::get('banks-inativo', [BanckController::class, 'showInativo']);
    Route::post('bank-create', [BanckController::class, 'store']);
    Route::put('bank-edit/{banck}', [BanckController::class, 'update']);
    Route::put('bank-status/{banck}', [BanckController::class, 'updateStatus']);
    ##FIM

    ##Rota Formas de Pagamentos
    Route::get('payments', [FormaPaymentsController::class, 'show']);
    Route::get('payments-inativo', [FormaPaymentsController::class, 'showInativo']);
    Route::post('payment-create', [FormaPaymentsController::class, 'store']);
    Route::put('payment-edit/{payment}', [FormaPaymentsController::class, 'update']);
    Route::put('payment-status/{payment}', [FormaPaymentsController::class, 'updateStatus']);
    ##Fim

    ## Rota Movimento de estoque
    Route::get('products-manage', [StockController::class, 'showManage']);
    Route::get('providers-manage', [StockController::class, 'showProvider']);
    Route::post('manage-stock', [StockController::class, 'store']);
    Route::get('stocks', [StockController::class, 'show']);
    ##Fim

    ##Contas a pagar

    ##Fim
});

