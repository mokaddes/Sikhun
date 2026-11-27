<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (! Schema::hasColumn('coupons', 'coupon_kind')) {
                $table->string('coupon_kind', 32)->default('discount')->after('description');
            }
            if (! Schema::hasColumn('coupons', 'discount_type')) {
                $table->string('discount_type', 24)->default('percent')->after('coupon_kind');
            }
            if (! Schema::hasColumn('coupons', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            }
            if (! Schema::hasColumn('coupons', 'max_discount_amount')) {
                $table->decimal('max_discount_amount', 10, 2)->nullable()->after('discount_value');
            }
            if (! Schema::hasColumn('coupons', 'min_order_amount')) {
                $table->decimal('min_order_amount', 10, 2)->nullable()->after('max_discount_amount');
            }
            if (! Schema::hasColumn('coupons', 'applicable_types')) {
                $table->json('applicable_types')->nullable()->after('min_order_amount');
            }
            if (! Schema::hasColumn('coupons', 'applicable_product_ids')) {
                $table->json('applicable_product_ids')->nullable()->after('applicable_types');
            }
            if (! Schema::hasColumn('coupons', 'allowed_student_ids')) {
                $table->json('allowed_student_ids')->nullable()->after('student_id');
            }
            if (! Schema::hasColumn('coupons', 'max_uses_per_user')) {
                $table->unsignedInteger('max_uses_per_user')->nullable()->after('max_uses');
            }
        });

        // Existing rows created before this upgrade were full-access grants.
        if (Schema::hasTable('coupons') && Schema::hasColumn('coupons', 'coupon_kind')) {
            DB::table('coupons')->update(['coupon_kind' => 'access_grant']);
        }

        Schema::table('coupon_redemptions', function (Blueprint $table) {
            if (! Schema::hasColumn('coupon_redemptions', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('student_id')->constrained('orders')->nullOnDelete();
            }
            if (! Schema::hasColumn('coupon_redemptions', 'invoice_id')) {
                $table->foreignId('invoice_id')->nullable()->after('order_id')->constrained('invoices')->nullOnDelete();
            }
            if (! Schema::hasColumn('coupon_redemptions', 'target_type')) {
                $table->string('target_type', 40)->nullable()->after('invoice_id');
            }
            if (! Schema::hasColumn('coupon_redemptions', 'target_id')) {
                $table->unsignedBigInteger('target_id')->nullable()->after('target_type');
            }
            if (! Schema::hasColumn('coupon_redemptions', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('target_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('coupon_redemptions', function (Blueprint $table) {
            if (Schema::hasColumn('coupon_redemptions', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }
            if (Schema::hasColumn('coupon_redemptions', 'invoice_id')) {
                $table->dropConstrainedForeignId('invoice_id');
            }
            $cols = array_values(array_filter(
                ['target_type', 'target_id', 'discount_amount'],
                fn (string $c) => Schema::hasColumn('coupon_redemptions', $c)
            ));
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('coupons', function (Blueprint $table) {
            $cols = array_values(array_filter([
                'coupon_kind',
                'discount_type',
                'discount_value',
                'max_discount_amount',
                'min_order_amount',
                'applicable_types',
                'applicable_product_ids',
                'allowed_student_ids',
                'max_uses_per_user',
            ], fn (string $c) => Schema::hasColumn('coupons', $c)));
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
