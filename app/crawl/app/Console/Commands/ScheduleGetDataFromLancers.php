<?php

namespace App\Console\Commands;

use App\Models\LancersClient;
use Illuminate\Console\Command;
use Goutte\Client;

class ScheduleGetDataFromLancers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lancers:crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from lancers page!';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $page = (int)file_get_contents(public_path('page.txt'));
        while (true) {
            $client = new Client();
            $crawler = $client->request('GET', 'https://www.lancers.jp/client_directory/all/industry/2116?page=' . $page);
            $result = $crawler->filter('.c-media-clients__item')->count();
            if ($result == 0) {
                break;
            }
            $data = [];
            $crawler->filterXPath('//div[@class="p-client-directory__list c-media-clients"]/div')->each(function ($node) use (&$client, &$data) {
                $clientDetailUrl = 'https://www.lancers.jp' . $node->filter('a')->attr('href');
//                $clientDetailUrl = 'https://www.lancers.jp/client/scratch160';
                echo $clientDetailUrl."\n";
                $client = new Client();
                $client = $client->request('GET', $clientDetailUrl);
                $clientNameCount = $client->filter('h1.p-profile__media-heading.c-heading.c-heading--lv2')->count();

                if($clientNameCount == 0){
                    sleep(1);
                    $client = new Client();
                    $crawler = $client->request('GET', 'https://www.lancers.jp/user/login');
                    $form = $crawler->selectButton('ログイン')->form();
                    $client->submit($form, array('data[User][email]' => 'daihusk57@gmail.com', 'data[User][password]' => 'Dai@1234'));
                    $client = $client->request('GET', $clientDetailUrl);
                }
                $clientName = $client->filter('h1.p-profile__media-heading.c-heading.c-heading--lv2')->text();


                $clientId = '';
                $clientType = '';
                $clientCity = '';
                $client->filterXPath('//ul[@class="p-profile__media-notes"]/li')->each(function ($node, $index) use (&$clientId, &$clientType, &$clientCity){
                    if($index == 0){
                        $clientId = $node->text();
                    }
                    if($index == 1){
                        $clientType = $node->text();
                    }
                    if($index == 2){
                        $clientCity = $node->text();
                    }
                });

                $clientStatus = '';
                if($client->filter('ul.p-profile__media-badges.c-status-list')->count()){
                    $clientStatus = $client->filter('ul.p-profile__media-badges.c-status-list')->text();
                }

                $clientIndustry = '';
                $clientRegisterDate = '';

                $clientIdentification = 0;
                $clientConfidentialityConfirmation = 0;
                $clientPhoneConfirmation = 0;
                $clientLancersCheck = 0;
                $client->filter('dd.p-profile__media-statuses-description')->each(function ($node, $index) use (&$clientPutForward, &$clientRegisterDate, &$clientIndustry, &$clientIdentification, &$clientConfidentialityConfirmation, &$clientPhoneConfirmation, &$clientLancersCheck) {
                    if ($index == 0) {
                        $node->filterXPath('//ul[@class="p-profile__media-status-list c-status-list"]/li')->each(function ($item, $index) use (&$clientIdentification, &$clientConfidentialityConfirmation, &$clientPhoneConfirmation, &$clientLancersCheck){
                            if($index == 0){
                                if($item->filter('i.p-profile__media-status-icon.c-status__icon.fas.fa-check')->count()){
                                    $clientIdentification = 1;
                                }
                            }
                            if($index == 1){
                                if($item->filter('i.p-profile__media-status-icon.c-status__icon.fas.fa-check')->count()){
                                    $clientConfidentialityConfirmation = 1;
                                }
                            }
                            if($index == 2){
                                if($item->filter('i.p-profile__media-status-icon.c-status__icon.fas.fa-check')->count()){
                                    $clientPhoneConfirmation = 1;
                                }
                            }
                            if($index == 3){
                                if($item->filter('i.p-profile__media-status-icon.c-status__icon.fas.fa-check')->count()){
                                    $clientLancersCheck = 1;
                                }
                            }
                        });
                    }
                    if($index == 1) {
                        $clientIndustry = $node->text();
                    }
                    if($index == 2){
                        $clientRegisterDate = $node->text();
                    }
                });

                $clientNumberOfOrder = '';
                $clientEvaluation = '';
                $clientOrderRate = '';

                $client->filter('p.c-table-summary__col-description')->each(function ($node, $index) use (&$clientNumberOfOrder, &$clientEvaluation, &$clientOrderRate){
                    if($index == 0){
                        $clientNumberOfOrder = $node->text();
                    }
                    if($index == 1){
                        $clientEvaluation = $node->text();
                    }
                    if($index == 2){
                        $clientOrderRate = $node->text();
                    }
                });

                $clientOrderRateNote = '';
                if($client->filter('p.c-table-summary__col-note')->count()){
                    $clientOrderRateNote = $client->filter('p.c-table-summary__col-note')->text();
                }

                $clientDescription = '';
                if($client->filter('div.p-profile__introduction.c-box')->count()){
                    $clientDescription = $client->filter('div.p-profile__introduction.c-box')->html();
                }

                $clientAboutUs = '';
                if($client->filter('div.p-profile__overview-lists')->count()){
                    $clientAboutUs = $client->filter('div.p-profile__overview-lists')->html();
                }

                $input = [
                  'name' => $clientName,
                  'client_id' => $clientId,
                  'client_type' => $clientType,
                  'client_city' => $clientCity,
                  'client_status' => $clientStatus,
                  'client_industry' => $clientIndustry,
                  'client_identification' => $clientIdentification,
                  'client_confidentiality_confirmation' => $clientConfidentialityConfirmation,
                  'client_phone_confirmation' => $clientPhoneConfirmation,
                  'client_lancers_check' => $clientLancersCheck,
                  'client_register_date' => $clientRegisterDate,
                  'client_number_order' => $clientNumberOfOrder,
                  'client_evaluation' => $clientEvaluation,
                  'client_order_rate' => $clientOrderRate,
                  'client_order_rate_note' => $clientOrderRateNote,
                  'client_description' => $clientDescription,
                  'client_about' => $clientAboutUs,
                ];

                if(!$this->checkClientExist($clientId)){
                    $data[] = $input;
                }

                sleep(1);
            });
            LancersClient::insert($data);
            $page++;
            file_put_contents(public_path('page.txt'), $page);
        }

    }

    public function checkClientExist($clientId)
    {
        $client = LancersClient::where('client_id', $clientId)->first();
        if($client){
            return true;
        }
        return false;
    }

    public function login()
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.lancers.jp/user/login');
        $form = $crawler->selectButton('ログイン')->form();
        return $client->submit($form, array('data[User][email]' => 'daihusk57@gmail.com', 'data[User][password]' => 'Dai@1234'));
    }
}
