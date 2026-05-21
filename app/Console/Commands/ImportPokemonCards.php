<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CardSet;
use App\Models\Card;

class ImportPokemonCards extends Command
{
    // Aquí es donde definimos el nombre que Laravel va a reconocer en la terminal
    protected $signature = 'pokemon:import {set_id=swsh10 : El ID oficial del set en la API}';

    protected $description = 'Importa cartas desde la API oficial de Pokémon TCG a nuestra base de datos';

    public function handle()
    {
        $setId = $this->argument('set_id');
        $this->info("Conectando a la API de Pokemon TCG buscando el set: {$setId}...");

        $response = Http::timeout(60)->get("https://api.pokemontcg.io/v2/cards", [
            'q' => "set.id:{$setId}"
        ]);

        if ($response->failed()) {
            $this->error('¡Ups! Hubo un error al conectar con la API.');
            return;
        }

        $cardsData = $response->json()['data'];

        if (empty($cardsData)) {
            $this->warn("No se encontraron cartas para el ID: {$setId}.");
            return;
        }

        $this->info("¡Éxito! Se encontraron " . count($cardsData) . " cartas. Guardando en la base de datos...");

        $setInfo = $cardsData[0]['set'];
        $cardSet = CardSet::firstOrCreate(
            ['name' => $setInfo['name']],
            ['set_total' => $setInfo['printedTotal']]
        );

        $bar = $this->output->createProgressBar(count($cardsData));

        foreach ($cardsData as $cardData) {
            Card::updateOrCreate(
                [
                    'card_set_id' => $cardSet->id,
                    'card_number' => $cardData['number']
                ],
                [
                    'name' => $cardData['name'],
                    'card_type' => $cardData['supertype'] ?? 'Pokémon',
                    'artist' => $cardData['artist'] ?? 'Desconocido',
                    'image_url' => $cardData['images']['small'] ?? null,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("¡Importación del set '{$cardSet->name}' finalizada con éxito! Ya puedes buscarlas en tu panel.");
    }
}
