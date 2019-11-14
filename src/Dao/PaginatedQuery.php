<?php

namespace App\Dao;

use App\URL;
use App\Config\DataBase;
use \PDO;


class PaginatedQuery
{
    private $query;
    private $queryCount;
    private $pdo;
    private $perPage;
    private $count;
    private $items;

    public function __construct(
        string $query,
        string $queryCount,
        ?\PDO $pdo = null,
        int $perPage = 10
    ) {

        $this->query = $query;
        $this->queryCount = $queryCount;
        $this->pdo = $pdo ?: DataBase::getPdo();
        $this->perPage = $perPage;
    }

    public function getItems(string $classMapping): array
    {
        if ($this->items === null) {
            $currentPage = URL::getPositiveInt('page', 1);
            $pages = $this->getPages();
            // if ($currentPage > $pages) {
            //     throw new \Exception('cette page n\'existe pas');
            // }
            $offset = $this->perPage * ($currentPage - 1);
            $this->items = $this->pdo
                ->query("{$this->query}  LIMIT {$this->perPage} OFFSET $offset")
                ->fetchAll(PDO::FETCH_CLASS, $classMapping);
        }
        return $this->items;
    }

    public function previousLink(string $link): ?string
    {
        $currentPage = $this->getCurrentPage();
        if ($currentPage <= 1) return null;
        if ($currentPage > 2) $link .= "?page=" . ($currentPage - 1);
        return <<<HTML
        <a class="page-link" href="{$link}"><i class="fas fa-chevron-left"></i></a>
HTML;
    }

    public function nextLink(string $link): ?string
    {
        $currentPage = $this->getCurrentPage();
        $pages = $this->getPages();
        if ($currentPage >= $pages) return null;
        $link .= "?page=" . ($currentPage + 1);
        return <<<HTML
        <a class="page-link" href="{$link}"><i class="fas fa-chevron-right"></i></a>
HTML;
    }

    private function getCurrentPage(): int
    {
        return URL::getPositiveInt('page', 1);
    }

    private function getPages()
    {
        if ($this->count === null) {
            $this->count = (int) $this->pdo
                ->query($this->queryCount)
                ->fetch(PDO::FETCH_NUM)[0];
        }

        return ceil($this->count / $this->perPage);
    }
}
