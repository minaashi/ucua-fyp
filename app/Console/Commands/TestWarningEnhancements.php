<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warning;
use App\Models\Report;
use App\Models\User;

class TestWarningEnhancements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:warning-enhancements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the warning system enhancements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Warning System Enhancements...');
        
        // Test 1: Check for reports with multiple warnings
        $reportsWithMultipleWarnings = Warning::select('report_id')
            ->groupBy('report_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('report_id')
            ->toArray();
            
        $this->info('Reports with multiple warnings: ' . count($reportsWithMultipleWarnings));
        
        if (count($reportsWithMultipleWarnings) > 0) {
            $this->table(['Report ID', 'Warning Count', 'Warning Types'], 
                collect($reportsWithMultipleWarnings)->map(function($reportId) {
                    $report = Report::find($reportId);
                    $warnings = $report->warnings;
                    return [
                        'RPT-' . str_pad($reportId, 3, '0', STR_PAD_LEFT),
                        $warnings->count(),
                        $warnings->pluck('type')->unique()->implode(', ')
                    ];
                })->toArray()
            );
        }
        
        // Test 2: Check warning sequence functionality
        $this->info("\nTesting warning sequence functionality...");
        
        $sampleWarning = Warning::with('report')->first();
        if ($sampleWarning) {
            $this->info("Sample Warning: {$sampleWarning->formatted_id}");
            $this->info("Report: RPT-" . str_pad($sampleWarning->report->id, 3, '0', STR_PAD_LEFT));
            $this->info("Sequence: {$sampleWarning->getSequenceNumber()} of {$sampleWarning->getTotalWarningsForReport()}");
            $this->info("Has multiple warnings: " . ($sampleWarning->hasMultipleWarnings() ? 'Yes' : 'No'));
            
            if ($sampleWarning->hasMultipleWarnings()) {
                $this->info("Sequence display: {$sampleWarning->getSequenceDisplay()}");
            }
        }
        
        // Test 3: Check duplicate prevention
        $this->info("\nTesting duplicate prevention...");
        
        $testReport = Report::first();
        if ($testReport) {
            $hasDuplicate = Warning::hasDuplicateWarning($testReport->id, 'minor');
            $this->info("Report {$testReport->id} has duplicate minor warning: " . ($hasDuplicate ? 'Yes' : 'No'));
        }
        
        // Test 4: Check warning statistics
        $this->info("\nWarning Statistics:");
        $totalWarnings = Warning::count();
        $pendingWarnings = Warning::where('status', 'pending')->count();
        $approvedWarnings = Warning::where('status', 'approved')->count();
        $sentWarnings = Warning::where('status', 'sent')->count();
        $rejectedWarnings = Warning::where('status', 'rejected')->count();
        
        $this->table(['Status', 'Count'], [
            ['Total', $totalWarnings],
            ['Pending', $pendingWarnings],
            ['Approved', $approvedWarnings],
            ['Sent', $sentWarnings],
            ['Rejected', $rejectedWarnings],
        ]);
        
        $this->info("\nWarning system enhancements test completed!");
        
        return 0;
    }
}
