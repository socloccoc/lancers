<?php

namespace App\Http\Controllers;
use Goutte\Client;

class HomeController extends Controller
{
    public function __construct()
    {
    }

    public function index() {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.lancers.jp/user/login');
        $form = $crawler->selectButton('ログイン')->form();
        $crawler = $client->submit($form, array('data[User][email]' => 'daihusk57@gmail.com', 'data[User][password]' => 'Dai@1234'));

        $crawler = $client->request('GET', 'https://www.lancers.jp/client/5fbb522f84612');
        $crawler->filter('.flash-error')->each(function ($node) {
            print $node->text()."\n";
        });
        echo $crawler->html();
    }
}
