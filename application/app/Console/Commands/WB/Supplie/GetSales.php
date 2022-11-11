<?php

namespace App\Console\Commands\WB\Supplie;

use App\Models\Export;
use App\Models\WB\Supplier\Order;
use App\Models\WB\Supplier\Sale;
use App\Models\WB\Supplier\Stock;
use App\Services\DB\Manager;
use App\Services\WB\RequestDto;
use App\Services\WB\ResponseParser;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use App\Services\WB\Client as WB;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class GetSales extends Command
{
    protected $signature = 'wb:supplie_sales {export}';

    protected $description = 'Command description';

    /**
     * @throws GuzzleException|DdException
     */
    public function handle(): int
    {
        $export  = Export::find($this->argument('export'));
        $account = $export->account;;
        $options = json_decode($export->options);

        $dbManager = (new Manager());
        $dbManager->init($account);

        $wb = WB::init(
            $account->token,
            $account->token32,
            $account->token64
        );

        $request = (new RequestDto());
        $request->dateFrom = '2022-09-30';
//        $request->end  = '';
//        $request->take = '';
//        $request->skip = '';

        try {
            $response = $wb->sales('supplie')->all($request);

            $response = (new ResponseParser)->parse($response);

            foreach ($response as $sale) {

                Sale::query()->create([
                    'date' => $sale->date,
                    'last_change_date' => $sale->lastChangeDate,
                    'supplier_article' => $sale->supplierArticle,
                    'tech_size' => $sale->techSize,
                    'barcode' => $sale->barcode,
                    'total_price' => $sale->totalPrice,
                    'discount_percent' => $sale->discountPercent,
                    'is_supply' => $sale->isSupply,
                    'is_realization' => $sale->isRealization,
                    'promo_code_discount' => $sale->promoCodeDiscount,
                    'warehouse_name' => $sale->warehouseName,
                    'country_name' => $sale->countryName,
                    'oblast_okrug_name' => $sale->oblastOkrugName,
                    'region_name' => $sale->regionName,
                    'income_id' => $sale->incomeID,
                    'sale_id' => $sale->saleID,
                    'od_id' => $sale->odid,
                    'spp' => $sale->spp,
                    'for_pay' => $sale->forPay,
                    'finished_price' => $sale->finishedPrice,
                    'price_with_disc' => $sale->priceWithDisc,
                    'nm_id' => $sale->nmId,
                    'subject' => $sale->subject,
                    'category' => $sale->category,
                    'brand' => $sale->brand,
                    'is_storno' => $sale->IsStorno,
                    'g_number' => $sale->gNumber,
                    'sticker' => $sale->sticker,
                ]);
            }

            return CommandAlias::SUCCESS;

        } catch (Throwable $exception) {
dd($exception->getMessage());
            Log::alert(__METHOD__.' : '.$exception->getMessage());

            return CommandAlias::FAILURE;
        }
    }
}
