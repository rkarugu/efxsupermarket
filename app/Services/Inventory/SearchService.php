<?php

namespace App\Services\Inventory;



use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchService
{
    protected $maxLevenshteinDistance = 3;
    protected $searchableFields = [
        'title' => 1.5,
        'description' => 1.0,
        'stock_id_code' => 2.0
    ];

    public function enhancedSearch(Builder $query, string $searchQuery)
    {
        if (empty($searchQuery)) {
            return $query;
        }

        // Normalize search query
        $searchTerms = array_filter(
            explode(' ', Str::lower(trim($searchQuery)))
        );

        return $query->where(function ($query) use ($searchTerms) {
            // Build the SQL for relevance scoring
            $cases = [];
            $bindings = [];

            foreach ($this->searchableFields as $field => $weight) {
                // Exact matches
                $cases[] = "CASE 
                    WHEN LOWER($field) LIKE ? THEN " . (100 * $weight) . "
                    WHEN LOWER($field) LIKE ? THEN " . (75 * $weight) . "
                    WHEN LOWER($field) LIKE ? THEN " . (50 * $weight) . "
                    ELSE 0 
                END";

                foreach ($searchTerms as $term) {
                    $bindings[] = $term; // Exact match
                    $bindings[] = "$term%"; // Starts with
                    $bindings[] = "%$term%"; // Contains
                }
            }

            // Combine all relevance scores
            $relevanceScore = '(' . implode(' + ', $cases) . ')';

            // Add the main search conditions
            $query->where(function ($innerQuery) use ($searchTerms) {
                foreach ($this->searchableFields as $field => $weight) {
                    foreach ($searchTerms as $term) {
                        $innerQuery->orWhere($field, 'LIKE', "%{$term}%");

                        // Add fuzzy matching for longer terms
                        if (strlen($term) > 3) {
                            // Get similar terms using custom function
                            $fuzzyTerms = $this->getFuzzyMatches($term);
                            foreach ($fuzzyTerms as $fuzzyTerm) {
                                $innerQuery->orWhere($field, 'LIKE', "%{$fuzzyTerm}%");
                            }
                        }
                    }
                }
            });

            // Add the relevance score as a subquery
            $query->addSelect('*', DB::raw("$relevanceScore as relevance_score"))
                ->orderByDesc('relevance_score');
        });
    }

    protected function getFuzzyMatches($term)
    {
        // Get common misspellings and variations
        $variations = [];

        // 1. Common character substitutions
        $substitutions = [
            'a' => ['e', '@'],
            'e' => ['a', '3'],
            'i' => ['y', '1'],
            'o' => ['0'],
            's' => ['5', 'z'],
            // Add more as needed
        ];

        // Generate variations
        $length = strlen($term);
        for ($i = 0; $i < $length; $i++) {
            $char = $term[$i];
            if (isset($substitutions[$char])) {
                foreach ($substitutions[$char] as $replacement) {
                    $variations[] = substr_replace($term, $replacement, $i, 1);
                }
            }
        }

        // 2. Handle common typos (character transposition)
        for ($i = 0; $i < $length - 1; $i++) {
            $variation = $term;
            $variation[$i] = $term[$i + 1];
            $variation[$i + 1] = $term[$i];
            $variations[] = $variation;
        }

        // 3. Handle missing/extra characters
        // Missing character
        for ($i = 0; $i < $length; $i++) {
            $variations[] = substr($term, 0, $i) . substr($term, $i + 1);
        }

        // Extra character
        for ($i = 0; $i <= $length; $i++) {
            foreach (str_split('abcdefghijklmnopqrstuvwxyz') as $char) {
                $variations[] = substr($term, 0, $i) . $char . substr($term, $i);
            }
        }

        // Filter variations by Levenshtein distance
        return array_filter($variations, function ($variation) use ($term) {
            return levenshtein($term, $variation) <= $this->maxLevenshteinDistance;
        });
    }
}