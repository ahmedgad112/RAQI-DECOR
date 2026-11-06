<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->optional()->numerify('05########'),
            'job_title' => fake()->optional()->jobTitle(),
            'bio' => fake()->optional()->sentence(),
            'avatar_path' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Profile $profile): void {
            if ($profile->user_id === null) {
                return;
            }

            $existing = Profile::query()->where('user_id', $profile->user_id)->first();

            if ($existing === null) {
                return;
            }

            // Reuse the profile created by User::created so the unique user_id stays valid.
            $profile->id = $existing->id;
            $profile->exists = true;
        });
    }
}
