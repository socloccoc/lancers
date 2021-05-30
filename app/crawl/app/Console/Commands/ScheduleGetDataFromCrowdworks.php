<?php

namespace App\Console\Commands;

use App\Models\CrowdworksClient;
use App\Models\LancersClient;
use App\Models\SokudanCompany;
use Illuminate\Console\Command;
use Goutte\Client;

class ScheduleGetDataFromCrowdworks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crowdworks:crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from crowdworks page!';

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

        $categories = config('constants.accounts.crowdworks.job_category');

        foreach ($categories as $catUrl) {
            $page = 1;
            while (true) {
                $client = new Client();
                $client = $client->request('GET', $catUrl . '?page=' . $page);

                if (!$client->filterXPath('//div[@class="search_results"]')->count()) {
                    break;
                }

                $clients = [];
                $client->filterXPath('//div[@class="search_results"]/ul/li')->each(function ($node) use (&$clients) {
                    $clientId = $node->attr('data-user_id');
                    if(!$this->checkCompanyExist($clientId)){
                        $this->getClientDetail($clientId);
                    }
                });
                $page++;
            }
        }
    }

    public function getClientDetail($clientId)
    {
        $client = new Client();
        $client = $client->request('GET', 'https://crowdworks.jp/public/employers/' . $clientId);

        $clientName = '';
        if ($client->filter('strong.user_name')->count()) {
            $clientName = $client->filter('strong.user_name')->text();
        }

        $evaluation = '';
        $evaluationCount = '';
        $jobOfferAchievementCount = '';
        $projectCompletionRate = '';
        $totalFinishedCount = '';
        $totalAcceptanceCount = '';
        if ($client->filter('div#achievements-container')->count()) {
            $data = $client->filter('div#achievements-container')->attr('data');
            $data = json_decode($data, true);
            $evaluation = $data['averageScore'];
            $evaluationCount = $data['evaluationCount'];
            $jobOfferAchievementCount = $data['jobOfferAchievementCount'];
            $projectCompletionRate = $data['projectFinishedDataJson']['rate'];
            $totalFinishedCount = $data['projectFinishedDataJson']['total_finished_count'];
            $totalAcceptanceCount = $data['projectFinishedDataJson']['total_acceptance_count'];
        }

        $input = [
            'client_id' => $clientId,
            'name' => $clientName,
            'evaluation' => $evaluation,
            'evaluation_count' => $evaluationCount,
            'job_offer_achievement_count' => $jobOfferAchievementCount,
            'project_completion_rate' => $projectCompletionRate,
            'total_finished_count' => $totalFinishedCount,
            'total_acceptance_count' => $totalAcceptanceCount,
        ];

        CrowdworksClient::insert($input);

    }

    public function checkCompanyExist($clientId)
    {
        $client = CrowdworksClient::where('client_id', $clientId)->first();
        if ($client) {
            return true;
        }
        return false;
    }

    public function login()
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://crowdworks.jp/login');
        $form = $crawler->selectButton('ログインする')->form();
        $client->submit($form, array('username' => config('constants.accounts.crowdworks.email'), 'password' => config('constants.accounts.crowdworks.email')));
    }
}
