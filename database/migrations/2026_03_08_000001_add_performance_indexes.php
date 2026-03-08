<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes de performance sur les tables les plus sollicitées.
 * - blog_posts : tri par created_at (liste + prev/next), filtre is_published
 * - users      : lookup par email (login)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Blog posts ────────────────────────────────────────────────────
        if (Schema::hasTable('blog_posts')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                // Tri DESC le plus fréquent (liste, prev/next)
                if (! $this->indexExists('blog_posts', 'blog_posts_created_at_index')) {
                    $table->index('created_at', 'blog_posts_created_at_index');
                }
                // Filtre is_published si la colonne existe
                if (
                    Schema::hasColumn('blog_posts', 'is_published')
                    && ! $this->indexExists('blog_posts', 'blog_posts_is_published_created_at_index')
                ) {
                    $table->index(
                        ['is_published', 'created_at'],
                        'blog_posts_is_published_created_at_index'
                    );
                }
            });
        }

        // ── Users ─────────────────────────────────────────────────────────
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // L'email est souvent déjà unique (index implicite), on ajoute role_id
                if (
                    Schema::hasColumn('users', 'role_id')
                    && ! $this->indexExists('users', 'users_role_id_index')
                ) {
                    $table->index('role_id', 'users_role_id_index');
                }
            });
        }

        // ── Public service categories ─────────────────────────────────────
        if (Schema::hasTable('public_service_categories')) {
            Schema::table('public_service_categories', function (Blueprint $table) {
                if (! $this->indexExists('public_service_categories', 'psc_is_active_sort_order_index')) {
                    $table->index(
                        ['is_active', 'sort_order'],
                        'psc_is_active_sort_order_index'
                    );
                }
            });
        }

        // ── Public services ───────────────────────────────────────────────
        if (Schema::hasTable('public_services')) {
            Schema::table('public_services', function (Blueprint $table) {
                if (! $this->indexExists('public_services', 'ps_is_active_sort_order_index')) {
                    $table->index(
                        ['is_active', 'sort_order'],
                        'ps_is_active_sort_order_index'
                    );
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blog_posts')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropIndexIfExists('blog_posts_created_at_index');
                $table->dropIndexIfExists('blog_posts_is_published_created_at_index');
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndexIfExists('users_role_id_index');
            });
        }
        if (Schema::hasTable('public_service_categories')) {
            Schema::table('public_service_categories', function (Blueprint $table) {
                $table->dropIndexIfExists('psc_is_active_sort_order_index');
            });
        }
        if (Schema::hasTable('public_services')) {
            Schema::table('public_services', function (Blueprint $table) {
                $table->dropIndexIfExists('ps_is_active_sort_order_index');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select(
                "SELECT INDEX_NAME FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND INDEX_NAME = ?",
                [$table, $index]
            );
            return count($indexes) > 0;
        } catch (\Throwable) {
            return false;
        }
    }
};
