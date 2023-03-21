<?php

namespace ukf;
include_once "csvdecode.php";
use br0kenpixel\CsvDecoder;

class Menu
{
    private $sourceFileName = "source/headerMenu.csv";

    public function getMenuData(string $type): array
    {
        $menu = [];

        if ($this->validateMenuType($type)) {
            if ($type === "header") {
                try {
                    $decoder = new CsvDecoder();
                    $decoder->parse_file($this->sourceFileName);
                    
                    $menu = [];
                    while($decoder->available()) {
                        $entry = $decoder->fetchOne();
                        array_push($menu, $entry);
                    }
                    $menu = $this->parseMenu($menu);
                } catch (\Exception $exception) {
                    echo $exception->getMessage();
                }
            }
        }

        return $menu;
    }

    private function parseMenu(array $menu) {
        $new_menu = [];

        for($i = 0; $i < count($menu); $i++) {
            $page = [];
            $page["name"] = $menu[$i]["name"];
            $page["path"] = $menu[$i]["path"];


            $new_menu[$menu[$i]["id"]] = $page;
        }
        return $new_menu;
    }

    public function printMenu(array $menu)
    {
        foreach ($menu as $menuName => $menuData) {
            echo '<li><a href="' . $menuData['path'] . '">' . $menuData['name'] . '</a></li>';
        }
    }

    private function validateMenuType(string $type): bool
    {
        $menuTypes = [
            'header',
            'footer'
        ];

        if (in_array($type, $menuTypes)) {
            return true;
        } else {
            return false;
        }
    }


    public function preparePortfolio(int $numberOfRows = 2, int $numberOfCols = 4): array
    {
        $portfolio = [];
        $colIndex = 1;

        for ($i = 1; $i <= $numberOfRows; $i++) {
            for ($j = 1; $j <= $numberOfCols; $j++) {
                $portfolio[$i][] = $colIndex;
                $colIndex++;
            }
        }

        return $portfolio;
    }
}


?>