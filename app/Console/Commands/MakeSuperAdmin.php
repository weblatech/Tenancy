<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:super-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote a user to Super Admin status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        $user->update(['is_super_admin' => true]);

        $this->info("User '{$user->name}' ({$email}) has been successfully promoted to Super Admin! 👑");
        return Command::SUCCESS;
    }
}
