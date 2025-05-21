<?php
/**
 * Vista de listado de empresas
 * 
 * Variables requeridas:
 * @var array $empresas Listado de empresas
 * @var int $pagina Página actual
 * @var int $totalPaginas Total de páginas
 * @var int $itensPorPagina Items por página
 * @var array $filtros Filtros aplicados
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Lista de Empresas Cadastradas</h2>
        <a href="index.php?acao=cadastrar" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Empresa
        </a>
    </div>

    <!-- Card de Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-0">
                <input type="hidden" name="acao" value="listar">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="cnpj" class="form-label">CNPJ</label>
                        <input type="text" id="cnpj" name="cnpj" class="form-control cnpj-mask" 
                               placeholder="00.000.000/0000-00" 
                               value="<?= htmlspecialchars($filtros['cnpj'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="razao_social" class="form-label">Razão Social</label>
                        <input type="text" id="razao_social" name="razao_social" class="form-control" 
                               placeholder="Digite a razão social"
                               value="<?= htmlspecialchars($filtros['razao_social'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="municipio" class="form-label">Município</label>
                        <input type="text" id="municipio" name="municipio" class="form-control" 
                               placeholder="Digite o município"
                               value="<?= htmlspecialchars($filtros['municipio'] ?? '') ?>">
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <a href="index.php?acao=listar" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card de Resultados -->
    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Resultados</h5>
                <span class="badge bg-primary">
                    Total: <?= number_format($total, 0, ',', '.') ?>
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">CNPJ</th>
                            <th width="25%">Razão Social</th>
                            <th width="20%">Nome Fantasia</th>
                            <th width="20%">Município</th>
                            <th width="10%">UF</th>
                            <th width="10%" class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($empresas) > 0): ?>
                            <?php foreach ($empresas as $empresa): ?>
                            <tr>
                                <td><?= formatarCNPJ($empresa['cnpj']) ?></td>
                                <td><?= htmlspecialchars($empresa['razao_social']) ?></td>
                                <td><?= htmlspecialchars($empresa['nome_fantasia'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($empresa['municipio']) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($empresa['uf']) ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="index.php?acao=visualizar&id=<?= $empresa['id'] ?? '' ?>&cnpj=<?= $empresa['cnpj'] ?>" 
                                            class="btn btn-outline-primary" title="Visualizar">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        <a href="index.php?acao=editar&id=<?= $empresa['id'] ?>" 
                                           class="btn btn-outline-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="index.php?acao=excluir&id=<?= $empresa['id'] ?>" 
                                           class="btn btn-outline-danger" title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja excluir esta empresa?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="alert alert-warning mb-0">
                                        <i class="bi bi-exclamation-circle"></i> Nenhuma empresa encontrada
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
        <div class="card-footer">
            <nav aria-label="Navegação de páginas">
                <ul class="pagination justify-content-center mb-0">
                    <!-- Primeira página -->
                    <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= construirUrlPaginacao(1) ?>">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                    </li>
                    
                    <!-- Página anterior -->
                    <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= construirUrlPaginacao($pagina - 1) ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <!-- Páginas numeradas -->
                    <?php for ($i = max(1, $pagina - 2); $i <= min($pagina + 2, $totalPaginas); $i++): ?>
                        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                            <a class="page-link" href="<?= construirUrlPaginacao($i) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Próxima página -->
                    <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= construirUrlPaginacao($pagina + 1) ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    
                    <!-- Última página -->
                    <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= construirUrlPaginacao($totalPaginas) ?>">
                            <i class="bi bi-chevron-double-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
/**
 * Função para construir URL de paginação mantendo filtros
 */
function construirUrlPaginacao($pagina) {
    $params = ['acao' => 'listar', 'pagina' => $pagina];
    if (!empty($_GET['cnpj'])) $params['cnpj'] = $_GET['cnpj'];
    if (!empty($_GET['razao_social'])) $params['razao_social'] = $_GET['razao_social'];
    if (!empty($_GET['municipio'])) $params['municipio'] = $_GET['municipio'];
    return 'index.php?' . http_build_query($params);
}
?>