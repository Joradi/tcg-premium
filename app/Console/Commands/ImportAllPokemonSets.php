<?php

namespace App\Console\Commands;

use App\Models\Card;
use App\Models\CardSet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportAllPokemonSets extends Command
{
    protected $signature = 'pokemon:import-all-sets';
    protected $description = 'Importa el catálogo historico de cartas';

    public function handle()
    {
        $this->info('Conectando a la API de Pokémon...');
        $response = Http::timeout(60)->get('https://api.pokemontcg.io/v2/sets');

        if($response->failed()) {
            $this->error('Error al conectar con la API de Pokémon');
            return;
        }

        $sets = $response->json()['data'];

        $this->info('Se encontraron ' . count($sets) . ' sets. Iniciando importación...');

        foreach($sets as $set)
        {
            $setId = $set['id'];
            $this->info("Importando set: {$set['name']} ({$setId})...");

            try {
                // Intentamos hacer la petición
                $cardsResponse = Http::timeout(120)->get("https://api.pokemontcg.io/v2/cards", [
                    'q' => "set.id:{$setId}"
                ]);

                if($cardsResponse->failed())
                {
                    $this->error("Fallo al descargar {$set['name']}. Código HTTP: " . $cardsResponse->status());
                    sleep(3);
                    continue;
                }
            } catch (\Exception $e) {
                // Si hay un micro-corte de internet o cURL error 18, lo atrapamos aquí y no explota
                $this->error("Caída de conexión al descargar {$set['name']}. Saltando al siguiente...");
                sleep(3);
                continue;
            }

            $cardsData = $cardsResponse->json()['data'] ?? [];

            if(empty($cardsData))
            {
                continue;
            }

            $cardSet = CardSet::firstOrCreate(
                ['name' => $set['name']],
                ['set_total' => $set['printedTotal']]
            );

            foreach($cardsData as $cardData)
            {
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
            }

            $this->info("✓ Guardadas " . count($cardsData) . " cartas de {$set['name']}.");

            sleep(3); // Pausa de 3 segundos vital para no saturar la API
        }

        $this->newLine();
        $this->info("¡Operación titánica completada! Tu base de datos tiene toda la historia.");
    }
}
