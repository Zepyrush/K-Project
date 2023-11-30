<?php

namespace App\Console\Commands;

use App\Models\ManajemenKuliah\Materi;
use App\Models\ManajemenKuliah\Tugas;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ResetDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Resetting database...');

        // delete all files in assets/image/users/
        // $files = File::allFiles(public_path('assets/image/users/'));
        // foreach ($files as $file) {
        //     File::delete($file);
        // }

        // get materis table if type is pdf and created_at is today then get foreach and delete file
        $materis = Materi::where('tipe', 'pdf')->whereDate('created_at', date('Y-m-d'))->get();
        foreach ($materis as $materi) {
            $file = public_path('assets/file/materi/' . $materi->file_or_link);
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        // // get slides table if type is slide and created_at is today then get foreach and delete file
        // $slides = Materi::where('tipe', 'slide')->whereDate('created_at', date('Y-m-d'))->get();
        // foreach ($slides as $slide) {
        //     $file = public_path('assets/file/materi/' . $slide->file_or_link);
        //     if (File::exists($file)) {
        //         File::delete($file);
        //     }
        // }

        // // get tugass table if type is file and created_at is today then get foreach and delete file
        // $tugass = Tugas::where('tipe', 'file')->whereNull('mahasiswa_id')
        //     ->whereDate('created_at', date('Y-m-d'))->get();
        // foreach ($tugass as $tugas) {
        //     $file = public_path('assets/file/tugas/' . $tugas->file_or_link);
        //     if (File::exists($file)) {
        //         File::delete($file);
        //     }
        // }

        // reset database
        // $sqlPath = base_path('database/e-class2.sql');
        // DB::statement('DROP DATABASE `' . env('DB_DATABASE') . '`');
        // DB::statement('CREATE DATABASE `' . env('DB_DATABASE') . '`');
        // DB::statement('USE `' . env('DB_DATABASE') . '`');
        // DB::unprepared(file_get_contents($sqlPath));

        $this->info('Database reset successfully!');

        $date = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');
        Log::info('Database berhasil direset pada hari: ' . $date . ' - ' . date('H:i:s') . ' WIB');

        return Command::SUCCESS;
    }
}
