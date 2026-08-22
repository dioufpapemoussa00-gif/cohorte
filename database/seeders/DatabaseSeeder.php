<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\Publication;
use App\Models\Reponse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $groupeA = Promotion::factory()->create([
            'nom' => 'Développement Web 2026 — Groupe A',
            'code_invitation' => 'DWA2026',
        ]);

        $groupeB = Promotion::factory()->create([
            'nom' => 'Développement Web 2026 — Groupe B',
            'code_invitation' => 'DWB2026',
        ]);

        foreach ([$groupeA, $groupeB] as $promotion) {
            $membres = User::factory()
                ->count(8)
                ->create(['promotion_id' => $promotion->id]);

            Publication::factory()
                ->count(15)
                ->recycle($membres)
                ->create(['promotion_id' => $promotion->id]);

            Publication::factory()
                ->count(6)
                ->question()
                ->recycle($membres)
                ->create(['promotion_id' => $promotion->id])
                ->each(function (Publication $question) use ($membres) {
                    Reponse::factory()
                        ->count(rand(0, 3))
                        ->recycle($membres)
                        ->create(['publication_id' => $question->id]);
                });
        }

        $this->comptesDeDemonstration($groupeA, $groupeB);
    }

    private function comptesDeDemonstration(Promotion $a, Promotion $b): void
    {
        User::factory()->create([
            'name' => 'Awa Diop',
            'email' => 'awa@cohorte.test',
            'password' => Hash::make('password'),
            'promotion_id' => $a->id,
            'role' => 'apprenant',
        ]);

        User::factory()->create([
            'name' => 'Moussa Ba',
            'email' => 'moussa@cohorte.test',
            'password' => Hash::make('password'),
            'promotion_id' => $a->id,
            'role' => 'delegue',
        ]);

        User::factory()->create([
            'name' => 'Fatou Sow',
            'email' => 'fatou@cohorte.test',
            'password' => Hash::make('password'),
            'promotion_id' => $b->id,
            'role' => 'apprenant',
        ]);

        User::factory()->create([
            'name' => 'Formateur',
            'email' => 'formateur@cohorte.test',
            'password' => Hash::make('password'),
            'promotion_id' => null,
            'role' => 'enseignant',
        ]);
    }
}