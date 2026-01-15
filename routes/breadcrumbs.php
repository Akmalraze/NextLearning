<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

Breadcrumbs::for('teacher.index', function (BreadcrumbTrail $trail): void {
    $trail->push('Dashboard', route('teacher.index'));
});
Breadcrumbs::for('teacher.users.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('teacher.index');
    $trail->push('Users', route('teacher.users.index'));
});
Breadcrumbs::for('teacher.users.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('teacher.users.index');
    $trail->push('Add new user', route('teacher.users.create'));
});
// profile
Breadcrumbs::for('teacher.profile.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('teacher.index');
    $trail->push('Profile', route('teacher.profile.index'));
});
// change password
Breadcrumbs::for('teacher.password.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('teacher.index');
    $trail->push('Change Password', route('teacher.password.index'));
});

Breadcrumbs::for('report', function (BreadcrumbTrail $trail): void {
    $trail->push('Dashboard', route('home'));  // You can replace this with your dashboard route
    $trail->push('Report', route('report'));
});

Breadcrumbs::for('subject', function (BreadcrumbTrail $trail): void {
    $trail->push('Dashboard', route('home'));  // Replace with your dashboard route
    $trail->push('Subject', route('subject'));
});

Breadcrumbs::for('module', function (BreadcrumbTrail $trail): void {
    $trail->push('Dashboard', route('home'));  // Replace with your dashboard route
    $trail->push('Module', route('module'));
});

Breadcrumbs::for('class', function (BreadcrumbTrail $trail): void {
    $trail->push('Dashboard', route('home'));  // Replace with your dashboard route
    $trail->push('Class', route('class'));
});

Breadcrumbs::for('assessment', function (BreadcrumbTrail $trail): void {
    $trail->push('Dashboard', route('home'));  // Replace with your dashboard route
    $trail->push('Assessment', route('assessment'));
});
