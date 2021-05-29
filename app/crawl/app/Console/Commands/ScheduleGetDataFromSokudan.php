<?php

namespace App\Console\Commands;

use App\Models\LancersClient;
use App\Models\SokudanCompany;
use Illuminate\Console\Command;
use Goutte\Client;

class ScheduleGetDataFromSokudan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sokudan:crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data from sokudan page!';

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
        for ($i = 1000 ; $i < 2000 ; $i++){
            sleep(1);
            $companyUrl = 'https://sokudan.work/corporations/'.$i;
            $client = new Client();
            $crawler = $client->request('GET', 'https://sokudan.work/login');
            $form = $crawler->selectButton('ログイン')->form();
            $client->submit($form, array('user[email]' => config('constants.accounts.sokudan.email'), 'user[password]' => config('constants.accounts.sokudan.pass')));
            $client = $client->request('GET', $companyUrl);


            if(!$client->filter('p.company-name__name')->count()){
                continue;
            }

            $companyId = $i;
            $companyName = $client->filter('p.company-name__name')->text();

            $companyUrl = '';
            if($client->filter('p.link')->count()){
                $companyUrl = $client->filter('p.link')->text();
            }

            $companyDescription = '';
            if($client->filter('div.main_contents')->count()){
                $companyDescription = $client->filter('div.main_contents')->html();
            }

            $foundingDate = '';
            $numberOfEmployees = '';
            $address = '';
            $client->filterXPath('//div[@class="company-detail-contents clearfix"]/div/table/tr/td')->each(function ($node, $index) use (&$foundingDate, &$numberOfEmployees, &$address) {
                if($index == 0){
                    $foundingDate = $node->text();
                }
                if($index == 1){
                    $numberOfEmployees = $node->text();
                }
                if($index == 2){
                    $address = $node->text();
                }
            });

            $evaluation = '';
            if($client->filter('header.d-lg-flex.flex-wrap.align-items-center.mt-n3.mb-8.border-bottom.pb-5.px-1.mx-n1.text-xl.text-sm-2xl.line-height-125')->count()){
                $evaluation = $client->filter('header.d-lg-flex.flex-wrap.align-items-center.mt-n3.mb-8.border-bottom.pb-5.px-1.mx-n1.text-xl.text-sm-2xl.line-height-125')->text();
                $evaluation = (float) filter_var( $evaluation, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
            }

            $evaluationOrder = '';
            $evaluationCommunication = '';
            $evaluationSchedule = '';
            $evaluationBudgetSetting = '';
            $evaluationCompliance = '';
            $evaluationCoordination = '';
            $evaluationHonesty = '';
            $client->filter('li.col-md-6.d-flex.mt-5.mt-sm-6')->each(function ($node, $index) use (&$evaluationOrder, &$evaluationCommunication, &$evaluationSchedule, &$evaluationBudgetSetting, &$evaluationCompliance, &$evaluationCoordination, &$evaluationHonesty){
                $evaluation = (float) filter_var( $node->text(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
                if($index == 0) {
                    $evaluationOrder = $evaluation;
                }

                if($index == 1) {
                    $evaluationCommunication = $evaluation;
                }

                if($index == 2) {
                    $evaluationSchedule = $evaluation;
                }

                if($index == 3) {
                    $evaluationBudgetSetting = $evaluation;
                }

                if($index == 4) {
                    $evaluationCompliance = $evaluation;
                }

                if($index == 5) {
                    $evaluationCoordination = $evaluation;
                }

                if($index == 6) {
                    $evaluationHonesty = $evaluation;
                }
            });

            $input = [
              'company_id' => $companyId,
              'name' => $companyName,
              'url' => $companyUrl,
              'description' => $companyDescription,
              'founding_date' => $foundingDate,
              'employees' => $numberOfEmployees,
              'address' => $address,
              'evaluation' => $evaluation,
              'evaluation_order' => $evaluationOrder,
              'evaluation_communication' => $evaluationCommunication,
              'evaluation_schedule' => $evaluationSchedule,
              'evaluation_budget_setting' => $evaluationBudgetSetting,
              'evaluation_compliance' => $evaluationCompliance,
              'evaluation_coordination' => $evaluationCoordination,
              'evaluation_honesty' => $evaluationHonesty,
            ];

            if(!$this->checkCompanyExist($input['company_id'])){
                SokudanCompany::insert($input);
            }

        }

    }

    public function checkCompanyExist($companyId)
    {
        $client = SokudanCompany::where('company_id', $companyId)->first();
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
