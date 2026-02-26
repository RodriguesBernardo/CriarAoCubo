<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - Exportação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
        }
        .report-date {
            font-size: 12px;
            color: #666;
        }
        .chart-container {
            width: 100%;
            height: 400px;
            margin: 20px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="report-title">{{ $title }}</div>
        <div class="report-date">Gerado em: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
    
    @if($type === 'pdf')
    <div class="chart-container">
        <img src="{{ $chartImage }}" style="width: 100%; height: 100%; object-fit: contain;">
    </div>
    @endif
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($chartData['datasets']))
                @foreach($chartData['datasets'] as $dataset)
                    @foreach($dataset['data'] as $index => $value)
                    <tr>
                        <td>{{ $chartData['labels'][$index] }} ({{ $dataset['label'] }})</td>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                @endforeach
            @else
                @foreach($chartData['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $chartData['data'][$index] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    
    <div class="footer">
        Relatório gerado pelo sistema em {{ now()->format('d/m/Y') }}
    </div>
</body>
</html>