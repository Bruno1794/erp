<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\EnterpriseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


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

    /*FIM*/
    ###Fim
});

