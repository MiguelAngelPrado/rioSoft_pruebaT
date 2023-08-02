<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ConfigDocController;
use DB;

class RegisteredVisits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:generate_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de recordatorio de documentos faltantes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controlador = new ConfigDocController();
        $controlador->tarea_envio();
        return 0;
    }
}
