<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote user to admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (empty($user)) {
            $this->info('User with email ' . $email . ' not found');
            return Command::FAILURE;
        }
        $user->role = User::ADMIN_ROLE;
        $user->save();
        $this->info('User with email ' . $email . ' was promoted to admin');
        return Command::SUCCESS;
    }
}
