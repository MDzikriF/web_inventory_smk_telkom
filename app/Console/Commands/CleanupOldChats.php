<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupOldChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old chat messages and their associated media files based on retention settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $autoClearEnabled = \App\Models\Setting::get('auto_clear_chat_enabled', false);
        
        if (!$autoClearEnabled) {
            $this->info('Auto clear chat is disabled. Skipping cleanup.');
            return 0;
        }
        
        $interval = \App\Models\Setting::get('auto_clear_chat_interval', 'daily');
        
        $cutoffDate = match($interval) {
            'weekly' => Carbon::now()->subWeek(),
            'monthly' => Carbon::now()->subMonth(),
            default => Carbon::now()->subDay(),
        };
        
        $oldMessages = Message::where('created_at', '<', $cutoffDate)->get();
        
        $count = $oldMessages->count();
        
        if ($count > 0) {
            foreach ($oldMessages as $message) {
                if ($message->file_path) {
                    Storage::disk('public')->delete($message->file_path);
                }
            }
            
            Message::where('created_at', '<', $cutoffDate)->delete();
            
            $this->info("Deleted $count old chat messages.");
        } else {
            $this->info('No old messages to delete.');
        }
        
        return 0;
    }
}