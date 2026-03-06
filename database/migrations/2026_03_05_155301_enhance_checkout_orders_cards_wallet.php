<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'wallet_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('wallet_balance', 10, 2)->default(0)->after('avatar');
            });
        }

        if (Schema::hasTable('cards')) {
            Schema::table('cards', function (Blueprint $table) {
                if (!Schema::hasColumn('cards', 'alias')) {
                    $table->string('alias')->nullable()->after('last4');
                }
                if (!Schema::hasColumn('cards', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('alias');
                }
                if (!Schema::hasColumn('cards', 'exp_month')) {
                    $table->unsignedTinyInteger('exp_month')->nullable()->after('is_default');
                }
                if (!Schema::hasColumn('cards', 'exp_year')) {
                    $table->unsignedSmallInteger('exp_year')->nullable()->after('exp_month');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'subtotal')) {
                    $table->decimal('subtotal', 10, 2)->default(0)->after('phone');
                }
                if (!Schema::hasColumn('orders', 'shipping_fee')) {
                    $table->decimal('shipping_fee', 10, 2)->default(0)->after('subtotal');
                }
                if (!Schema::hasColumn('orders', 'service_fee')) {
                    $table->decimal('service_fee', 10, 2)->default(0)->after('shipping_fee');
                }
                if (!Schema::hasColumn('orders', 'discount')) {
                    $table->decimal('discount', 10, 2)->default(0)->after('service_fee');
                }
                if (!Schema::hasColumn('orders', 'wallet_used')) {
                    $table->decimal('wallet_used', 10, 2)->default(0)->after('discount');
                }
                if (!Schema::hasColumn('orders', 'delivery_type')) {
                    $table->string('delivery_type')->default('shipping')->after('wallet_used');
                }
                if (!Schema::hasColumn('orders', 'pickup_point')) {
                    $table->string('pickup_point')->nullable()->after('delivery_type');
                }
                if (!Schema::hasColumn('orders', 'delivery_notes')) {
                    $table->text('delivery_notes')->nullable()->after('pickup_point');
                }
                if (!Schema::hasColumn('orders', 'tracking_code')) {
                    $table->string('tracking_code')->nullable()->after('delivery_notes');
                }
                if (!Schema::hasColumn('orders', 'card_id')) {
                    $table->foreignId('card_id')->nullable()->after('payment_method')->constrained('cards')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'card_id')) {
                    $table->dropConstrainedForeignId('card_id');
                }

                foreach ([
                    'tracking_code',
                    'delivery_notes',
                    'pickup_point',
                    'delivery_type',
                    'wallet_used',
                    'discount',
                    'service_fee',
                    'shipping_fee',
                    'subtotal',
                ] as $column) {
                    if (Schema::hasColumn('orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('cards')) {
            Schema::table('cards', function (Blueprint $table) {
                foreach (['exp_year', 'exp_month', 'is_default', 'alias'] as $column) {
                    if (Schema::hasColumn('cards', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'wallet_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('wallet_balance');
            });
        }
    }
};
