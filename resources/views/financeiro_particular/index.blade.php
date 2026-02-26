@extends('layouts.app')

@section('title', 'Gestão Financeira')

@section('content')
<div class="container-fluid p-4">

    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="fw-bold mb-1"><i class="fas fa-chart-pie me-2 text-primary"></i>Painel Financeiro</h3>
                    <span class="text-secondary">Visão geral: Bernardo & Gabriele</span>
                </div>

                <form action="{{ route('financeiro_particular.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
                    
                    <select name="responsavel_filtro" class="form-select border-0 bg-secondary bg-opacity-10 text-secondary fw-bold rounded-pill" onchange="this.form.submit()" style="min-width: 140px;">
                        <option value="">Todos</option>
                        <option value="Bernardo" {{ $filtroResponsavel == 'Bernardo' ? 'selected' : '' }}>Bernardo</option>
                        <option value="Gabriele" {{ $filtroResponsavel == 'Gabriele' ? 'selected' : '' }}>Gabriele</option>
                    </select>

                    <select name="categoria_filtro" class="form-select border-0 bg-secondary bg-opacity-10 text-secondary fw-bold rounded-pill" onchange="this.form.submit()" style="min-width: 160px;">
                        <option value="">Todas Categorias</option>
                        @foreach($todasCategorias as $cat)
                        <option value="{{ $cat }}" {{ $filtroCategoria == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <div class="vr mx-2 text-secondary opacity-25 d-none d-md-block"></div>

                    <div class="d-flex gap-2">
                        <select name="mes" class="form-select border-0 bg-secondary bg-opacity-10 text-secondary fw-bold rounded-pill" onchange="this.form.submit()">
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                                {{ ucfirst(\Carbon\Carbon::create()->month($i)->locale('pt_BR')->monthName) }}
                                </option>
                            @endfor
                        </select>
                        <select name="ano" class="form-select border-0 bg-secondary bg-opacity-10 text-secondary fw-bold rounded-pill" onchange="this.form.submit()">
                            <option value="2025" {{ $ano == 2025 ? 'selected' : '' }}>2025</option>
                            <option value="2026" {{ $ano == 2026 ? 'selected' : '' }}>2026</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative text-white"
                style="background: linear-gradient(135deg, #059669 0%, #34d399 100%);">
                <div class="position-absolute top-0 end-0 p-3 opacity-25"><i class="fas fa-arrow-up fa-3x"></i></div>
                <div class="card-body p-4 position-relative z-1">
                    <p class="mb-1 text-uppercase fw-bold opacity-75 small">Entradas Totais</p>
                    <h2 class="fw-bold mb-2">R$ {{ number_format($totalReceitas, 2, ',', '.') }}</h2>

                    <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                        <div class="d-flex justify-content-between small opacity-90 mb-1">
                            <span><i class="fas fa-user me-1"></i> Particular:</span>
                            <span class="fw-bold">R$ {{ number_format($receitaParticular, 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between small opacity-90">
                            <span><i class="fas fa-cube me-1"></i> Lucro Criar³:</span>
                            <span class="fw-bold">R$ {{ number_format($lucroCriar3, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative text-white"
                style="background: linear-gradient(135deg, #dc2626 0%, #f87171 100%);">
                <div class="position-absolute top-0 end-0 p-3 opacity-25"><i class="fas fa-arrow-down fa-3x"></i></div>
                <div class="card-body p-4 position-relative z-1">
                    <p class="mb-1 text-uppercase fw-bold opacity-75 small">Saídas ({{ $mes }}/{{ $ano }})</p>
                    <h2 class="fw-bold mb-0">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative text-white"
                style="background: linear-gradient(135deg, #2563eb 0%, #60a5fa 100%);">
                <div class="position-absolute top-0 end-0 p-3 opacity-25"><i class="fas fa-wallet fa-3x"></i></div>
                <div class="card-body p-4 position-relative z-1">
                    <p class="mb-1 text-uppercase fw-bold opacity-75 small">Saldo Líquido</p>
                    <h2 class="fw-bold mb-0">R$ {{ number_format($saldo, 2, ',', '.') }}</h2>

                    @if($saldo > 0)
                    <small class="d-block mt-2 opacity-75"><i class="fas fa-check-circle me-1"></i> No azul!</small>
                    @else
                    <small class="d-block mt-2 opacity-75"><i class="fas fa-exclamation-circle me-1"></i> Atenção aos gastos</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-secondary">Fluxo de Caixa Anual</h5>
                        <small class="text-muted">Comparativo mês a mês em {{ $ano }}</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="graficoAnual" style="max-height: 300px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-secondary">Top Despesas</h5>
                    <small class="text-muted">Onde o dinheiro foi gasto este mês</small>
                </div>
                <div class="card-body p-4 d-flex justify-content-center align-items-center position-relative">
                    <canvas id="graficoCategoria" style="max-height: 250px;"></canvas>
                    @if($gastosPorCategoria->isEmpty())
                    <div class="position-absolute text-center text-muted">
                        <small>Sem dados</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header border-0 pt-4 px-4 pb-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-secondary">Extrato Detalhado</h5>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="abrirModalCriacao()">
                <i class="fas fa-plus me-2"></i> Novo Lançamento
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3 border-0">Data</th>
                            <th class="py-3 border-0">Descrição</th>
                            <th class="py-3 border-0">Categoria</th>
                            <th class="py-3 border-0 text-center">Resp.</th>
                            <th class="py-3 border-0 text-end">Valor</th>
                            <th class="py-3 border-0 text-center">Status</th>
                            <th class="py-3 border-0 text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($movimentacoes as $mov)
                        <tr style="cursor: pointer;" onclick="editarLancamento({{ json_encode($mov) }})">
                            <td class="ps-4 fw-medium text-secondary" style="width: 100px;">
                                {{ $mov->data_vencimento->format('d/m') }}
                            </td>
                            <td>
                                <div class="fw-bold ">{{ $mov->descricao }}</div>
                                <div class="d-flex gap-1 mt-1">
                                    @if($mov->is_parcelado)
                                    <span class="badge rounded-pill bg-light text-secondary border" style="font-size: 0.65rem;">
                                        {{ $mov->parcela_atual }}/{{ $mov->total_parcelas }}
                                    </span>
                                    @endif
                                    @if($mov->is_fixo)
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info" style="font-size: 0.65rem;">Fixo</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-light text-secondary border fw-normal px-3">
                                    {{ $mov->categoria }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($mov->responsavel == 'Bernardo')
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary">B</span>
                                @elseif($mov->responsavel == 'Gabriele')
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger">G</span>
                                @else
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary">B&G</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $mov->tipo == 'receita' ? 'text-success' : 'text-danger' }}">
                                {{ $mov->tipo == 'receita' ? '+' : '-' }} R$ {{ number_format($mov->valor, 2, ',', '.') }}
                            </td>
                            <td class="text-center" style="width: 120px;" onclick="event.stopPropagation()">
                                <div class="form-check form-switch d-flex justify-content-center align-items-center flex-column">
                                    <input class="form-check-input check-pago shadow-none" type="checkbox" role="switch"
                                        style="cursor: pointer; width: 2.5em; height: 1.25em;"
                                        data-id="{{ $mov->id }}" {{ $mov->pago ? 'checked' : '' }}>
                                    <small class="mt-1 fw-bold {{ $mov->pago ? 'text-success' : 'text-muted' }}" style="font-size: 0.65rem;">
                                        {{ $mov->pago ? 'PAGO' : 'PENDENTE' }}
                                    </small>
                                </div>
                            </td>
                            <td class="text-end pe-4" onclick="event.stopPropagation()">
                                <form action="{{ route('financeiro_particular.destroy', $mov->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-icon btn-sm text-muted hover-danger border-0 bg-transparent">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-2 opacity-25"><i class="fas fa-file-invoice-dollar fa-3x"></i></div>
                                Nenhuma movimentação encontrada para este filtro.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLancamento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form id="formLancamento" method="POST">
                @csrf
                <div id="methodPut"></div>

                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-secondary" id="modalTitulo">Novo Lançamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-3">

                    <div class="d-flex justify-content-center mb-4">
                        <div class="btn-group w-100 shadow-sm" role="group">
                            <input type="radio" class="btn-check" name="tipo" id="tipoDespesa" value="despesa" checked>
                            <label class="btn btn-outline-danger py-2 fw-medium" for="tipoDespesa">
                                <i class="fas fa-arrow-down me-2"></i>Despesa
                            </label>

                            <input type="radio" class="btn-check" name="tipo" id="tipoReceita" value="receita">
                            <label class="btn btn-outline-success py-2 fw-medium" for="tipoReceita">
                                <i class="fas fa-arrow-up me-2"></i>Receita
                            </label>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control rounded-3 border-secondary border-opacity-25" id="descricao" name="descricao" placeholder="Ex: Padaria" required>
                        <label>Descrição</label>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control rounded-3 border-secondary border-opacity-25" id="valor" name="valor" placeholder="0,00" required>
                                <label>Valor (R$)</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="date" class="form-control rounded-3 border-secondary border-opacity-25" id="data_vencimento" name="data_vencimento" value="{{ date('Y-m-d') }}" required>
                                <label>Data</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <select class="form-select rounded-3 border-secondary border-opacity-25" id="categoria" name="categoria" required>
                                    <option value="Casa">🏠 Casa</option>
                                    <option value="Alimentação">🍔 Alimentação</option>
                                    <option value="Transporte">🚗 Transporte</option>
                                    <option value="Lazer">🎉 Lazer</option>
                                    <option value="Saúde">💊 Saúde</option>
                                    <option value="Esporte">🏃‍♂️ Esporte</option>
                                    <option value="Assinaturas">📱 Assinaturas</option>
                                    <option value="Estudos">📚 Estudos</option>
                                    <option value="Empreendimento">💼 Empreendimento</option>
                                    <option value="Roupas">🛍️ Roupas</option>
                                    <option value="Internet">🛒 Compras Internet</option>
                                    <option value="Salário">💰 Salário</option>
                                    <option value="Fatura">💰 Fatura</option>
                                    <option value="Outros">💡 Outros</option>
                                </select>
                                <label>Categoria</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <select class="form-select rounded-3 border-secondary border-opacity-25" id="responsavel" name="responsavel" required>
                                    <option value="Bernardo">Bernardo</option>
                                    <option value="Gabriele">Gabriele</option>
                                    <option value="Ambos">Ambos</option>
                                </select>
                                <label>Responsável</label>
                            </div>
                        </div>
                    </div>

                    <div id="areaRepeticao" class="bg-secondary bg-opacity-10 p-3 rounded-3 mt-4">
                        <label class="small text-secondary fw-bold mb-3 d-block text-uppercase" style="letter-spacing: 0.5px;">Frequência</label>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="repeticao" id="repUnica" value="unica" checked onclick="toggleParcelas(false)">
                                <label class="form-check-label small" for="repUnica">Única</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="repeticao" id="repFixa" value="fixa" onclick="toggleParcelas(false)">
                                <label class="form-check-label small" for="repFixa">Fixo Mensal</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="repeticao" id="repParcelada" value="parcelada" onclick="toggleParcelas(true)">
                                <label class="form-check-label small" for="repParcelada">Parcelado</label>
                            </div>
                        </div>

                        <div class="mt-3 d-none animate__animated animate__fadeIn" id="divParcelas">
                            <label class="small text-secondary mb-1">Número de parcelas</label>
                            <input type="number" class="form-control rounded-3 border-secondary border-opacity-25 bg-white" name="qtd_parcelas" placeholder="Ex: 12">
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4 mt-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none me-auto" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuração do Modal
    const modalLancamento = new bootstrap.Modal(document.getElementById('modalLancamento'));

    // --- GRÁFICO 1: Distribuição por Categoria (Rosca) ---
    const ctxCat = document.getElementById('graficoCategoria');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($gastosPorCategoria->keys()) !!},
            datasets: [{
                data: {!! json_encode($gastosPorCategoria->values()) !!},
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b', '#ec4899', '#14b8a6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { usePointStyle: true, boxWidth: 8, padding: 15 }
                }
            },
            cutout: '75%'
        }
    });

    // --- GRÁFICO 2: Evolução Anual (Barras) ---
    const ctxAnual = document.getElementById('graficoAnual');
    new Chart(ctxAnual, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                    label: 'Receitas',
                    data: {!! json_encode(array_values($receitasAno)) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                    barPercentage: 0.6,
                },
                {
                    label: 'Despesas',
                    data: {!! json_encode(array_values($despesasAno)) !!},
                    backgroundColor: '#ef4444',
                    borderRadius: 4,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });

    // --- FUNÇÕES DE INTERFACE ---

    function abrirModalCriacao() {
        document.getElementById('formLancamento').reset();
        document.getElementById('formLancamento').action = "{{ route('financeiro_particular.store') }}";
        document.getElementById('methodPut').innerHTML = ''; // Limpa PUT
        
        document.getElementById('modalTitulo').innerText = 'Novo Lançamento';
        document.getElementById('areaRepeticao').classList.remove('d-none'); // Mostra repeticao
        
        // Data de hoje padrão
        document.getElementById('data_vencimento').value = new Date().toISOString().split('T')[0];
        
        modalLancamento.show();
    }

    function editarLancamento(mov) {
        document.getElementById('formLancamento').action = `/financeiro_particular/${mov.id}`;
        document.getElementById('methodPut').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('modalTitulo').innerText = 'Editar Lançamento';
        document.getElementById('areaRepeticao').classList.add('d-none'); // Esconde repetição na edição

        // Preenche campos
        document.getElementById('descricao').value = mov.descricao;
        document.getElementById('valor').value = mov.valor;
        document.getElementById('data_vencimento').value = mov.data_vencimento.split('T')[0];
        document.getElementById('categoria').value = mov.categoria;
        document.getElementById('responsavel').value = mov.responsavel;

        if (mov.tipo === 'despesa') document.getElementById('tipoDespesa').checked = true;
        else document.getElementById('tipoReceita').checked = true;

        modalLancamento.show();
    }

    function toggleParcelas(show) {
        const div = document.getElementById('divParcelas');
        if (show) {
            div.classList.remove('d-none');
            div.querySelector('input').focus();
        } else {
            div.classList.add('d-none');
        }
    }

    // --- Checkbox Pagar (AJAX) ---
    document.querySelectorAll('.check-pago').forEach(check => {
        check.addEventListener('change', function() {
            const id = this.dataset.id;
            const label = this.parentElement.nextElementSibling;

            if (this.checked) {
                label.innerText = 'PAGO';
                label.classList.remove('text-muted');
                label.classList.add('text-success');
            } else {
                label.innerText = 'PENDENTE';
                label.classList.add('text-muted');
                label.classList.remove('text-success');
            }

            fetch(`/financeiro_particular/${id}/pagar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).catch(err => {
                console.error('Erro ao salvar pagamento', err);
                this.checked = !this.checked;
            });
        });
    });
</script>
@endpush