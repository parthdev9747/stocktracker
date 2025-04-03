<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Scanning controllers for permissions...');

        // Path to controllers directory
        $controllersPath = app_path('Http/Controllers');

        // Get all controller files
        $controllerFiles = File::allFiles($controllersPath);

        $permissions = [];

        foreach ($controllerFiles as $file) {
            $className = str_replace('.php', '', $file->getFilename());

            // Skip files that don't end with Controller
            if (!Str::endsWith($className, 'Controller')) {
                continue;
            }

            // Get full class name with namespace
            $fullClassName = 'App\\Http\\Controllers\\' . $className;

            // Check if class exists
            if (class_exists($fullClassName)) {
                try {
                    $this->command->info("Processing $className...");

                    // Get the file content to parse middleware
                    $content = File::get($file->getPathname());

                    // Extract permissions from constructor middleware method using regex
                    preg_match_all("/this->middleware\s*\(\s*'permission:(.*?)'\s*,/", $content, $constructorMatches);

                    // Also keep the original regex for attribute-based middleware
                    preg_match_all("/new\s+Middleware\s*\(\s*'permission:(.*?)'\s*,/", $content, $attributeMatches);

                    // Process constructor middleware matches
                    if (isset($constructorMatches[1]) && count($constructorMatches[1]) > 0) {
                        foreach ($constructorMatches[1] as $permissionString) {
                            // Split permissions if they are pipe-separated
                            $permissionList = explode('|', $permissionString);
                            foreach ($permissionList as $permission) {
                                $permission = trim($permission);
                                if (!empty($permission) && !in_array($permission, $permissions)) {
                                    $permissions[] = $permission;
                                }
                            }
                        }
                    }

                    // Process attribute middleware matches
                    if (isset($attributeMatches[1]) && count($attributeMatches[1]) > 0) {
                        foreach ($attributeMatches[1] as $permissionString) {
                            // Split permissions if they are pipe-separated
                            $permissionList = explode('|', $permissionString);
                            foreach ($permissionList as $permission) {
                                $permission = trim($permission);
                                if (!empty($permission) && !in_array($permission, $permissions)) {
                                    $permissions[] = $permission;
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $this->command->error("Error processing $className: " . $e->getMessage());
                }
            }
        }

        // Insert permissions into database if they don't exist
        $count = 0;
        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('name', $permission)->exists();

            if (!$exists) {
                // Extract group name from permission (everything after the dash)
                $groupName = 'general';
                if (strpos($permission, '-') !== false) {
                    $parts = explode('-', $permission);
                    $groupName = $parts[1];
                }

                DB::table('permissions')->insert([
                    'name' => $permission,
                    'guard_name' => 'web',
                    'group_name' => $groupName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
                $this->command->info("Added permission: $permission (Group: $groupName)");
            }
        }

        $this->command->info("Added $count new permissions. Total unique permissions found: " . count($permissions));
    }
}
