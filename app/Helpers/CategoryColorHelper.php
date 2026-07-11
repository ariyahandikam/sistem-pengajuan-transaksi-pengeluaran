<?php

namespace App\Helpers;

use App\Models\Category;

class CategoryColorHelper
{
    /**
     * Bootstrap-alike color palette (badge = main color, progress = slightly darker shade)
     * These are standard Bootstrap 5 color hex values and darker variants for progress bars.
     */
    private static $colorPalette = [
        ['badge' => '#0d6efd', 'progress' => '#0b5ed7', 'label' => 'primary'],
        ['badge' => '#198754', 'progress' => '#146c43', 'label' => 'success'],
        ['badge' => '#dc3545', 'progress' => '#b02a37', 'label' => 'danger'],
        ['badge' => '#ffc107', 'progress' => '#e0a800', 'label' => 'warning'],
        ['badge' => '#0dcaf0', 'progress' => '#31d2f2', 'label' => 'info'],
        ['badge' => '#6c757d', 'progress' => '#5c636a', 'label' => 'secondary'],
        ['badge' => '#212529', 'progress' => '#1b1f23', 'label' => 'dark'],
    ];

    /**
     * Get colors for all categories (for bulk color assignment)
     * Uses a deterministic approach based on all categories in the database
     */
    public static function getAllCategoryColors($categories)
    {
        // Get all categories from database sorted by name for consistent ordering
        $allCategoriesSorted = Category::query()
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $colors = [];

        foreach ($categories as $categoryName) {
            // Find the index of this category in the sorted list
            $index = array_search($categoryName, $allCategoriesSorted);
            if ($index === false) {
                $index = 0; // Fallback to first color if category not found
            }
            $colors[$categoryName] = self::getCategoryColor($index);
        }

        return $colors;
    }

    /**
     * Get color for a specific category by index
     */
    public static function getCategoryColor($index)
    {
        $colorIndex = $index % count(self::$colorPalette);
        $entry = self::$colorPalette[$colorIndex];

        return [
            'badge' => $entry['badge'],
            'progress' => $entry['progress'],
            'label' => $entry['label'],
        ];
    }

    /**
     * Get color palette count
     */
    public static function getPaletteCount()
    {
        return count(self::$colorPalette);
    }
}
