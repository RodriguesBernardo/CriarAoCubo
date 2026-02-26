<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\FinanceiroParticularController;
use App\Http\Controllers\DashboardPessoalController;
use App\Http\Controllers\HomeController; // <--- 1. ADICIONE ISSO

// 2. ALTERE AQUI: Redireciona para a nova Home (Visão Geral)
Route::get('/', function () {
    return redirect()->route('home');
});

// Rotas de Autenticação (Login, Register, etc)
Auth::routes();

Route::middleware(['auth'])->group(function () {
    
    // 3. NOVA ROTA HOME (O Cockpit Principal)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Dashboard Empresa (Criar³)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard Pessoal (Detalhado)
    Route::get('/dashboard-pessoal', [DashboardPessoalController::class, 'index'])->name('dashboard_pessoal.index');

    // --- Rotas da Empresa (Clientes, Produtos, Pedidos...) ---
    Route::resource('clientes', ClienteController::class);
    Route::get('/clientes/export/{type}', [ClienteController::class, 'export'])->name('clientes.export');
    Route::get('/clientes/search', [ClienteController::class, 'search'])->name('clientes.search');

    Route::resource('produtos', ProdutoController::class);
    Route::get('/produtos/{id}/download-stl', [ProdutoController::class, 'downloadStl'])->name('produtos.downloadStl');
    Route::get('/produtos/export/{type}', [ProdutoController::class, 'export'])->name('produtos.export');
    Route::get('/produtos/search', [ProdutoController::class, 'search'])->name('produtos.search');
    // Rota de imagem protegida
    Route::get('/storage/produtos/imagens/{filename}', function ($filename) {
        $path = storage_path('app/public/produtos/imagens/' . $filename);
        if (!file_exists($path)) abort(404);
        $file = File::get($path);
        $type = File::mimeType($path);
        return response($file, 200)->header('Content-Type', $type);
    })->name('produtos.imagem');

    Route::get('/orcamento/produto/{produto}', [OrcamentoController::class, 'gerarOrcamentoProduto'])->name('orcamento.produto');
    Route::get('/orcamentos/{id}/pdf', [OrcamentoController::class, 'gerarPdf'])->name('orcamentos.gerarPdf');

    Route::resource('pedidos', PedidoController::class);
    Route::post('/pedidos/{pedido}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.updateStatus');
    Route::get('pedidos/{pedido}/orcamento', [OrcamentoController::class, 'gerarPdf'])->name('pedidos.orcamento');
    Route::get('/pedidos/export/{type}', [PedidoController::class, 'export'])->name('pedidos.export');
    Route::delete('/pedidos/anexos/{anexo}', [PedidoController::class, 'removerAnexo'])->name('pedidos.removerAnexo');
    Route::get('/pedidos/{pedido}/download/{tipo}', [PedidoController::class, 'downloadArquivo'])->name('pedidos.download');
    Route::get('/pedidos/{pedido}/remover-arquivo/{tipo}', [PedidoController::class, 'removerArquivo'])->name('pedidos.remover-arquivo');
    Route::get('/pedidos/{id}/comprovante-entrega', [PedidoController::class, 'gerarComprovanteEntrega'])->name('pedidos.gerarComprovanteEntrega');
    Route::get('/pedidos/{id}/pdf', [PedidoController::class, 'gerarPdf'])->name('pedidos.gerarPdf');
    Route::patch('/pedidos/{pedido}/produtos/{produto}', [PedidoController::class, 'updateProdutoStatus'])->name('pedidos.updateProdutoStatus');
    Route::patch('/pedidos/{pedido}/pagamento', [PedidoController::class, 'updateStatusPagamento'])->name('pedidos.updateStatusPagamento');
    Route::post('/pedidos/{pedido}/archive', [PedidoController::class, 'archive'])->name('pedidos.archive');

    Route::resource('arquivos', ArquivoController::class)->only(['store', 'destroy']);

    // Financeiro Empresa
    Route::prefix('financeiro')->group(function () {
        Route::get('/', [FinanceiroController::class, 'index'])->name('financeiro.index');
        Route::get('/create', [FinanceiroController::class, 'create'])->name('financeiro.create');
        Route::post('/', [FinanceiroController::class, 'store'])->name('financeiro.store');
        Route::get('/{financeiro}/edit', [FinanceiroController::class, 'edit'])->name('financeiro.edit');
        Route::put('/{financeiro}', [FinanceiroController::class, 'update'])->name('financeiro.update');
        Route::delete('/{financeiro}', [FinanceiroController::class, 'destroy'])->name('financeiro.destroy');
    });

    // Relatórios Empresa
    Route::get('/relatorios', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/relatorios/gerar', [ReportController::class, 'generate'])->name('reports.generate');
    Route::post('/relatorios/exportar-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::post('/relatorios/exportar-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');

    // --- Área Pessoal ---

    // Calendário
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/api/calendario-events', [CalendarioController::class, 'getEvents']);
    Route::post('/calendario', [CalendarioController::class, 'store']);
    Route::put('/calendario/{id}', [CalendarioController::class, 'update']);
    Route::delete('/calendario/{id}', [CalendarioController::class, 'destroy']);

    // Financeiro Particular
    Route::resource('financeiro_particular', FinanceiroParticularController::class);
    Route::post('/financeiro_particular/{id}/pagar', [FinanceiroParticularController::class, 'pagar'])->name('financeiro_particular.pagar');
});