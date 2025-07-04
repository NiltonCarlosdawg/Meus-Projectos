<div class="sidebar" style="background: linear-gradient(180deg,  #91530c 10%,rgb(133, 74, 8)  100%);">
    <div class="sidebar-header">
        <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>SISTEMA DE GESTÃO DE PAP</h4>
    </div>
    <div class="sidebar-content">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/sidebar.js"></script>
        <ul class="sidebar-menu" id="sidebarMenu">
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/dashboard/admin/">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
            </li>
            <!-- Gestão de Usuários -->
            <li class="menu-section">
                <h4 class="menu-text">Gestão de Usuários</h4>
            </li>
            <li>
                <a href="#estudantesSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-user-graduate"></i>Estudantes
                    <span class="submenu-indicator"><i class="fas fa-chevron-right"></i></span>
                </a>
                <ul class="collapse submenu" id="estudantesSubmenu">
                    <li><a href="<?php echo BASE_URL; ?>/admin/cadastrar-estudante.php"><i class="fas fa-plus-circle"></i>Cadastrar Estudante</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/ativar-desativar-estudante.php"><i class="fas fa-toggle-on"></i>Ativar/Desativar Conta</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/atribuir-orientador.php"><i class="fas fa-user-plus"></i>Atribuir Orientador</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/consultar-projetos-estudante.php"><i class="fas fa-search"></i>Consultar Projetos</a></li>
                </ul>
            </li>
            <li>
                <a href="#orientadoresSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-chalkboard-teacher"></i>Docentes/Orientadores
                    <span class="submenu-indicator"><i class="fas fa-chevron-right"></i></span>
                </a>
                <ul class="collapse submenu" id="orientadoresSubmenu">
                    <li><a href="<?php echo BASE_URL; ?>/admin/cadastrar-orientador.php"><i class="fas fa-user-plus"></i>Cadastrar Orientador</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/atualizar-dados-orientador.php"><i class="fas fa-user-edit"></i>Atualizar Dados</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/consultar-carga-orientacao.php"><i class="fas fa-tasks"></i>Consultar Carga</a></li>
                </ul>
            </li>
            <li>
                <a href="#bancaSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-users-cog"></i>Júri/Banca
                    <span class="submenu-indicator"><i class="fas fa-chevron-right"></i></span>
                </a>
                <ul class="collapse submenu" id="bancaSubmenu">
                    <li><a href="<?php echo BASE_URL; ?>/admin/cadastrar-membro-banca.php"><i class="fas fa-user-plus"></i>Cadastrar Membro</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/associar-banca-projeto.php"><i class="fas fa-link"></i>Associar a Projeto</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/emitir-pauta.php"><i class="fas fa-file-alt"></i>Emitir Pauta</a></li>
                </ul>
            </li>
            <!-- Gestão de Defesas -->
            <li class="menu-section">
                <h4 class="menu-text">Gestão de Defesas</h4>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/gerenciar-defesas.php">
                    <i class="fas fa-tasks"></i>Gerenciar Defesas
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/estudantes-aptos.php">
                    <i class="fas fa-user-check"></i>Estudantes Aptos
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/agendamentos.php">
                    <i class="fas fa-calendar-alt"></i>Agendamentos
                </a>
            </li>
            <li>
                <a href="#documentosSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-file-pdf"></i>Documentos
                    <span class="submenu-indicator"><i class="fas fa-chevron-right"></i></span>
                </a>
                <ul class="collapse submenu" id="documentosSubmenu">
                    <li><a href="<?php echo BASE_URL; ?>/admin/gerar-ata.php"><i class="fas fa-file-alt"></i>Gerar Ata</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/gerar-pauta.php"><i class="fas fa-clipboard-list"></i>Gerar Pauta</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/gerar-convite.php"><i class="fas fa-envelope"></i>Gerar Convite</a></li>
                </ul>
            </li>
            <!-- Outras Seções -->
            <li class="menu-section">
                <h4 class="menu-text">Outras Seções</h4>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/temas-tfc.php">
                    <i class="fas fa-book"></i>Temas TFC
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/lista-tfcs.php">
                    <i class="fas fa-file-alt"></i>Lista de TFCs
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/relatorios.php">
                    <i class="fas fa-chart-bar"></i>Relatórios
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/configuracoes.php">
                    <i class="fas fa-cog"></i>Configurações
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/logout.php">
                    <i class="fas fa-sign-out-alt"></i>Sair
                </a>
            </li>
        </ul>
    </div>
</div>