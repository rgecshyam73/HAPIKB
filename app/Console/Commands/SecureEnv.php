<?php

namespace App\Console\Commands;

use App\Security\FsdSecurity;
use Illuminate\Console\Command;

class SecureEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fsd:secure {--info}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Secure Variable to Store to Environment';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fsdSecure = new FsdSecurity();
        $isRequestedInfo = $this->option('info');
        $line = '=====================================================';

        $this->info('======== Setup Secure Environment Variable ==========');
        $this->info($line);
        $this->info('');
        $this->info('');

        if($isRequestedInfo)
        {
            $fsdSecure->decryptConfigInfo()->each(function($v, $k){
                $this->info($k .' = '. $v);
            });
        }else{
            $fsdSecure->encryptConfig();
        }

        $this->info('OK Your Config has been Secured');


        $this->info('Close this command prompt and restart your webserver');
        $this->info($line);
        $this->info('Focus Software Development (FSD)');
        $this->info($line);
    }


}
