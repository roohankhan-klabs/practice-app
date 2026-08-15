<?php

namespace App\Filament\Resources\Variants;

use App\Filament\Resources\Variants\Pages\CreateVariant;
use App\Filament\Resources\Variants\Pages\EditVariant;
use App\Filament\Resources\Variants\Pages\ListVariants;
use App\Filament\Resources\Variants\Pages\ViewVariant;
use App\Filament\Resources\Variants\Schemas\VariantForm;
use App\Filament\Resources\Variants\Schemas\VariantInfolist;
use App\Filament\Resources\Variants\Tables\VariantsTable;
use App\Models\VariantOption;
use App\Models\Variant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class VariantResource extends Resource
{
    protected static ?string $model = Variant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bars3BottomLeft;
    protected static string|UnitEnum|null $navigationGroup = 'Products Catalog';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'product.name',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return VariantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VariantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VariantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getSelectedOptionsState(?array $variantOptionIds): array
    {
        if (blank($variantOptionIds)) {
            return [];
        }

        $positions = array_flip($variantOptionIds);

        return VariantOption::query()
            ->whereIn('id', $variantOptionIds)
            ->get()
            ->sortBy(fn (VariantOption $option) => $positions[$option->id] ?? PHP_INT_MAX)
            ->map(fn (VariantOption $option) => [
                'variant_type_id' => $option->variant_type_id,
                'variant_option_id' => $option->id,
            ])
            ->values()
            ->all();
    }

    public static function getVariantOptionIdsFromFormData(array $data): array
    {
        $selectedOptions = collect($data['selected_options'] ?? [])
            ->filter(fn (array $option) => filled($option['variant_type_id'] ?? null) && filled($option['variant_option_id'] ?? null))
            ->values();

        if ($selectedOptions->isEmpty()) {
            throw ValidationException::withMessages([
                'selected_options' => 'Select at least one variant type and option.',
            ]);
        }

        if ($selectedOptions->pluck('variant_type_id')->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages([
                'selected_options' => 'Each variant type can only be selected once.',
            ]);
        }

        $options = VariantOption::query()
            ->whereIn('id', $selectedOptions->pluck('variant_option_id')->all())
            ->get()
            ->keyBy('id');

        return $selectedOptions
            ->map(function (array $selection) use ($options) {
                $option = $options->get($selection['variant_option_id']);

                if (! $option || (int) $option->variant_type_id !== (int) $selection['variant_type_id']) {
                    throw ValidationException::withMessages([
                        'selected_options' => 'Selected option does not belong to the chosen variant type.',
                    ]);
                }

                return (int) $option->id;
            })
            ->values()
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVariants::route('/'),
            'create' => CreateVariant::route('/create'),
            'view' => ViewVariant::route('/{record}'),
            'edit' => EditVariant::route('/{record}/edit'),
        ];
    }
}
