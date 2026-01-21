<?php
require_once 'auth.php';
require_login(); // Wymagaj zalogowania na wszystkich stronach
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Warsztat Samochodowy</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }
        .dropdown-menu {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-car"></i> CRM Warsztat
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    
                    <?php if (has_permission('crm.read')): ?>
                    <!-- Moduł CRM -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarCRM" role="button" data-toggle="dropdown">
                            <i class="fas fa-briefcase"></i> CRM
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="contacts_list.php"><i class="fas fa-user"></i> Kontakty</a>
                            <a class="dropdown-item" href="leads_list.php"><i class="fas fa-user-plus"></i> Leady</a>
                            <a class="dropdown-item" href="opportunities_list.php"><i class="fas fa-chart-line"></i> Szanse</a>
                            <a class="dropdown-item" href="activities_list.php"><i class="fas fa-tasks"></i> Aktywności</a>
                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (has_permission('service.read') || has_role('ADMIN')): ?>
                    <!-- Moduł Serwis -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarService" role="button" data-toggle="dropdown">
                            <i class="fas fa-tools"></i> Serwis
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="klient_add.php"><i class="fas fa-user-circle"></i> Klienci</a>
                            <a class="dropdown-item" href="pojazd_add.php"><i class="fas fa-car"></i> Pojazdy</a>
                            <a class="dropdown-item" href="zlecenie_add.php"><i class="fas fa-wrench"></i> Zlecenia</a>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarUser" role="button" data-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars(get_current_user_full_name()); ?>
                            <span class="badge badge-info"><?php echo htmlspecialchars($_SESSION['role_name']); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#"><i class="fas fa-user-edit"></i> Profil</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Wyloguj</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="row mt-4">
            <div class="col-md-12">
                <!-- Content of the page goes here -->
