<?php

namespace App\Http\Controllers;
use Goutte\Client;

class HomeController extends Controller
{
    public function __construct()
    {
    }

    public function index() {
        $workDetailUrl = 'https://www.lancers.jp/work/detail/'.'3642710';
        echo $workDetailUrl."\n";
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.lancers.jp/user/login');
        $form = $crawler->selectButton('ログイン')->form();
        $client->submit($form, array('data[User][email]' =>  config('constants.accounts.lancers.email'), 'data[User][password]' =>  config('constants.accounts.lancers.pass')));
        $client = $client->request('GET', $workDetailUrl);
        $client->filter('ul.logoNuanceRange__list')->each(function ($node, $index){
            if($index == 1){
                dd($node->html());
            }
        });
    }

    public function crowdworks() {
        $client = new Client();
        $crawler = $client->request('GET', 'https://crowdworks.jp/login');
        $form = $crawler->selectButton('ログインする')->form();
        $crawler = $client->submit($form, array('username' => 'daihusk57@gmail.com', 'password' => 'Dai@1234'));

        $crawler = $client->request('GET', 'https://crowdworks.jp/public/employers/165402');
        $crawler->filter('.flash-error')->each(function ($node) {
            print $node->text()."\n";
        });
        echo $crawler->html();
    }

    public function sokudan() {
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
