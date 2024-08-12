$(document).ready(function () {
    const $cidadeSelect = $('#cidade');
    const $categoriaSelect = $('#categoria');
    const $form = $('#calculo-form');
    const $resultadoDiv = $('#resultado');

    const $historicoDiv = $('#historico');
    const $paginacaoDiv = $('#paginacao');
    let currentPage = 1;

    $cidadeSelect.change(function () {
        const cidadeId = $(this).val();

        if (cidadeId) {  // Verifica se o valor não é nulo
            $categoriaSelect.html('<option value="">Carregando...</option>');

            $.getJSON(`./Controllers/CategoriaController.php?action=getCategoriasPorCidade&cidade_id=${cidadeId}`, function (data) {
                $categoriaSelect.html('<option value="">Selecione uma categoria</option>');
                if (data.length > 0) {
                    $.each(data, function (index, categoria) {
                        $categoriaSelect.append($('<option>', {
                            value: categoria.id,
                            text: categoria.nome
                        }));
                    });
                } else {
                    $categoriaSelect.html('<option value="">Nenhuma categoria disponível</option>');
                }
            });
        } else {
            $categoriaSelect.html('<option value="">Selecione uma cidade primeiro</option>');
        }
    });

    $form.submit(function (e) {
        e.preventDefault();

        const formData = $form.serialize();

        $.post('../questao-02-e-05/api/calculoGoogle', formData, function (data) {
            if (data.error) {
                $resultadoDiv.text(`Erro ao acessar a API: ${data.error}`);
            } else {
                $resultadoDiv.html(`
                    <div class="resultado-calc">
                        <p><strong>Estimativa de Tarifa:</strong></p>
                        <p><strong>R$ ${data.valor_calculado}</strong></p>
                    </div>
                `);
                carregarHistorico();
            }
        }, 'json');
    });

    // Função para formatar a data no padrão brasileiro
    function formatarData(dataHora) {
        const data = new Date(dataHora);
        const dia = String(data.getDate()).padStart(2, '0');
        const mes = String(data.getMonth() + 1).padStart(2, '0'); // Janeiro é 0!
        const ano = data.getFullYear();
        const horas = String(data.getHours()).padStart(2, '0');
        const minutos = String(data.getMinutes()).padStart(2, '0');

        return `${horas}:${minutos}`;
    }


    function carregarHistorico(page = 1) {
        $.getJSON('./Controllers/HistoricoController.php?page=' + page, function (data) {
            $historicoDiv.empty();

            if (data.historico.length === 0) {
                $historicoDiv.text('Nenhum cálculo realizado.');
                return;
            }

            $.each(data.historico, function (index, item) {
                const dataFormatada = formatarData(item.data_hora_corrida);
                const p = $('<p>').text(`Em ${item.cidade_nome}, ${item.categoria_nome}, de ${item.endereco_origem} para ${item.endereco_destino}, às ${dataFormatada}: R$ ${item.tarifa_calculada}`);
                $historicoDiv.append(p);
            });

            $paginacaoDiv.empty();

            if (data.currentPage > 1) {
                const prevButton = $('<button>').text('Anterior');
                prevButton.click(function () {
                    currentPage--;
                    carregarHistorico(currentPage);
                });
                $paginacaoDiv.append(prevButton);
            }

            for (let i = 1; i <= data.totalPages; i++) {
                const button = $('<button>').text(i);
                if (i === data.currentPage) {
                    button.attr('disabled', true);
                }
                button.click(function () {
                    currentPage = i;
                    carregarHistorico(currentPage);
                });
                $paginacaoDiv.append(button);
            }

            if (data.currentPage < data.totalPages) {
                const nextButton = $('<button>').text('Próximo');
                nextButton.click(function () {
                    currentPage++;
                    carregarHistorico(currentPage);
                });
                $paginacaoDiv.append(nextButton);
            }
        });
    }

    carregarHistorico();

});
