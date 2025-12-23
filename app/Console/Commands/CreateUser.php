<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Validation\Rule;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "user:create";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create a new user from CLI.";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Creating a new user...");

        $name = $this->ask("Enter user's name");
        $email = $this->ask("Enter user's email adress");
        $password = $this->secret("Enter the password");

        $validator = \Validator::make(
            [
                "name" => $name,
                "email" => $email,
                "password" => $password,
            ],
            [
                "name" => ["required", "string", "max:255"],
                "email" => [
                    "required",
                    "string",
                    "email",
                    "max:255",
                    Rule::unique("users"),
                ],
                "password" => ["required", "string", "min:6", "max:20"],
            ],
        );

        if ($validator->fails()) {
            $this->error("Validation failed.");

            foreach ($validator->errors()->all() as $error) {
                $this->error("- $error");
            }
            return 1;
        }

        try {
            $user = new User([
                "name" => $name,
                "email" => $email,
                "password" => bcrypt($password),
            ]);
            $user->save();

            $this->info(
                "Success! User with name {$name} and email {$email} has been successfully created.",
            );

            $this->table(
                ["ID", "Name", "Email", "Created At"],
                [[$user->id, $user->name, $user->email, $user->created_at]],
            );

            return 0;
        } catch (\Exception $e) {
            $this->error(
                "There's an error when saving to database: " . $e->getMessage(),
            );

            return 1;
        }
    }
}
