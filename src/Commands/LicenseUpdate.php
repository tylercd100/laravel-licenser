<?php 

namespace Tylercd100\License\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LicenseUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates a license quantity';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Pick values from the database
        $owner_type_classname = $this->selectOwnerType();
        $license_classname = $this->selectLicense(['owner_type' => $owner_type_classname]);
        $owner_id = $this->selectOwnerId(['owner_type' => $owner_type_classname, 'license' => $license_classname]);
        
        // Build the License instance
        $model = with(new $owner_type_classname)->newQuery()->where(["id" => $owner_id])->firstOrFail();
        $license = new $license_classname($model);

        // Perform the update
        $license->set($this->getQuantity($license->maximum()));

        $this->info("Done!");
    }

    protected function getQuantity($current = 0)
    {
        return intval($this->ask("Please select a new maximum value for this license. (The current maximum is {$current})"));
    }

    protected function selectLicense($where = [])
    {
        return $this->getSelection('license', $where);
    }

    protected function selectOwnerType($where = [])
    {
        return $this->getSelection('owner_type', $where);
    }

    protected function selectOwnerId($where = [])
    {
        return $this->getSelection('owner_id', $where);
    }

    final protected function getSelection($column, $where = [])
    {
        try {
            $options = DB::table('licenses')->where($where)->groupBy($column)->get([$column])->pluck($column)->toArray();
            if(count($options) > 1) {
                $selection = $this->choice("Select a {$column}", $options);
            } else {
                $selection = $options[0];
            }
        } catch (\OutOfBoundsException $e) {
            throw new \Exception("Could not find a {$column}", 1, $e);
        }

        $this->info("Selected: {$selection}");

        return $selection;
    }
}