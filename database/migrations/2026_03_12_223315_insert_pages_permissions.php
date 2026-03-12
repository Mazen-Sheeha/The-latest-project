<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;

return new class extends Migration {
    public function up(): void
    {
        Permission::create(['name' => 'صلاحية البكسلات']);
        Permission::create(['name' => 'صلاحية السلة المتروكة']);
    }

    public function down(): void
    {
        Permission::where('name', 'صلاحية البكسلات')->delete();
        Permission::where('name', 'صلاحية السلة المتروكة')->delete();
    }
};
