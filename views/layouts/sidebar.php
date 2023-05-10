<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

use yii\helpers\Url;
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block btn-link">
                    Rufusy Idachi
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="<?=Url::to(['/users'])?>" class="nav-link">
                        <i class="nav-icon fa fa-users" aria-hidden="true"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= Url::to(['/roles']); ?>" class="nav-link">
                        <i class="nav-icon fa fa-tasks" aria-hidden="true"></i>
                        <p>Roles</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= Url::to(['#']); ?>" class="nav-link">
                        <i class="nav-icon fas fa-lock" aria-hidden="true"></i>
                        <p>Permissions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= Url::to(['#']); ?>" class="nav-link">
                        <i class="nav-icon fa fa-cogs" aria-hidden="true"></i>
                        <p>Rules</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>