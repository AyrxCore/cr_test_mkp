<?php
namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Resources\DataFixtures\ContactTestFixtures;
use App\Tests\Resources\DataFixtures\UserTestFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Service\Attribute\Required;

class UrlsControllerTest extends WebTestCase
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public KernelBrowser $client;

    #[Required]
    public AbstractDatabaseTool $databaseTool;

    #[Required]
    public RouterInterface $router;


    public function setUp(): void
    {
        $_ENV["APP_ENV"] = "test";
        parent::setUp();
        $this->client = self::createClient();
        $kernel = static::bootKernel();
        $this->databaseTool = $kernel->getContainer()->get(DatabaseToolCollection::class)->get();
        $this->router = $kernel->getContainer()->get('router');
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }


    public function testRoutes(): void
    {
        $this->databaseTool->loadFixtures([
            UserTestFixtures::class
        ]);

        $checkNotLoggedUrls = [
            ['url' => $this->router->generate('app'), 'assertCode' => 200],
            ['url' => $this->router->generate('app_link'), 'assertCode' => 200],
            ['url' => $this->router->generate('get_me'), 'assertCode' => 401],
        ];


        $this->client->followRedirects(true);
        foreach ($checkNotLoggedUrls as $url){
            echo "Test de l'url " . $url["url"] . " attente d'un code http ".$url["assertCode"]."\n";
            $this->client->request('GET', $url["url"]);
            echo "Resultat de la requete " . $this->client->getResponse()->getStatusCode() . "\n";
            $this
                ->assertEquals($url["assertCode"],$this->client->getResponse()->getStatusCode());
        }
    }

    public function testGetToken()
    {

        $this->databaseTool->loadFixtures([
            UserTestFixtures::class
        ]);

        echo "test de récupération d'un cookie httpOnly ... \n";

        $this->client->request(
            'POST',
            $this->router->generate('authentication_token'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['username' => 'test@qantis.co','password'=>'0000'])
        );
        //$result = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseHasCookie('BEARER', '/', null, 'Aucun cookie présent');

    }

    private function logIn(User $user, $client)
    {
        /**
         * @var Session $session
         */
        $session = self::$container->get('session');
        $token = new UsernamePasswordToken($user,null,'main',$user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(),$session->getId());
        $client->getCookieJar()->set($cookie);
    }

    private function logout()
    {
        /**
         * @var Session $session
         */
        $session = self::$container->get('session');
        $session->clear();
        $this->client->getCookieJar()->clear();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->em->close();
    }


}
