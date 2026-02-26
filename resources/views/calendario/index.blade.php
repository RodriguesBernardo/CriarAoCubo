@extends('layouts.app')

@section('title', 'Agenda Pessoal')

@section('content')
<div class="container-fluid p-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-0 "><i class="far fa-calendar-alt me-2"></i>Minha Agenda</h4>
            <small class="text-muted">Gerencie seus compromissos pessoais</small>
        </div>

        <div class="d-flex p-1 rounded-pill shadow-sm border">
            <div class="form-check form-check-inline m-0">
                <input class="btn-check filter-check" type="checkbox" id="filterBernardo" value="Bernardo" checked>
                <label class="btn btn-sm btn-outline-primary rounded-pill border-0 fw-semibold px-3" for="filterBernardo">Bernardo</label>
            </div>
            <div class="form-check form-check-inline m-0">
                <input class="btn-check filter-check" type="checkbox" id="filterGabriele" value="Gabriele" checked>
                <label class="btn btn-sm btn-outline-danger rounded-pill border-0 fw-semibold px-3" for="filterGabriele">Gabriele</label>
            </div>
        </div>

        <button class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm" onclick="abrirModalCriacao()">
            <i class="fas fa-plus me-2"></i> Novo Evento
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEvento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formEvento">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitulo">Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <input type="hidden" id="eventoId">

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control rounded-3  " id="titulo" name="titulo" placeholder="Título" required>
                        <label for="titulo">Título do compromisso</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold text-uppercase">Quem participa?</label>
                        <div class="d-flex gap-2">
                            <input type="checkbox" class="btn-check" id="checkBernardo" value="Bernardo">
                            <label class="btn btn-outline-primary flex-fill rounded-3" for="checkBernardo">
                                <i class="fas fa-user me-1"></i> Bernardo
                            </label>

                            <input type="checkbox" class="btn-check" id="checkGabriele" value="Gabriele">
                            <label class="btn btn-outline-danger flex-fill rounded-3" for="checkGabriele">
                                <i class="fas fa-heart me-1"></i> Gabriele
                            </label>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="datetime-local" class="form-control rounded-3  " id="inicio" required>
                                <label>Início</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="datetime-local" class="form-control rounded-3  " id="fim" required>
                                <label>Fim</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold text-uppercase">Cor de destaque</label>
                        <div class="d-flex justify-content-between px-2">
                            <div class="opcao-cor selected" data-cor="#3788d8" style="background:#3788d8"></div>
                            <div class="opcao-cor" data-cor="#10b981" style="background:#10b981"></div>
                            <div class="opcao-cor" data-cor="#f59e0b" style="background:#f59e0b"></div>
                            <div class="opcao-cor" data-cor="#ef4444" style="background:#ef4444"></div>
                            <div class="opcao-cor" data-cor="#8b5cf6" style="background:#8b5cf6"></div>
                            <div class="opcao-cor" data-cor="#64748b" style="background:#64748b"></div>
                        </div>
                        <input type="hidden" id="cor" value="#3788d8">
                    </div>

                    <div class="form-floating mb-3">
                        <textarea class="form-control rounded-3  " id="descricao" style="height: 100px" placeholder="Detalhes"></textarea>
                        <label>Observações</label>
                    </div>

                    <div id="areaRecorrencia" class=" p-3 rounded-3 mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="checkRecorrencia">
                            <label class="form-check-label" for="checkRecorrencia">Repetir evento</label>
                        </div>
                        <div id="opcoesRecorrencia" class="mt-2 d-none">
                            <select class="form-select mb-2" id="tipoRecorrencia">
                                <option value="semanal">Toda semana</option>
                                <option value="mensal">Todo mês</option>
                            </select>
                            <input type="date" class="form-control" id="fimRecorrencia">
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-link text-danger text-decoration-none me-auto d-none" id="btnExcluir" onclick="deletarEvento()">Excluir</button>
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* Design Clean para o Calendário */
    #calendar {
        font-family: 'Inter', sans-serif;
        padding: 20px;
    }

    /* Remove sublinhado dos números e links */
    .fc-col-header-cell-cushion,
    .fc-daygrid-day-number,
    .fc a {
        text-decoration: none !important;
        color: inherit;
    }

    /* Cabeçalho da tabela */
    .fc-col-header-cell {
        padding: 10px 0;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        border-bottom: none !important;
    }

    /* Botões do FullCalendar */
    .fc-button {
        /* color: #475569 !important; */
        text-transform: capitalize;
        font-weight: 500;
        box-shadow: none !important;
    }

    .fc-button-active {
        color: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }

    /* Eventos */
    .fc-event {
        border: none;
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.85rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Seletor de Cores Redondo */
    .opcao-cor {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: transform 0.2s;
    }

    .opcao-cor:hover {
        transform: scale(1.1);
    }

    .opcao-cor.selected {
        box-shadow: 0 0 0 2px #212529;
        transform: scale(1.1);
    }

    /* Estilo dos inputs */
    .form-control:focus {
        box-shadow: none;
        border-color: #818CF8;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let calendar;
    const modal = new bootstrap.Modal(document.getElementById('modalEvento'));

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            dayMaxEvents: true, // Mostra "+2 mais" se tiver muitos eventos

            // ATUALIZADO: Passa os filtros na URL
            events: function(info, successCallback, failureCallback) {
                // Pega quem está marcado
                let pessoas = [];
                if (document.getElementById('filterBernardo').checked) pessoas.push('Bernardo');
                if (document.getElementById('filterGabriele').checked) pessoas.push('Gabriele');

                // Constrói a URL com parametros
                let url = '/api/calendario-events?start=' + info.startStr + '&end=' + info.endStr;
                if (pessoas.length > 0) {
                    url += '&pessoas=' + pessoas.join(',');
                }

                fetch(url)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },

            editable: true,
            selectable: true,

            select: function(info) {
                limparFormulario();
                // Ajuste de data e hora
                let inicio = info.startStr.includes('T') ? info.startStr.substring(0, 16) : info.startStr + 'T09:00';
                let fim = info.endStr.includes('T') ? info.endStr.substring(0, 16) : info.startStr + 'T10:00';

                document.getElementById('inicio').value = inicio;
                document.getElementById('fim').value = fim;

                // Padrão de recorrência
                let dataFimRec = new Date();
                dataFimRec.setMonth(dataFimRec.getMonth() + 1);
                document.getElementById('fimRecorrencia').value = dataFimRec.toISOString().split('T')[0];

                document.getElementById('modalTitulo').innerText = 'Novo Evento';
                document.getElementById('btnExcluir').classList.add('d-none');
                document.getElementById('areaRecorrencia').classList.remove('d-none');
                modal.show();
            },

            eventClick: function(info) {
                limparFormulario();
                const evento = info.event;

                document.getElementById('eventoId').value = evento.id;
                // Remove prefixo [B, G] do título para edição
                document.getElementById('titulo').value = evento.title.replace(/^\[.*?\]\s/, '');
                document.getElementById('descricao').value = evento.extendedProps.descricao || '';

                // ATUALIZADO: Preenche os checkboxes de participantes
                const parts = evento.extendedProps.participantes || [];
                document.getElementById('checkBernardo').checked = parts.includes('Bernardo');
                document.getElementById('checkGabriele').checked = parts.includes('Gabriele');

                document.getElementById('inicio').value = formatarDataISO(evento.start);
                document.getElementById('fim').value = evento.end ? formatarDataISO(evento.end) : formatarDataISO(evento.start);

                selecionarCor(evento.backgroundColor);

                document.getElementById('modalTitulo').innerText = 'Editar Evento';
                document.getElementById('btnExcluir').classList.remove('d-none');
                document.getElementById('areaRecorrencia').classList.add('d-none');
                modal.show();
            },

            eventDrop: function(info) {
                atualizarDatas(info.event);
            },
            eventResize: function(info) {
                atualizarDatas(info.event);
            }
        });
        calendar.render();

        // Listeners para filtros (Recarrega o calendario ao clicar)
        document.querySelectorAll('.filter-check').forEach(check => {
            check.addEventListener('change', () => calendar.refetchEvents());
        });

        // Listeners visuais
        document.querySelectorAll('.opcao-cor').forEach(opt => {
            opt.addEventListener('click', function() {
                selecionarCor(this.dataset.cor);
            });
        });
        document.getElementById('checkRecorrencia').addEventListener('change', function() {
            document.getElementById('opcoesRecorrencia').classList.toggle('d-none', !this.checked);
        });
    });

    // Salvar
    document.getElementById('formEvento').addEventListener('submit', function(e) {
        e.preventDefault();

        // Coleta participantes (Array)
        let participantes = [];
        if (document.getElementById('checkBernardo').checked) participantes.push('Bernardo');
        if (document.getElementById('checkGabriele').checked) participantes.push('Gabriele');

        if (participantes.length === 0) {
            Swal.fire('Atenção', 'Selecione pelo menos um participante!', 'warning');
            return;
        }

        const id = document.getElementById('eventoId').value;
        const url = id ? `/calendario/${id}` : '/calendario';
        const method = id ? 'PUT' : 'POST';

        const dados = {
            titulo: document.getElementById('titulo').value,
            participantes: participantes, // Envia array
            inicio: document.getElementById('inicio').value,
            fim: document.getElementById('fim').value,
            cor: document.getElementById('cor').value,
            descricao: document.getElementById('descricao').value,
            recorrencia: document.getElementById('checkRecorrencia').checked ? document.getElementById('tipoRecorrencia').value : 'nenhuma',
            recorrencia_fim: document.getElementById('fimRecorrencia').value
        };

        fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(dados)
            })
            .then(res => res.json())
            .then(data => {
                modal.hide();
                calendar.refetchEvents();
                Swal.fire({
                    icon: 'success',
                    title: 'Salvo!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
    });

    // Funções auxiliares (deletar, atualizar datas, limpar form) mantêm-se similares
    // Apenas lembre de no 'atualizarDatas' incluir o campo 'participantes'
    function atualizarDatas(evento) {
        fetch(`/calendario/${evento.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                titulo: evento.title.replace(/^\[.*?\]\s/, ''),
                participantes: evento.extendedProps.participantes,
                inicio: formatarDataISO(evento.start),
                fim: evento.end ? formatarDataISO(evento.end) : formatarDataISO(evento.start),
                cor: evento.backgroundColor,
                descricao: evento.extendedProps.descricao
            })
        }); // Opcional: Adicionar feedback visual aqui
    }

    function deletarEvento() {
        const id = document.getElementById('eventoId').value;
        Swal.fire({
            title: 'Tem certeza?',
            text: "Deseja excluir este evento?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sim, excluir'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/calendario/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(() => {
                        modal.hide();
                        calendar.refetchEvents();
                        Swal.fire('Excluído!', '', 'success');
                    });
            }
        });
    }

    function limparFormulario() {
        document.getElementById('formEvento').reset();
        document.getElementById('eventoId').value = '';
        selecionarCor('#3788d8');
        document.getElementById('opcoesRecorrencia').classList.add('d-none');
        // Default: ambos marcados
        document.getElementById('checkBernardo').checked = true;
        document.getElementById('checkGabriele').checked = true;
    }

    function selecionarCor(cor) {
        document.querySelectorAll('.opcao-cor').forEach(el => el.classList.remove('selected'));
        const el = document.querySelector(`.opcao-cor[data-cor="${cor}"]`);
        if (el) el.classList.add('selected');
        document.getElementById('cor').value = cor;
    }

    function formatarDataISO(date) {
        if (!date) return '';
        const d = new Date(date);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        return d.toISOString().slice(0, 16);
    }

    function abrirModalCriacao() {
        const hoje = new Date().toISOString();
        calendar.trigger('select', {
            startStr: hoje,
            endStr: hoje
        });
    }
</script>
@endpush