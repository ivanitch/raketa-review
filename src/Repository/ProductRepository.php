<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Raketa\BackendTestTask\Repository\Entity\Product;

class ProductRepository
{
    private const PRODUCT_IS_ACTIVE = 1;

    public function __construct(
        private readonly Connection $connection
    )
    {
    }

    /**
     * Получение товара по Uuid
     *
     * - Метод `fetchOne` заменил на `fetchAssociative` (нам для на вход в метод `make` нужен массив)
     * - Заменил кавычки на одинарные
     *
     * @param string $uuid
     * @return Product
     * @throws Exception
     */
    public function getByUuid(string $uuid): Product
    {
        $sql = 'SELECT *
                FROM products 
                WHERE uuid = :uuid AND is_active = :is_active';

        $row = $this->connection->fetchAssociative($sql, [
            'uuid'      => $uuid,
            'is_active' => static::PRODUCT_IS_ACTIVE,
        ]);

        if (empty($row)) {
            throw new NotFoundException('Product not found');
        }

        return $this->make($row);
    }

    /**
     * Получение списка товаров из категории
     *
     * - Для гидрации через метод `make` нужны были все поля `*`
     * - Заменил кавычки на одинарные
     * - `category = :category` поставил на первое место, так как у нас есть составной индекс
     *
     * @param string $category
     *
     * @return array
     *
     * @throws Exception
     */
    public function getByCategory(string $category): array
    {
        $sql = 'SELECT *
                FROM products 
                WHERE category = :category AND is_active = :is_active';

        $rows = $this->connection->fetchAllAssociative($sql, [
            'category'  => $category,
            'is_active' => static::PRODUCT_IS_ACTIVE,
        ]);

        return array_map(fn(array $row) => $this->make($row), $rows);
    }

    /**
     * Гидратор товара
     *
     * @param array $row
     *
     * @return Product
     */
    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
