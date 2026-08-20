<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $normalize = static function ($value) {
            if (!is_string($value)) {
                return $value;
            }

            $value = trim(preg_replace('/\s+/u', ' ', $value) ?? $value);

            return mb_strtolower($value, 'UTF-8');
        };

        $reassign = static function (string $table, string $column, int $fromId, int $toId): void {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                DB::table($table)->where($column, $fromId)->update([$column => $toId]);
            }
        };

        $mergeKelas = static function (string $groupColumn) use ($normalize, $reassign): void {
            if (!Schema::hasTable('kelas') || !Schema::hasColumn('kelas', $groupColumn)) {
                return;
            }

            $rows = DB::table('kelas')->orderBy('id')->get(['id', $groupColumn, 'deleted_at']);
            $groups = $rows->filter(fn($row) => $normalize($row->{$groupColumn}) !== '')
                ->groupBy(fn($row) => $normalize($row->{$groupColumn}));

            foreach ($groups as $group) {
                if ($group->count() < 2) {
                    continue;
                }

                $canonical = $group->sortBy(fn($row) => [$row->deleted_at !== null ? 1 : 0, $row->id])->first();

                foreach ($group as $duplicate) {
                    if ($duplicate->id === $canonical->id) {
                        continue;
                    }

                    $reassign('siswas', 'kelas_id', $duplicate->id, $canonical->id);
                    $reassign('jadwals', 'kelas_id', $duplicate->id, $canonical->id);
                    $reassign('users', 'kelas_id', $duplicate->id, $canonical->id);
                    $reassign('guru_mengajars', 'kelas_id', $duplicate->id, $canonical->id);
                    DB::table('kelas')->where('id', $duplicate->id)->delete();
                }
            }
        };

        $mergeMataPelajaran = static function (string $groupColumn) use ($normalize, $reassign): void {
            if (!Schema::hasTable('mata_pelajarans') || !Schema::hasColumn('mata_pelajarans', $groupColumn)) {
                return;
            }

            $rows = DB::table('mata_pelajarans')->orderBy('id')->get(['id', $groupColumn, 'deleted_at']);
            $groups = $rows->filter(fn($row) => $normalize($row->{$groupColumn}) !== '')
                ->groupBy(fn($row) => $normalize($row->{$groupColumn}));

            foreach ($groups as $group) {
                if ($group->count() < 2) {
                    continue;
                }

                $canonical = $group->sortBy(fn($row) => [$row->deleted_at !== null ? 1 : 0, $row->id])->first();

                foreach ($group as $duplicate) {
                    if ($duplicate->id === $canonical->id) {
                        continue;
                    }

                    $reassign('jadwals', 'mata_pelajaran_id', $duplicate->id, $canonical->id);
                    $reassign('guru_mengajars', 'mata_pelajaran_id', $duplicate->id, $canonical->id);
                    DB::table('mata_pelajarans')->where('id', $duplicate->id)->delete();
                }
            }
        };

        DB::transaction(function () use ($normalize, $mergeKelas, $mergeMataPelajaran): void {
            $mergeKelas('nama');
            $mergeMataPelajaran('kode');
            $mergeMataPelajaran('nama');

            $tables = [
                'kelas' => ['nama', 'jurusan', 'ruangan', 'status'],
                'mata_pelajarans' => ['kode', 'nama', 'kategori', 'status'],
                'gurus' => ['status'],
                'siswas' => ['status'],
                'jadwals' => ['ruangan', 'status'],
                'kehadirans' => ['status'],
                'kehadiran_gurus' => ['status_kehadiran'],
                'izin_gurus' => ['jenis_izin', 'status_approval'],
                'guru_pengganties' => ['status_penggantian'],
            ];

            foreach ($tables as $table => $columns) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                DB::table($table)->orderBy('id')->chunkById(250, function ($rows) use ($table, $columns, $normalize): void {
                    foreach ($rows as $row) {
                        $updates = [];

                        foreach ($columns as $column) {
                            if (!Schema::hasColumn($table, $column)) {
                                continue;
                            }

                            $value = $row->{$column} ?? null;

                            if (is_string($value)) {
                                $normalized = $normalize($value);

                                if ($normalized !== $value) {
                                    $updates[$column] = $normalized;
                                }
                            }
                        }

                        if ($updates !== []) {
                            DB::table($table)->where('id', $row->id)->update($updates);
                        }
                    }
                });
            }

            if (Schema::hasTable('kelas') && Schema::hasColumn('kelas', 'jumlah_siswa') && Schema::hasTable('siswas')) {
                $kelasIds = DB::table('kelas')->pluck('id');

                foreach ($kelasIds as $kelasId) {
                    $count = DB::table('siswas')->where('kelas_id', $kelasId)->whereNull('deleted_at')->count();
                    DB::table('kelas')->where('id', $kelasId)->update(['jumlah_siswa' => $count]);
                }
            }
        });

        if (Schema::hasTable('kelas') && Schema::hasColumn('kelas', 'nama')) {
            Schema::table('kelas', function (Blueprint $table): void {
                $table->unique('nama', 'kelas_nama_case_unique');
            });
        }

        if (Schema::hasTable('mata_pelajarans') && Schema::hasColumn('mata_pelajarans', 'nama')) {
            Schema::table('mata_pelajarans', function (Blueprint $table): void {
                $table->unique('nama', 'mata_pelajarans_nama_case_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kelas')) {
            Schema::table('kelas', function (Blueprint $table): void {
                $table->dropUnique('kelas_nama_case_unique');
            });
        }

        if (Schema::hasTable('mata_pelajarans')) {
            Schema::table('mata_pelajarans', function (Blueprint $table): void {
                $table->dropUnique('mata_pelajarans_nama_case_unique');
            });
        }
    }
};
