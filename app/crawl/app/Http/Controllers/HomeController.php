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
        $crawler = $client->request('GET', 'https://sokudan.work/login');
        $form = $crawler->selectButton('ログイン')->form();
        $crawler = $client->submit($form, array('user[email]' => 'danghuuhai1@gmail.com', 'user[password]' => 'Dai21234'));

        $crawler = $client->request('GET', 'https://sokudan.work/corporations/1110');
        $crawler->filter('.flash-error')->each(function ($node) {
            print $node->text()."\n";
        });
        echo $crawler->html();
    }

    public function lancers() {
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
