<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;

foreach (Role::all() as $role) {
    echo $role->slug . ' => ' . $role->name . ' (' . $role->users()->count() . ' users)\n';
}
