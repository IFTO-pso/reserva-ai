<?php

use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rotas carregadas pelo RouteServiceProvider dentro do grupo de middleware
| "web", que inclui a proteção CSRF para requisições POST, PUT, PATCH e DELETE.
|
*/

Route::redirect('/', '/equipamentos');

Route::controller(EquipamentoController::class)
    ->prefix('equipamentos')
    ->name('equipamentos.')
    ->group(function () {
        Route::get('/', 'listar')->name('listar');
        Route::get('/novo', 'criar')->name('criar');
        Route::post('/', 'cadastrar')->name('cadastrar');
        Route::get('/{equipamento}', 'consultar')->whereNumber('equipamento')->name('consultar');
        Route::get('/{equipamento}/editar', 'editar')->whereNumber('equipamento')->name('editar');
        Route::put('/{equipamento}', 'atualizar')->whereNumber('equipamento')->name('atualizar');
        Route::get('/{equipamento}/excluir', 'confirmarExclusao')->whereNumber('equipamento')->name('confirmar-exclusao');
        Route::delete('/{equipamento}', 'excluir')->whereNumber('equipamento')->name('excluir');
    });

Route::controller(ReservaController::class)
    ->prefix('reservas')
    ->name('reservas.')
    ->group(function () {
        Route::get('/', 'listar')->name('listar');
        Route::get('/nova', 'criar')->name('criar');
        Route::post('/', 'cadastrar')->name('cadastrar');
        Route::get('/{reserva}', 'consultar')->whereNumber('reserva')->name('consultar');
        Route::get('/{reserva}/cancelar', 'confirmarCancelamento')->whereNumber('reserva')->name('confirmar-cancelamento');
        Route::patch('/{reserva}/cancelar', 'cancelar')->whereNumber('reserva')->name('cancelar');
    });
