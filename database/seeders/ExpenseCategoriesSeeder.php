<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Feed',
                'description' => 'The primary operating expense. A layer farm has high consumption, requiring a consistent supply of commercial feeds.',
                'is_batch_specific' => 1
            ],
            [
                'name' => 'Vitamins & Vaccines',
                'description' => 'Essential for maintaining bird health and preventing disease.',
                'is_batch_specific' => 1
            ],
            [
                'name' => 'Labor',
                'description' => 'Feeding, cleaning, egg collection, and monitoring,',
                'is_batch_specific' => 0
            ],
            [
                'name' => 'Utilities',
                'description' => 'Electricity for lighting (to maintain 14-16 hours of light) and water supplies and etc. to continuously operate poultry house.',
                'is_batch_specific' => 1
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Expenses related to the upkeep of farm equipment, infrastructure, and facilities.',
                'is_batch_specific' => 0
            ],
            [
                'name' => 'Packaging/Supplies',
                'description' => 'Paper trays, plastic trays, egg stickers, tape etc.',
                'is_batch_specific' => 1
            ],
            [
                'name' => 'Logistics/Transpo',
                'description' => 'Gasoline, oil changes, tire repairs for the delivery bike.',
                'is_batch_specific' => 1
            ],
            [
                'name' => 'Inventory Adjustment',
                'description' => 'Adjustments made to inventory levels, including write-offs and corrections.',
                'is_batch_specific' => 1
            ]
            ];

            foreach ($categories as $category) {
                ExpenseCategory::create($category);
            }
    }
}
