<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ( ! Schema::hasTable('ticket_categories')) {
            Schema::create('ticket_categories', static function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('type');
                $table->string('name', 100);
                $table->string('code', 50)->index();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if ( ! Schema::hasTable('ticket_services')) {
            Schema::create('ticket_services', static function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('cascade');
                $table->string('name', 200);
                $table->string('code', 50)->nullable()->index();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if ( ! Schema::hasTable('tickets')) {
            Schema::create('tickets', static function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedTinyInteger('type');
                $table->foreignId('category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
                $table->foreignId('service_id')->nullable()->constrained('ticket_services')->nullOnDelete();
                $table->unsignedTinyInteger('priority')->default(2);
                $table->unsignedTinyInteger('status')->default(1);
                $table->boolean('published')->default(false);
                $table->text('description');
                $table->text('result')->nullable();
                $table->string('contact_name')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('contact_email')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('account_id');
                $table->index('status');
                $table->index('priority');
            });
        }

        if ( ! Schema::hasTable('ticket_comments')) {
            Schema::create('ticket_comments', static function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->text('comment');
                $table->boolean('is_internal')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_services');
        Schema::dropIfExists('ticket_categories');
    }
};
