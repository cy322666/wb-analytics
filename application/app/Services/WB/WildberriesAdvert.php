<?php

namespace App\Services\WB;

class WildberriesAdvert extends WildberriesAdvertClient
{
    /**
     * Получение РК
     * Получение количества рекламных кампаний (РК) поставщика
     *
     * @return array
     */
    public function getCount(): mixed
    {
        return (new WildberriesData($this->getResponse('adv/v0/count')))->data;
    }

    /**
     * Список РК
     * Получение списка РК поставщика
     *
     * @param int status
     * @param int type
     * @param int limit
     * @param int offset
     * @param string order
     * @param string direction
     * @return array
     */
    public function getAdverts(
        int $status = null,
        int $type = null,
        int $limit = null,
        int $offset = null,
        string $order = null,
        string $direction = null,
    ): mixed {
        return (new WildberriesData($this->getResponse(
            'adv/v0/adverts',
            array_diff(compact('status', 'type', 'limit', 'offset', 'order', 'direction'), [''])
        )))->data;
    }

    /**
     * Информация о РК
     * Получение информации об одной РК
     *
     * @param int id
     * @return array
     */
    public function getAdvert(int $id): mixed
    {
        return (new WildberriesData($this->getResponse(
            'adv/v0/advert',
            compact('id')
        )))->data;
    }

    /**
     * Список ставок
     * Получение списка РК поставщика
     *
     * @param int type
     * @param int param
     * @return array
     */
    public function getCpm(int $type, int $param): mixed
    {
        return (new WildberriesData($this->getResponse(
            'adv/v0/cpm',
            compact('type', 'param')
        )))->data;
    }
}
