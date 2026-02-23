<?php
namespace Database\Factories\DataMaster;

use App\Models\DataMaster\Profesi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DataMaster\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama'       => strtoupper($this->faker->name()),
            'alamat'     => $this->faker->streetAddress(),
            'no_hp'      => $this->faker->numerify('08##########'),
            'id_profesi' => Profesi::factory(),
            'id_user'    => User::factory(),
        ];
    }
}