<?php 

// tests/Controller/TrainingControllerTest.php

namespace App\Tests;

use App\Entity\Training;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Module;

class TrainingControllerTest extends WebTestCase
{
    private static EntityManagerInterface $entityManager;
    private static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = static::createClient();
        self::$entityManager = self::$client->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        self::$entityManager->close();
    }

    public function testListNoModulesSelected()
    {
        // Simulate a request to the /search_training route with no modules selected
        $crawler = self::$client->request('GET', '/search_training');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Verify all trainings are listed when no module is selected
        $trainingRepository = self::$entityManager->getRepository(Training::class);
        $totalTrainings = count($trainingRepository->findAll());
        $this->assertCount($totalTrainings, $crawler->filter('table tbody tr'));
    }

    public function testListWithModulesSelectedMatchAny()
    {
        // Simulate a request to the /search_training route with some modules selected and match_any_module=true
        $crawler = self::$client->request('GET', '/search_training', [
            'modules' => [14, 15], // Assuming module IDs 1 and 2 exist
            'match_any_module' => true
        ]);

        $this->assertResponseIsSuccessful();

        // Assert that relevant trainings are shown based on the query parameters
        // Update this to check the actual result from your database or mock data
        $trainings = $crawler->filter('table tbody tr');
        $this->assertGreaterThan(0, $trainings->count());
    }

    public function testListWithModulesSelectedMatchAll()
    {
        // Simulate a request to the /search_training route with some modules selected and match_any_module=false
        $crawler = self::$client->request('GET', '/search_training', [
            'modules' => [14, 15], // Assuming module IDs 1 and 2 exist
            'match_any_module' => false
        ]);

        $this->assertResponseIsSuccessful();

        // Assert that relevant trainings are shown based on the query parameters
        // Update this to check the actual result from your database or mock data
        $trainings = $crawler->filter('table tbody tr');
        $this->assertGreaterThan(0, $trainings->count());
    }

    public function testInvalidModuleSelection()
    {
        // Simulate a request with an invalid module ID
        $crawler = self::$client->request('GET', '/search_training', [
            'modules' => ['invalid_id']
        ]);

        $this->assertResponseIsSuccessful();

        // Assert that no trainings are shown or an appropriate error message is displayed
        $this->assertSelectorTextContains('body', 'Aucune formation ne dispense ce module');
    }

    ///////////////////////////
    // TDD ////////////////////
    ///////////////////////////

    // // Teste que la page renvoie un statut HTTP 200
    // public function testPageIsSuccessful(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/manage-training/molestiae');

    //     // Teste que la page renvoie un statut HTTP 200
    //     $this->assertResponseIsSuccessful();

    //     // Teste que le titre de la page est affiché correctement
    //     $this->assertSelectorTextContains('h1', 'Manage Training: molestiae');
    // }

    // // Teste que la page contient une liste de modules
    // public function testModulesAreListed(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/manage-training/molestiae');

    //     // Teste que les modules sont affichés dans une liste non ordonnée (ul)
    //     $this->assertCount(2, $crawler->filter('ul > li'));

    //     // Teste que les noms des modules sont correctement affichés
    //     $this->assertSelectorTextContains('li', 'voluptas');
    //     $this->assertSelectorTextContains('li', 'ipsam');

    //     // Teste que chaque module a un bouton de suppression
    //     $this->assertCount(2, $crawler->filter('li > .delete-button'));
    // }

    // // Teste la sécurité du bouton de suppression pour le premier module
    // public function testDeleteModuleViaGet(): void
    // {
    //     $client = static::createClient();

    //     // Récupérer une formation aléatoire de la base de données
    //     $training = $this->entityManager->getRepository(Training::class)->findOneBy([]);
    //     $this->assertNotNull($training, 'Aucune formation trouvée dans la base de données.');

    //     // Récupérer un module aléatoire associé à cette formation
    //     $module = $this->entityManager->getRepository(Module::class)->findOneBy(['training' => $training]);
    //     $this->assertNotNull($module, 'Aucun module trouvé pour cette formation.');

    //     $crawler = $client->request('GET', '/manage-training/' . $training->getName());

    //     // Vérifier que le module récupéré existe avant de le supprimer
    //     $this->assertSelectorTextContains('li', $module->getName());

    //     // Obtenir dynamiquement l'ID du module
    //     $deleteButton = $crawler->filter('li:contains("' . $module->getName() . '") .delete-button')->link();
    //     $deleteUrl = $deleteButton->getUri();

    //     // Extraire l'ID directement depuis l'URL
    //     $moduleId = $this->extractIdFromUrl($deleteUrl);

    //     // Simuler une requête GET pour supprimer le module avec l'ID trouvé
    //     $client->request('GET', '/manage-module/delete/' . $moduleId . '/training/' . $training->getName());

    //     // Vérifie que la suppression redirige vers la bonne page
    //     $this->assertResponseRedirects('/manage-training/' . $training->getName());

    //     // Suivre la redirection
    //     $crawler = $client->followRedirect();

    //     // Vérifie que le module a été supprimé
    //     $this->assertSelectorTextNotContains('li', $module->getName());
    // }

    // // Fonction pour extraire l'ID du dernier segment d'une URL
    // private function extractIdFromUrl(string $url): int
    // {
    //     // Extraire l'ID du dernier segment de l'URL
    //     // Exemple d'URL: http://localhost/manage-module/delete/1
    //     $parts = explode('/', parse_url($url, PHP_URL_PATH));
    //     return (int) end($parts);
    // }
}