<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Produto;
use App\Models\Cliente;
use Carbon\Carbon;
use PDF;
use Excel;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }
    
    public function generate(Request $request)
    {
        $request->validate([
            'data_source' => 'required|in:pedidos,produtos,clientes,itens_pedido',
            'x_axis' => 'required|string',
            'y_axis' => 'required|string',
            'chart_type' => 'required|in:bar,line,pie,doughnut,radar',
            'time_interval' => 'nullable|in:daily,weekly,monthly,yearly',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:100',
            'group_by' => 'nullable|string',
            'report_title' => 'required|string|max:255'
        ]);
        
        $data = [];
        $labels = [];
        $values = [];
        
        switch ($request->data_source) {
            case 'pedidos':
                $query = Pedido::query();
                
                // Aplicar filtro de data
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween($request->x_axis, [$request->start_date, $request->end_date]);
                }
                
                // Aplicar agrupamento por tempo
                if ($request->time_interval) {
                    $query = $this->applyTimeInterval($query, $request->time_interval, $request->x_axis);
                }
                
                // Aplicar agrupamento adicional
                if ($request->group_by) {
                    $query->selectRaw("{$request->group_by} as group_value, {$request->x_axis} as label, SUM({$request->y_axis}) as value")
                        ->groupBy('group_value', 'label');
                } else {
                    $query->selectRaw("{$request->x_axis} as label, SUM({$request->y_axis}) as value")
                        ->groupBy($request->x_axis);
                }
                
                $results = $query->orderByDesc('value')
                    ->limit($request->limit ?? 20)
                    ->get();
                
                if ($request->group_by) {
                    $groupedData = $results->groupBy('group_value');
                    
                    $datasets = [];
                    foreach ($groupedData as $group => $items) {
                        $datasets[] = [
                            'label' => $group,
                            'data' => $items->pluck('value')->toArray(),
                            'backgroundColor' => $this->generateChartColors(count($items), 0.7),
                            'borderColor' => $this->generateChartColors(count($items), 1),
                            'borderWidth' => 1
                        ];
                    }
                    
                    return response()->json([
                        'labels' => $results->pluck('label')->unique()->values()->toArray(),
                        'datasets' => $datasets,
                        'chart_type' => $request->chart_type
                    ]);
                } else {
                    $labels = $results->pluck('label')->toArray();
                    $values = $results->pluck('value')->toArray();
                }
                break;
                
            case 'produtos':
                $query = Produto::query();
                
                // Tratamento especial para tempo de impressão
                if ($request->y_axis === 'tempo_impressao') {
                    $query->selectRaw("{$request->x_axis} as label, 
                        CASE 
                            WHEN tempo_impressao LIKE '%h%' 
                            THEN CAST(SUBSTRING_INDEX(tempo_impressao, 'h', 1) AS DECIMAL(10,2)) + 
                                 CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tempo_impressao, 'h', -1), 'm', 1) AS DECIMAL(10,2)) / 60
                            ELSE CAST(tempo_impressao AS DECIMAL(10,2))
                        END as value");
                } else {
                    $query->selectRaw("{$request->x_axis} as label, {$request->y_axis} as value");
                }
                
                $results = $query->orderByDesc('value')
                    ->limit($request->limit ?? 20)
                    ->get();
                
                $labels = $results->pluck('label')->toArray();
                $values = $results->pluck('value')->toArray();
                break;
                
            case 'clientes':
                $query = Cliente::query();
                
                if ($request->start_date && $request->end_date) {
                    $query->whereHas('pedidos', function($q) use ($request) {
                        $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
                    });
                }
                
                if ($request->y_axis === 'total_pedidos') {
                    $query->withCount('pedidos');
                    $results = $query->orderByDesc('pedidos_count')
                        ->limit($request->limit ?? 10)
                        ->get()
                        ->map(function($cliente) {
                            return [
                                'label' => $cliente->nome,
                                'value' => $cliente->pedidos_count
                            ];
                        });
                } else {
                    $query->withSum('pedidos', 'valor_total');
                    $results = $query->orderByDesc('pedidos_sum_valor_total')
                        ->limit($request->limit ?? 10)
                        ->get()
                        ->map(function($cliente) {
                            return [
                                'label' => $cliente->nome,
                                'value' => $cliente->pedidos_sum_valor_total ?? 0
                            ];
                        });
                }
                
                $labels = $results->pluck('label')->toArray();
                $values = $results->pluck('value')->toArray();
                break;
                
            case 'itens_pedido':
                $query = ItemPedido::with('produto');
                
                if ($request->start_date && $request->end_date) {
                    $query->whereHas('pedido', function($q) use ($request) {
                        $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
                    });
                }
                
                if ($request->group_by === 'produto_id') {
                    $query->selectRaw("produto_id, {$request->x_axis} as label, SUM({$request->y_axis}) as value")
                        ->groupBy('produto_id', 'label');
                } else {
                    $query->selectRaw("{$request->x_axis} as label, SUM({$request->y_axis}) as value")
                        ->groupBy($request->x_axis);
                }
                
                $results = $query->orderByDesc('value')
                    ->limit($request->limit ?? 20)
                    ->get();
                
                // Se agrupando por produto, usar o nome do produto
                if ($request->group_by === 'produto_id') {
                    $groupedData = $results->groupBy('produto_id');
                    
                    $datasets = [];
                    foreach ($groupedData as $produtoId => $items) {
                        $produtoNome = $items->first()->produto->nome ?? 'Produto #' . $produtoId;
                        
                        $datasets[] = [
                            'label' => $produtoNome,
                            'data' => $items->pluck('value')->toArray(),
                            'backgroundColor' => $this->generateChartColors(count($items), 0.7),
                            'borderColor' => $this->generateChartColors(count($items), 1),
                            'borderWidth' => 1
                        ];
                    }
                    
                    return response()->json([
                        'labels' => $results->pluck('label')->unique()->values()->toArray(),
                        'datasets' => $datasets,
                        'chart_type' => $request->chart_type
                    ]);
                } else {
                    $labels = $results->pluck('label')->toArray();
                    $values = $results->pluck('value')->toArray();
                }
                break;
        }
        
        return response()->json([
            'labels' => $labels,
            'data' => $values,
            'chart_type' => $request->chart_type
        ]);
    }
    
    public function exportPdf(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'chart_data' => 'required|json'
        ]);
    
        $chartData = json_decode($request->chart_data, true);
        
        // Validação adicional dos dados do gráfico
        if (!isset($chartData['labels'])) {
            abort(422, 'Dados do gráfico inválidos');
        }
    
        $pdf = PDF::loadView('reports.export', [
            'title' => $request->title,
            'chartData' => $chartData,
            'type' => 'pdf'
        ]);
        
        return $pdf->download('relatorio_'.now()->format('YmdHis').'.pdf');
    }
    
    public function exportExcel(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'chart_data' => 'required|json'
        ]);
    
        $chartData = json_decode($request->chart_data, true);
        
        if (!isset($chartData['labels'])) {
            abort(422, 'Dados do gráfico inválidos');
        }
    
        return Excel::download(new ReportExport($chartData), 'relatorio_'.now()->format('YmdHis').'.xlsx');
    }
    
    private function getChartDataForExport(Request $request)
    {
        // Simular os dados que seriam gerados pelo método generate()
        // Na prática, você pode querer reutilizar a lógica do método generate()
        return [
            'labels' => ['Jan', 'Fev', 'Mar'],
            'data' => [100, 200, 300],
            'chart_type' => $request->chart_type,
            'title' => $request->title,
            'data_source' => $request->data_source,
            'x_axis' => $request->x_axis,
            'y_axis' => $request->y_axis
        ];
    }

    private function applyTimeInterval($query, $interval, $dateField)
    {
        switch ($interval) {
            case 'daily':
                $query->selectRaw("DATE({$dateField}) as date_group, {$dateField} as label")
                    ->groupBy('date_group', 'label');
                break;
                
            case 'weekly':
                $query->selectRaw("CONCAT(YEAR({$dateField}), '-', WEEK({$dateField})) as label, YEAR({$dateField}) as year, WEEK({$dateField}) as week")
                    ->groupBy('year', 'week', 'label');
                break;
                
            case 'monthly':
                $query->selectRaw("CONCAT(YEAR({$dateField}), '-', MONTH({$dateField})) as label, YEAR({$dateField}) as year, MONTH({$dateField}) as month")
                    ->groupBy('year', 'month', 'label');
                break;
                
            case 'yearly':
                $query->selectRaw("YEAR({$dateField}) as label")
                    ->groupBy('label');
                break;
        }
        
        return $query;
    }
    
    private function generateChartColors($count, $opacity = 1)
    {
        $colors = [];
        $hueStep = 360 / $count;
        
        for ($i = 0; $i < $count; $i++) {
            $hue = $i * $hueStep;
            $colors[] = "hsla({$hue}, 70%, 60%, {$opacity})";
        }
        
        return $colors;
    }
}