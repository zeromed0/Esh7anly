
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Game;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // روابط المستخدم واللعبة والعرض
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Game::class)->constrained()->onDelete('cascade'); // game_id
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            
            $table->enum('type', ['topup', 'voucher'])->default('topup');
            $table->string('player_id');
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 12, 2);
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};