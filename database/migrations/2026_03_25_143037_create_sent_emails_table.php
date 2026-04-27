<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasTable('sent_emails')) {
            Schema::create('sent_emails', static function (Blueprint $table) {
                $table->id();
                $table->string('message_id')->nullable()->unique(); // ID сообщения (например, из Mailgun/SES)
                $table->string('recipient_email');
                $table->string('recipient_name')->nullable();
                $table->string('subject')->nullable();
                $table->text('body')->nullable();          // можно хранить HTML или текст
                $table->json('attachments')->nullable();      // вложения
                $table->string('mailer')->nullable();      // драйвер
                $table->string('status')->default('sent'); // sent, delivered, opened, clicked, bounced, failed
                $table->json('metadata')->nullable();      // дополнительные данные (user_id, type и т.п.)
                $table->timestamp('sent_at');
                $table->timestamp('delivered_at')->nullable();
                $table->timestamps();

                $table->index('recipient_email');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_emails');
    }
};
