<?php
// Create missing Spatie permission tables
require __DIR__ . '/../bootstrap/app.php';

$app = app();
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Creating missing permission tables...\n";

// Create permissions table
if (!Schema::hasTable('permissions')) {
    Schema::create('permissions', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->string('guard_name')->default('web');
        $table->timestamps();
    });
    echo "✓ Created permissions table\n";
} else {
    echo "✓ permissions table already exists\n";
}

// Create model_has_roles table
if (!Schema::hasTable('model_has_roles')) {
    Schema::create('model_has_roles', function (Blueprint $table) {
        $table->unsignedBigInteger('role_id');
        $table->string('model_type');
        $table->unsignedBigInteger('model_id');
        
        $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
        $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
        
        $table->foreign('role_id')
            ->references('id')
            ->on('roles')
            ->onDelete('cascade');
    });
    echo "✓ Created model_has_roles table\n";
} else {
    echo "✓ model_has_roles table already exists\n";
}

// Create model_has_permissions table
if (!Schema::hasTable('model_has_permissions')) {
    Schema::create('model_has_permissions', function (Blueprint $table) {
        $table->unsignedBigInteger('permission_id');
        $table->string('model_type');
        $table->unsignedBigInteger('model_id');
        
        $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
        $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
        
        $table->foreign('permission_id')
            ->references('id')
            ->on('permissions')
            ->onDelete('cascade');
    });
    echo "✓ Created model_has_permissions table\n";
} else {
    echo "✓ model_has_permissions table already exists\n";
}

// Create role_has_permissions table
if (!Schema::hasTable('role_has_permissions')) {
    Schema::create('role_has_permissions', function (Blueprint $table) {
        $table->unsignedBigInteger('permission_id');
        $table->unsignedBigInteger('role_id');
        
        $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        
        $table->foreign('permission_id')
            ->references('id')
            ->on('permissions')
            ->onDelete('cascade');
        $table->foreign('role_id')
            ->references('id')
            ->on('roles')
            ->onDelete('cascade');
    });
    echo "✓ Created role_has_permissions table\n";
} else {
    echo "✓ role_has_permissions table already exists\n";
}

echo "\n✅ All permission tables created successfully!\n";
