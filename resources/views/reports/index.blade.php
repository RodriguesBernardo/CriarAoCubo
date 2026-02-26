@section('title', 'Painel Análitico')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gerador de Gráficos Personalizados</h1>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Configurar Relatório</h6>
                </div>
                <div class="card-body">
                    <form id="customReportForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_source">Fonte de Dados</label>
                                    <select class="form-control" id="data_source" name="data_source" required>
                                        <option value="">Selecione...</option>
                                        <option value="pedidos">Pedidos</option>
                                        <option value="produtos">Produtos</option>
                                        <option value="itens_pedido">Itens de Pedido</option>
                                        <option value="clientes">Clientes</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="x_axis">Eixo X (Categorias)</label>
                                    <select class="form-control" id="x_axis" name="x_axis" required disabled>
                                        <option value="">Selecione a fonte de dados primeiro</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="y_axis">Eixo Y (Valores)</label>
                                    <select class="form-control" id="y_axis" name="y_axis" required disabled>
                                        <option value="">Selecione a fonte de dados primeiro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="chart_type">Tipo de Gráfico</label>
                                    <select class="form-control" id="chart_type" name="chart_type" required>
                                        <option value="bar">Barras</option>
                                        <option value="line">Linhas</option>
                                        <option value="pie">Pizza</option>
                                        <option value="doughnut">Rosca</option>
                                        <option value="radar">Radar</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="time_interval">Agrupar por Tempo</label>
                                    <select class="form-control" id="time_interval" name="time_interval">
                                        <option value="">Não agrupar</option>
                                        <option value="daily">Diário</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="monthly">Mensal</option>
                                        <option value="yearly">Anual</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="group_by">Agrupar por</label>
                                    <select class="form-control" id="group_by" name="group_by" disabled>
                                        <option value="">Não agrupar</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="limit">Limite de Resultados</label>
                                    <input type="number" class="form-control" id="limit" name="limit" value="" min="1" max="100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Data Inicial</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Data Final</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="report_title">Título do Relatório</label>
                                    <input type="text" class="form-control" id="report_title" name="report_title" placeholder="Digite um título para o relatório" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-chart-bar"></i> Gerar Relatório
                                </button>
                                <button type="button" id="exportPdf" class="btn btn-danger ml-2" disabled>
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                                <button type="button" id="exportExcel" class="btn btn-success ml-2" disabled>
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resultados do Relatório Personalizado -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary" id="customChartTitle">Seu Relatório Personalizado Aparecerá Aqui</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px; width:100%">
                        <canvas id="customReportChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Relatório Analítico -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Análise do Relatório</h6>
                </div>
                <div class="card-body">
                    <div id="reportAnalysis">
                        <p class="text-center">Nenhum relatório gerado ainda. Configure e gere um relatório para visualizar a análise.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabela de Dados do Relatório Personalizado -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dados do Relatório</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="customDataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Categoria (Eixo X)</th>
                                    <th>Valor (Eixo Y)</th>
                                </tr>
                            </thead>
                            <tbody id="customReportDataBody">
                                <tr>
                                    <td colspan="2" class="text-center">Nenhum dado disponível. Gere um relatório para visualizar os dados.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }
    
    .analysis-metric {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
    
    .analysis-metric h5 {
        font-size: 1rem;
        font-weight: 600;
        color: #4e73df;
    }
    
    .analysis-metric p {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Moment.js para manipulação de datas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/pt-br.min.js"></script>

<script>
    // Definir os campos disponíveis para cada fonte de dados
    const dataFields = {
        'pedidos': {
            'valor_total': 'Valor Total (R$)',
            'dias_entrega': 'Dias para Entrega',
            'status': 'Status do Pedido',
            'data_pedido': 'Data do Pedido',
            'data_entrega_prevista': 'Data de Entrega Prevista',
            'created_at': 'Data de Criação'
        },
        'produtos': {
            'nome': 'Nome do Produto',
            'preco': 'Preço de Venda (R$)',
            'preco_custo': 'Preço de Custo (R$)',
            'custo_estimado_energia': 'Custo de Energia (R$)',
            'custo_estimado_por_hora': 'Custo por Hora (R$)',
            'custo_estimado_por_grama': 'Custo por Grama (R$)',
            'quantidade': 'Quantidade em Estoque',
            'tempo_impressao': 'Tempo de Impressão (horas)',
            'dias_entrega': 'Prazo de Entrega (dias)'
        },
        'clientes': {
            'nome': 'Nome do Cliente',
            'email': 'E-mail',
            'telefone': 'Telefone',
            'total_pedidos': 'Total de Pedidos',
            'total_gasto': 'Total Gasto (R$)'
        },
        'itens_pedido': {
            'quantidade': 'Quantidade',
            'preco_unitario': 'Preço Unitário (R$)',
            'tempo_estimado': 'Tempo Estimado (horas)',
            'peso_estimado': 'Peso Estimado (gramas)',
            'custo_estimado': 'Custo Estimado (R$)'
        }
    };

    // Variáveis globais
    let customReportChart = null;
    let currentReportData = null;
    
    // Função para formatar valores
    function formatValue(value, isCurrency = false) {
        if (value === null || value === undefined) return 'N/A';
        
        if (isCurrency) {
            return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        return parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    
    // Função para gerar cores para os gráficos
    function generateColors(count, opacity = 1) {
        const colors = [];
        const hueStep = 360 / count;
        
        for (let i = 0; i < count; i++) {
            const hue = i * hueStep;
            colors.push(`hsla(${hue}, 70%, 60%, ${opacity})`);
        }
        
        return colors;
    }
    
    // Carregar campos disponíveis quando a fonte de dados é selecionada
    $('#data_source').change(function() {
        const source = $(this).val();
        
        if (source) {
            // Habilitar selects dependentes
            $('#x_axis, #y_axis, #group_by').prop('disabled', false);
            
            // Obter campos da fonte selecionada
            const fields = dataFields[source];
            
            // Preencher eixos X e Y
            fillSelectOptions($('#x_axis'), fields);
            fillSelectOptions($('#y_axis'), fields);
            
            // Preencher opções de agrupamento
            const groupOptions = {
                '': 'Não agrupar',
                ...fields
            };
            fillSelectOptions($('#group_by'), groupOptions);
        } else {
            $('#x_axis, #y_axis, #group_by').prop('disabled', true);
        }
    });
    
    // Função para preencher selects com opções
    function fillSelectOptions(selectElement, options) {
        selectElement.empty();
        
        for (const [value, text] of Object.entries(options)) {
            selectElement.append(new Option(text, value));
        }
    }
    
    // Gerar relatório personalizado
    $('#customReportForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        
        // Mostrar loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        
        $.ajax({
            url: "{{ route('reports.generate') }}",
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                currentReportData = response;
                const title = $('#report_title').val();
                $('#customChartTitle').text(title);
                renderCustomChart(response);
                fillCustomDataTable(response);
                generateReportAnalysis(response);
                
                // Habilitar botões de exportação
                $('#exportPdf, #exportExcel').prop('disabled', false);
            },
            error: function(xhr) {
                let errorMessage = 'Ocorreu um erro ao gerar o relatório.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Erro ao gerar relatório:', xhr.responseText);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-chart-bar"></i> Gerar Relatório');
            }
        });
    });
    
    // Renderizar gráfico personalizado
    function renderCustomChart(data) {
        const ctx = document.getElementById('customReportChart').getContext('2d');
        
        if (customReportChart) {
            customReportChart.destroy();
        }
        
        // Verificar se é um valor monetário
        const yAxisLabel = $('#y_axis option:selected').text();
        const isCurrency = yAxisLabel.includes('R$') || yAxisLabel.includes('Valor') || 
                         yAxisLabel.includes('Preço') || yAxisLabel.includes('Custo');
        
        // Configurações do gráfico
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            
                            const value = context.parsed?.y ?? context.raw;
                            label += isCurrency ? formatValue(value, true) : formatValue(value);
                            
                            return label;
                        }
                    }
                }
            },
            scales: data.chart_type === 'pie' || data.chart_type === 'doughnut' ? {} : {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: yAxisLabel
                    },
                    ticks: {
                        callback: function(value) {
                            return isCurrency ? formatValue(value, true) : formatValue(value);
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: $('#x_axis option:selected').text()
                    }
                }
            }
        };
        
        // Configurações específicas para gráficos de radar
        if (data.chart_type === 'radar') {
            chartOptions.scales = {
                r: {
                    angleLines: { display: true },
                    suggestedMin: 0,
                    ticks: {
                        callback: function(value) {
                            return isCurrency ? formatValue(value, true) : formatValue(value);
                        }
                    }
                }
            };
        }
        
        // Criar o gráfico
        customReportChart = new Chart(ctx, {
            type: data.chart_type,
            data: {
                labels: data.labels,
                datasets: data.datasets || [{
                    label: yAxisLabel,
                    data: data.data,
                    backgroundColor: generateColors(data.labels.length, 0.7),
                    borderColor: generateColors(data.labels.length, 1),
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });
    }
    
    // Preencher tabela com os dados do relatório personalizado
    function fillCustomDataTable(data) {
        const tbody = $('#customReportDataBody');
        tbody.empty();
        
        if (data.datasets) {
            // Gráfico com múltiplos datasets (agrupado)
            data.datasets.forEach(dataset => {
                dataset.data.forEach((value, index) => {
                    const yAxisText = dataset.label.toLowerCase();
                    const isCurrency = yAxisText.includes('r$') || yAxisText.includes('valor') || 
                                     yAxisText.includes('preço') || yAxisText.includes('custo');
                    
                    tbody.append(`
                        <tr>
                            <td>${data.labels[index]} (${dataset.label})</td>
                            <td>${formatValue(value, isCurrency)}</td>
                        </tr>
                    `);
                });
            });
        } else {
            // Gráfico simples
            data.labels.forEach((label, index) => {
                const yAxisText = $('#y_axis option:selected').text().toLowerCase();
                const isCurrency = yAxisText.includes('r$') || yAxisText.includes('valor') || 
                                 yAxisText.includes('preço') || yAxisText.includes('custo');
                
                tbody.append(`
                    <tr>
                        <td>${label}</td>
                        <td>${formatValue(data.data[index], isCurrency)}</td>
                    </tr>
                `);
            });
        }
        
        if (tbody.children().length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="2" class="text-center">Nenhum dado disponível para exibição.</td>
                </tr>
            `);
        }
    }
    
    // Gerar análise do relatório

    function generateReportAnalysis(data) {
        const analysisDiv = $('#reportAnalysis');
        analysisDiv.empty();
        
        const title = $('#report_title').val();
        const xAxisLabel = $('#x_axis option:selected').text();
        const yAxisLabel = $('#y_axis option:selected').text();
        const isCurrency = yAxisLabel.includes('R$') || yAxisLabel.includes('Valor') || 
                        yAxisLabel.includes('Preço') || yAxisLabel.includes('Custo');
        
        // Calcular métricas básicas
        let values = [];
        if (data.datasets) {
            // Para gráficos com múltiplos datasets
            data.datasets.forEach(dataset => {
                values = values.concat(dataset.data);
            });
        } else {
            // Para gráficos simples
            values = data.data || [];
        }
        
        // Converter para números e filtrar valores válidos
        const validValues = values
            .map(v => {
                if (v === null || v === undefined) return NaN;
                // Se for string de moeda, converter para número
                if (typeof v === 'string' && v.includes('R$')) {
                    return parseFloat(v.replace(/[^\d,]/g, '').replace(',', '.')) || NaN;
                }
                return parseFloat(v) || NaN;
            })
            .filter(v => !isNaN(v));
        
        // Calcular métricas
        let sum = 0;
        let avg = 'N/A';
        let max = 'N/A';
        let min = 'N/A';
        
        if (validValues.length > 0) {
            sum = validValues.reduce((a, b) => a + b, 0);
            avg = sum / validValues.length;
            max = Math.max(...validValues);
            min = Math.min(...validValues);
        }
        
        // Adicionar logs para depuração
        console.log('Valores originais:', values);
        console.log('Valores válidos convertidos:', validValues);
        console.log('Soma calculada:', sum);
        console.log('Média calculada:', avg);
        
        // Criar HTML da análise
        analysisDiv.append(`
            <div class="row">
                <div class="col-md-12">
                    <h4>${title}</h4>
                    <p class="mb-4">Análise dos dados: ${xAxisLabel} vs ${yAxisLabel}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="analysis-metric">
                        <h5>Total</h5>
                        <p>${formatValue(sum, isCurrency)}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="analysis-metric">
                        <h5>Média</h5>
                        <p>${formatValue(avg, isCurrency)}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="analysis-metric">
                        <h5>Máximo</h5>
                        <p>${formatValue(max, isCurrency)}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="analysis-metric">
                        <h5>Mínimo</h5>
                        <p>${formatValue(min, isCurrency)}</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <h5>Insights:</h5>
                    <ul>
                        <li>O valor ${isCurrency ? 'monetário ' : ''}total analisado é de ${formatValue(sum, isCurrency)}</li>
                        <li>A média por ${xAxisLabel.toLowerCase()} é de ${formatValue(avg, isCurrency)}</li>
                        <li>O maior valor encontrado foi ${formatValue(max, isCurrency)}</li>
                        <li>O menor valor encontrado foi ${formatValue(min, isCurrency)}</li>
                    </ul>
                </div>
            </div>
        `);
    }
// Exportar para PDF
$('#exportPdf').click(function() {
    if (!currentReportData) {
        alert('Gere um relatório antes de exportar');
        return;
    }
    
    // Criar um formulário dinâmico com todos os dados necessários
    const form = $('<form>', {
        method: 'POST',
        action: "{{ route('reports.export.pdf') }}",
        style: 'display: none;'
    });
    
    // Adicionar token CSRF
    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: $('meta[name="csrf-token"]').attr('content')
    }));
    
    // Adicionar dados do relatório atual
    form.append($('<input>', {
        type: 'hidden',
        name: 'chart_data',
        value: JSON.stringify(currentReportData)
    }));
    
    // Adicionar título
    form.append($('<input>', {
        type: 'hidden',
        name: 'title',
        value: $('#report_title').val()
    }));
    
    // Adicionar configurações do formulário original
    $('#customReportForm').find('input, select').each(function() {
        if (this.name && !form.find(`[name="${this.name}"]`).length) {
            form.append($(this).clone());
        }
    });
    
    // Adicionar ao DOM e submeter
    $('body').append(form);
    form.submit();
    form.remove();
});

    // Exportar para Excel (mesma abordagem)
    $('#exportExcel').click(function() {
        if (!currentReportData) {
            alert('Gere um relatório antes de exportar');
            return;
        }
        
        const form = $('<form>', {
            method: 'POST',
            action: "{{ route('reports.export.excel') }}",
            style: 'display: none;'
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'chart_data',
            value: JSON.stringify(currentReportData)
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'title',
            value: $('#report_title').val()
        }));
        
        $('#customReportForm').find('input, select').each(function() {
            if (this.name && !form.find(`[name="${this.name}"]`).length) {
                form.append($(this).clone());
            }
        });
        
        $('body').append(form);
        form.submit();
        form.remove();
    });

    // Exportar para Excel
    $('#exportExcel').click(function() {
        if (!currentReportData) return;
        
        const form = $('#customReportForm');
        const formData = new FormData(form[0]);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('title', $('#report_title').val());
        
        $.ajax({
            url: "{{ route('reports.export.excel') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data) {
                const blob = new Blob([data], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'relatorio_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao exportar Excel:', error);
                alert('Ocorreu um erro ao gerar o Excel. Verifique o console para mais detalhes.');
            }
        });
    });
    
    // Inicializar selects quando a página é carregada
    $(document).ready(function() {
        $('#data_source').trigger('change');
    });
</script>
@endpush