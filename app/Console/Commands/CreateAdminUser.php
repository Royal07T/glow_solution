<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--email=} {--name=} {--password=}';
    protected $description = 'Create a new administrator account';

    public function handle()
    {
        $email = $this->option('email') ?: $this->ask('Email');
        $name = $this->option('name') ?: $this->ask('Name');
        $password = $this->option('password') ?: $this->secret('Password');

        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ], [
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:12'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info("Admin user created: {$user->email}");
        return Command::SUCCESS;
    }
}
