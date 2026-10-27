
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_id', 30)->nullable()->unique();
            $table->string('mobile', 20)->nullable()->unique();
            $table->text('address')->nullable();

            $table->timestamp('phone_verified_at')->nullable();

            // pending, active, suspended, disabled
            $table->string('status', 20)->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropUnique(['mobile']);

            $table->dropColumn([
                'user_id',
                'mobile',
                'address',
                'phone_verified_at',
                'status',
            ]);
        });
    }
};
