<?php 

use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchoolTest extends WebTestCase
{
    private School $school;
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $school = new School();
        $school->setName("3WA");
        $school->setDescription("Ecole dans le 18 eme ");

        $this->entityManager->persist($school);
        $this->entityManager->flush();

        $this->school = $school;
    }

    public function tearDown(): void
    {
        if ($this->school && $this->entityManager) 
        {
            $this->entityManager->remove($this->school);
            $this->entityManager->flush();
        }

        parent::tearDown();
    }

    public function testSchool(): void
    {
        $school = new School();
        $school->setName("3WA");
        $this->assertEquals("3WA", $school->getName(), '3WA');
    }

    public function testSchoolWithDatabase(): void
    {
        $repository = $this->entityManager->getRepository(School::class);
        $school = $repository->find($this->school->getId());

        $this->assertEquals("3WA", $school->getName(), '3WA');

        // Utiliser l'URL correcte pour votre route
        $crawler = $this->client->request('GET', '/school/' . $school->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', '3WA');
    }
}