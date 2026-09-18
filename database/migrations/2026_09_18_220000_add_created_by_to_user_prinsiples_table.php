<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('user_prinsiples') && !Schema::hasColumn('user_prinsiples', 'created_by')) {
            Schema::table('user_prinsiples', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->index()->after('status');
            });
        }

        if (Schema::hasTable('userprinsiple') && !Schema::hasColumn('userprinsiple', 'created_by')) {
            Schema::table('userprinsiple', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->index();
            });
        }

        // Backfill created_by for existing records based on candidate assignments
        // 1. By recruiter_id
        if (Schema::hasTable('candidates') && Schema::hasTable('user_prinsiples')) {
            $candLinks = DB::table('candidates')
                ->whereNotNull('idprinsiple')
                ->where('idprinsiple', '!=', 0)
                ->where('idprinsiple', '!=', '')
                ->whereNotNull('recruiter_id')
                ->select('idprinsiple', 'recruiter_id')
                ->distinct()
                ->get();

            foreach ($candLinks as $link) {
                DB::table('user_prinsiples')
                    ->where('id', $link->idprinsiple)
                    ->whereNull('created_by')
                    ->update(['created_by' => $link->recruiter_id]);

                if (Schema::hasTable('userprinsiple')) {
                    DB::table('userprinsiple')
                        ->where('id', $link->idprinsiple)
                        ->whereNull('created_by')
                        ->update(['created_by' => $link->recruiter_id]);
                }
            }

            // 2. By useras matching users.name or users.email
            $users = DB::table('users')->get(['id', 'name', 'email', 'area']);
            foreach ($users as $u) {
                $matchingCandPrinIds = DB::table('candidates')
                    ->whereNotNull('idprinsiple')
                    ->where('idprinsiple', '!=', 0)
                    ->where('idprinsiple', '!=', '')
                    ->where(function ($q) use ($u) {
                        $q->whereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($u->email))])
                          ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($u->name))]);
                    })
                    ->pluck('idprinsiple')
                    ->unique();

                if ($matchingCandPrinIds->isNotEmpty()) {
                    DB::table('user_prinsiples')
                        ->whereIn('id', $matchingCandPrinIds)
                        ->whereNull('created_by')
                        ->update(['created_by' => $u->id]);

                    if (Schema::hasTable('userprinsiple')) {
                        DB::table('userprinsiple')
                            ->whereIn('id', $matchingCandPrinIds)
                            ->whereNull('created_by')
                            ->update(['created_by' => $u->id]);
                    }
                }

                // 3. Match by exact Area if user has an area and record area matches
                if (!empty($u->area) && strtolower($u->area) !== 'jakarta' && strtolower($u->area) !== 'nasional') {
                    DB::table('user_prinsiples')
                        ->whereNull('created_by')
                        ->whereRaw('LOWER(TRIM(area)) = ?', [strtolower(trim($u->area))])
                        ->update(['created_by' => $u->id]);

                    if (Schema::hasTable('userprinsiple')) {
                        DB::table('userprinsiple')
                            ->whereNull('created_by')
                            ->whereRaw('LOWER(TRIM(area)) = ?', [strtolower(trim($u->area))])
                            ->update(['created_by' => $u->id]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_prinsiples') && Schema::hasColumn('user_prinsiples', 'created_by')) {
            Schema::table('user_prinsiples', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }

        if (Schema::hasTable('userprinsiple') && Schema::hasColumn('userprinsiple', 'created_by')) {
            Schema::table('userprinsiple', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }
};
