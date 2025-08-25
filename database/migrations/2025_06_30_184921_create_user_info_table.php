<?php declare(strict_types=1);

use App\Models\Infra\UserInfo;
use Core\Domains\User\Models\UserSearcher;
use Core\Domains\User\UserLocator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_info')) {
            return;
        }

        Schema::create('user_info', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->date('membership_date')->nullable();
            $table->string('membership_duty_info')->nullable();
        });

        $users = UserLocator::UserService()->search(new UserSearcher()?->setWithExData())->getItems();

        foreach ($users as $user) {
            $exData            = $user?->getExData();
            $id                = $user->getId();
            $membershipDate     = $exData?->getMembershipDate()?->format("Y-m-d");
            $membershipDutyInfo = $exData?->getMembershipDutyInfo();

            if ( ! $exData?->getId()) {
                continue;
            }

            UserInfo::make([
                UserInfo::USER_ID             => $id,
                UserInfo::MEMBERSHIP_DATE      => $membershipDate,
                UserInfo::MEMBERSHIP_DUTY_INFO => $membershipDutyInfo,
            ])->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_info');
    }
};
