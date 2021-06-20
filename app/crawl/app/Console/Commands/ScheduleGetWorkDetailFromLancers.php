<?php

namespace App\Console\Commands;

use App\Models\LancersClient;
use App\Models\LancersWorkDetail;
use Illuminate\Console\Command;
use Goutte\Client;

class ScheduleGetWorkDetailFromLancers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lancers_work_detail:crawl';

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
        $page = (int)file_get_contents(public_path('lancers_work_detail_page.txt'));
        while (true) {
            $client = new Client();
            $crawler = $client->request('GET', 'https://www.lancers.jp/work/search/design/logo?open=1&show_description=0&sort=deadlined&work_rank%5B0%5D=2&work_rank%5B1%5D=3&work_rank%5B2%5D=0&category=0&page=' . $page);
            $result = $crawler->filter('.c-media-list__item')->count();
            if ($result <= 1) {
                break;
            }
            $data = [];
            $crawler->filterXPath('//div[@class="c-media-list c-media-list--forClient"]/div')->each(function ($node) use (&$client, &$data) {
                $workId = (int)filter_var($node->attr('onclick'), FILTER_SANITIZE_NUMBER_INT);
                if ($workId) {
                    $workDetailUrl = 'https://www.lancers.jp/work/detail/' . $workId;
                    echo $workDetailUrl . "\n";
                    $client = new Client();
                    $crawler = $client->request('GET', 'https://www.lancers.jp/user/login');
                    $form = $crawler->selectButton('ログイン')->form();
                    $client->submit($form, array('data[User][email]' => config('constants.accounts.lancers.email'), 'data[User][password]' => config('constants.accounts.lancers.pass')));
                    $client = $client->request('GET', $workDetailUrl);

                    $subject = '';
                    if ($client->filter('a.c-link.c-link--black.c-link--no-visited-style')->count()) {
                        $subject = $client->filter('a.c-link.c-link--black.c-link--no-visited-style')->text();
                    }

                    $orderingParty = '';
                    if ($client->filter('span.p-work-detail__sub-heading-content')->count()) {
                        $orderingParty = $client->filter('span.p-work-detail__sub-heading-content')->text();
                        $orderingParty = str_replace('発注者: ', '', $orderingParty);
                    }

                    $aiJudgment = '';
                    if ($client->filter('div.tableSummary__head.work-rank-middle')->count()) {
                        $aiJudgment = $client->filter('div.tableSummary__head.work-rank-middle')->text();
                    }

                    $style = '';
                    if ($client->filter('span.worktype__text')->count()) {
                        $style = $client->filter('span.worktype__text')->text();
                    }

                    $price = '';
                    if ($client->filter('span.workprice__text')->count()) {
                        $price = $client->filter('span.workprice__text')->text();
                    }

                    $remainingTime = '';
                    $numberOfProposals = '';
                    $favorite = '';
                    $numberOfViews = '';
                    $client->filter('p.worksummary__text')->each(function ($node, $index) use (&$remainingTime, &$numberOfProposals, &$favorite, &$numberOfViews) {
                        if ($index == 0) $remainingTime = $node->text();
                        if ($index == 1) $numberOfProposals = $node->text();
                        if ($index == 2) $favorite = $node->text();
                        if ($index == 3) $numberOfViews = $node->text();
                    });

                    $remark1 = '';
                    if ($client->filter('ul.c-tagList')->count()) {
                        $remark1 = $client->filter('ul.c-tagList')->text();
                    }

                    $remark2 = '';
                    $client->filterXPath('//ul[@class="c-tagList tagList--warning"]/li')->each(function ($node) use (&$remark2) {
                        $remark2 .= $node->text() . ' - ';
                    });

                    $startTime = '';
                    $endTime = '';
                    if ($client->filter('span.workdetail-schedule__item__text')->count()) {
                        $client->filter('span.workdetail-schedule__item__text')->each(function ($node, $index) use (&$startTime, &$endTime) {
                            if ($index == 0) $startTime = $node->text();
                            if ($index == 1) $endTime = $node->text();
                        });
                    }

                    $logoNotationName = '';
                    $features = '';
                    $desiredColor = '';
                    $trademark = '';
                    $usage = '';
                    $deliveryFile = '';
                    $supplementary = '';
                    if ($client->filter('dd.definitionList__description')->count()) {
                        $client->filter('dd.definitionList__description')->each(function ($node, $index) use (&$logoNotationName, &$features, &$desiredColor, &$trademark, &$usage, &$deliveryFile, &$supplementary) {
                            if ($index == 0) $logoNotationName = $node->text();
                            if ($index == 1) $features = $node->html();
                            if ($index == 4) $desiredColor = $node->text();
                            if ($index == 5) $trademark = $node->text();
                            if ($index == 6) $usage = $node->text();
                            if ($index == 7) $deliveryFile = $node->text();
                            if ($index == 8) $supplementary = $node->text();
                        });
                    }

                    $hopeImage = '';
                    $client->filter('ul.logoNuanceRange__list')->each(function ($node, $index) use (&$hopeImage) {
                        if ($index == 1) {
                            preg_match_all('!\d+!', $node->html(), $matches);
                            foreach ($matches[0] as $index => $item) {
                                if ($index == 0 && (int)$item != 5) {
                                    if ((int)$item < 5) {
                                        $hopeImage .= 'シンプル : ' . $item . " - ";
                                    } else {
                                        $hopeImage .= '複雑 : ' . ($item - 5) . " - ";
                                    }
                                }
                                if ($index == 1 && (int)$item != 5) {
                                    if ((int)$item < 5) {
                                        $hopeImage .= '単色 : ' . $item . " - ";
                                    } else {
                                        $hopeImage .= 'カラフル : ' . ($item - 5) . " - ";
                                    }
                                }
                                if ($index == 2 && (int)$item != 5) {
                                    if ((int)$item < 5) {
                                        $hopeImage .= '暗い : ' . $item . " - ";
                                    } else {
                                        $hopeImage .= '明るい : ' . ($item - 5) . " - ";
                                    }
                                }
                                if ($index == 3 && (int)$item != 5) {
                                    if ((int)$item < 5) {
                                        $hopeImage .= '日常 : ' . $item . " - ";
                                    } else {
                                        $hopeImage .= '高級 : ' . ($item - 5) . " - ";
                                    }
                                }
                                if ($index == 4 && (int)$item != 5) {
                                    if ((int)$item < 5) {
                                        $hopeImage .= '遊び心 : ' . $item . " - ";
                                    } else {
                                        $hopeImage .= '厳粛 : ' . ($item - 5) . " - ";
                                    }
                                }
                                if ($index == 5 && (int)$item != 5) {
                                    if ((int)$item < 5) {
                                        $hopeImage .= '未来的 : ' . $item . " - ";
                                    } else {
                                        $hopeImage .= '伝統的 : ' . ($item - 5) . " - ";
                                    }
                                }
                                if ($index == 6 && (int)$item != 5) {
                                    if ((int)$item < 5) {
                                        $hopeImage .= '女性的 : ' . $item;
                                    } else {
                                        $hopeImage .= '男性的 : ' . ($item - 5);
                                    }
                                }
                            }
                        }
                    });

                    $desiredLogoType = '';
                    if ($client->filter('p.logoOrderDescription__list__item__title')->count()) {
                        $client->filter('p.logoOrderDescription__list__item__title')->each(function ($node, $index) use (&$desiredLogoType) {
                            $desiredLogoType .= $node->text() . ' - ';
                        });
                    }

                    $input = [
                        'work_id'             => $workId,
                        'subject'             => $subject,
                        'ordering_party'      => $orderingParty,
                        'ai_judgment'         => $aiJudgment,
                        'style'               => $style,
                        'price'               => $price,
                        'remaining_time'      => $remainingTime,
                        'number_of_proposals' => $numberOfProposals,
                        'favorite'            => $favorite,
                        'number_of_views'     => $numberOfViews,
                        'remark_1'            => $remark1,
                        'remark_2'            => $remark2,
                        'start_time'          => $startTime,
                        'end_time'            => $endTime,
                        'logo_notation_name'  => $logoNotationName,
                        'features'            => $features,
                        'desired_logo_type'   => $desiredLogoType,
                        'hope_image'          => $hopeImage,
                        'desired_color'       => $desiredColor,
                        'trademark'           => $trademark,
                        'usage'               => $usage,
                        'delivery_file'       => $deliveryFile,
                        'supplementary'       => $supplementary,
                    ];

                    if (!$this->checkWorkExist($workId)) {
                        $data[] = $input;
                    }

                    sleep(1);
                }
            });
            LancersWorkDetail::insert($data);
            $page++;
            file_put_contents(public_path('lancers_work_detail_page.txt'), $page);
        }
    }

    public function checkWorkExist($clientId)
    {
        $client = LancersWorkDetail::where('work_id', $clientId)->first();
        if ($client) {
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
