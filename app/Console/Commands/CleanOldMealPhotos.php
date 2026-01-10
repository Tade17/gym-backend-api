<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MealLog;
use Illuminate\Support\Facades\Storage;


class CleanOldMealPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-old-meal-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Buscamos logs de hace más de 24 horas
        $oldLogs = MealLog::where('created_at', '<', now()->subDay())->get();

        foreach ($oldLogs as $log) {
            // 1. Borrar el archivo físico 
            $path = str_replace('/storage/', '', $log->photo_url);
            Storage::disk('public')->delete($path);

            // 2. Limpiar el registro en la BD o borrar el log (según prefieras)
            $log->update(['photo_url' => null]);
        }
        $this->info('Fotos antiguas eliminadas correctamente.');
    }
}
