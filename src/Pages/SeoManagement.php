<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Pages;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Nomanur\FilamentSeoPro\Models\SeoMeta;
use Nomanur\FilamentSeoPro\Services\SeoAnalyzer;
use Nomanur\FilamentSeoPro\Services\SeoScoreCalculator;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Bulk SEO management page.
 *
 * Provides a table view of all content with SEO metadata,
 * allowing bulk analysis, filtering by score, and export.
 */
class SeoManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static \UnitEnum|string|null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'SEO Management';

    protected static ?string $title = 'SEO Management';

    protected static ?string $slug = 'seo-management';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament-seo-pro::pages.seo-management';

    public function table(Table $table): Table
    {
        return $table
            ->query(SeoMeta::query())
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament-seo-pro::seo.seo_title'))
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->placeholder(__('filament-seo-pro::seo.no_title')),

                TextColumn::make('seoable_type')
                    ->label(__('filament-seo-pro::seo.content_type'))
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge()
                    ->sortable(),

                TextColumn::make('seo_score')
                    ->label(__('filament-seo-pro::seo.seo_score'))
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 30 => 'danger',
                        $state <= 60 => 'warning',
                        $state <= 80 => 'info',
                        default => 'success',
                    })
                    ->suffix('/100'),

                TextColumn::make('description')
                    ->label(__('filament-seo-pro::seo.meta_description'))
                    ->limit(60)
                    ->placeholder(__('filament-seo-pro::seo.no_description'))
                    ->toggleable(),

                TextColumn::make('focus_keyword')
                    ->label(__('filament-seo-pro::seo.focus_keyword'))
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('robots')
                    ->label(__('filament-seo-pro::seo.robots'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('filament-seo-pro::seo.last_updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('score_range')
                    ->label(__('filament-seo-pro::seo.score_range'))
                    ->options([
                        'poor' => __('filament-seo-pro::seo.grade_poor') . ' (0-30)',
                        'fair' => __('filament-seo-pro::seo.grade_fair') . ' (31-60)',
                        'good' => __('filament-seo-pro::seo.grade_good') . ' (61-80)',
                        'excellent' => __('filament-seo-pro::seo.grade_excellent') . ' (81-100)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'poor' => $query->where('seo_score', '<=', 30),
                            'fair' => $query->whereBetween('seo_score', [31, 60]),
                            'good' => $query->whereBetween('seo_score', [61, 80]),
                            'excellent' => $query->where('seo_score', '>', 80),
                            default => $query,
                        };
                    }),

                SelectFilter::make('missing_meta')
                    ->label(__('filament-seo-pro::seo.missing_meta'))
                    ->options([
                        'title' => __('filament-seo-pro::seo.missing_title'),
                        'description' => __('filament-seo-pro::seo.missing_description'),
                        'keyword' => __('filament-seo-pro::seo.missing_keyword'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'title' => $query->where(fn ($q) => $q->whereNull('title')->orWhere('title', '')),
                            'description' => $query->where(fn ($q) => $q->whereNull('description')->orWhere('description', '')),
                            'keyword' => $query->where(fn ($q) => $q->whereNull('focus_keyword')->orWhere('focus_keyword', '')),
                            default => $query,
                        };
                    }),
            ])
            ->bulkActions([
                BulkAction::make('analyze')
                    ->label(__('filament-seo-pro::seo.bulk_analyze'))
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $analyzer = app(SeoAnalyzer::class);
                        $calculator = app(SeoScoreCalculator::class);

                        /** @var SeoMeta $seoMeta */
                        foreach ($records as $seoMeta) {
                            /** @var Model|null $model */
                            $model = $seoMeta->seoable;
                            if (! $model) {
                                continue;
                            }

                            $data = [
                                'title' => $seoMeta->title ?? '',
                                'description' => $seoMeta->description ?? '',
                                'focus_keyword' => $seoMeta->focus_keyword ?? '',
                                'content' => $model->{config('filament-seo-pro.default_content_field', 'content')} ?? '',
                                'slug' => $model->{config('filament-seo-pro.default_slug_field', 'slug')} ?? '',
                                'url' => '',
                            ];

                            $result = $analyzer->analyze($data);
                            $scoreData = $calculator->calculate($result);

                            $seoMeta->update(['seo_score' => $scoreData['score']]);
                        }

                        Notification::make()
                            ->title(__('filament-seo-pro::seo.analysis_complete'))
                            ->body(__('filament-seo-pro::seo.analysis_complete_body', ['count' => $records->count()]))
                            ->success()
                            ->send();
                    }),

                BulkAction::make('export')
                    ->label(__('filament-seo-pro::seo.bulk_export'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records): StreamedResponse {
                        return response()->streamDownload(function () use ($records) {
                            $csv = fopen('php://output', 'w');
                            fputcsv($csv, ['Title', 'Type', 'SEO Score', 'Description', 'Focus Keyword', 'Robots']);

                            /** @var SeoMeta $seoMeta */
                            foreach ($records as $seoMeta) {
                                fputcsv($csv, [
                                    $seoMeta->title ?? '',
                                    class_basename($seoMeta->seoable_type),
                                    $seoMeta->seo_score,
                                    $seoMeta->description ?? '',
                                    $seoMeta->focus_keyword ?? '',
                                    $seoMeta->robots ?? '',
                                ]);
                            }

                            fclose($csv);
                        }, 'seo-export-' . now()->format('Y-m-d') . '.csv');
                    }),
            ])
            ->defaultSort('seo_score', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
     }
 
     /**
      * Get the average SEO score across all tracked content.
      */
     public function getAverageScore(): int
     {
         $avg = SeoMeta::query()->avg('seo_score');
 
         return (int) round($avg ?? 0);
     }
 
     /**
      * Get count of records missing SEO titles.
      */
     public function getMissingTitlesCount(): int
     {
         return SeoMeta::query()
             ->where(fn ($query) => $query->whereNull('title')->orWhere('title', ''))
             ->count();
     }
 
     /**
      * Get count of records missing meta descriptions.
      */
     public function getMissingDescriptionsCount(): int
     {
         return SeoMeta::query()
             ->where(fn ($query) => $query->whereNull('description')->orWhere('description', ''))
             ->count();
     }
 
     /**
      * Get the percentage of content with good+ scores (>60).
      */
     public function getHealthyPercentage(): int
     {
         $total = SeoMeta::query()->count();
 
         if ($total === 0) {
             return 0;
         }
 
         $healthy = SeoMeta::query()
             ->where('seo_score', '>', 60)
             ->count();
 
         return (int) round(($healthy / $total) * 100);
     }
 
     /**
      * Get the lowest-scoring content items.
      *
      * @return Collection<int, SeoMeta>
      */
     public function getLowestScoring(int $limit = 5): Collection
     {
         return SeoMeta::query()
             ->where('seo_score', '>', 0)
             ->orderBy('seo_score', 'asc')
             ->limit($limit)
             ->get();
     }
 
     /**
      * Get the color for a score.
      */
     public function getColorForScore(int $score): string
     {
         return match (true) {
             $score <= 30 => 'danger',
             $score <= 60 => 'warning',
             $score <= 80 => 'info',
             default => 'success',
         };
     }
 }
