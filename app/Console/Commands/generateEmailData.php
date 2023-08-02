<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class generateEmailData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        DB::table('emails')->insert([
            'id_solicitud' => 2,
            'correos' => json_encode(['miguel@gmail.com']),
            'tipo' => 'notificacion de documentos',
            'asunto' => 'Solicitud de documentos',
            'texto' => 'documentos',
            'created_at' => date('Y-m-d H:i:')
        ]);
        return 0;
    }
}
