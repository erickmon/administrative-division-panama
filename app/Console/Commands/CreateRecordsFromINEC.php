<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\INECData;
use App\Models\Province;
use App\Models\District;
use App\Models\Corregimiento;
use App\Models\Settlement;

use Carbon\Carbon;

class CreateRecordsFromINEC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'frominec:create-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The process search in INEC data and records them in the corresponding tables.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {                
        /**
         * In all process the concatenation of the names are used as key to avoid repeated settlement, for example:
         * Province key:        Province name
         * District key:        Province name + District name
         * Corregimiento key:   Province name + District name + Corregimiento name
         * Settlement key:      Province name + District name + Corregimiento name + Settlement name
         * 
         * $sesettlementkey = $inec->province.$inec->district.$inec->corregimiento.$inec->settlement
         */

        $inecdata = INECData::where( 'status', 0 )->get();

        /**
         * Search in INEC data and add new provinces
         * 
         */
        $provinces = [];
        $provincesarray = $this->getProvincesArray();

        foreach ($inecdata as $inec) {
            $key = $inec->province;
            if( !isset( $provincesarray[$key] ) ){
                $now = Carbon::now();

                $provinces[] = [
                    'name' => $inec->province,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $provincesarray[$key] = $inec->id;
            }
        }

        if( isset($provinces) )
            Province::insert(
                $provinces
            );
            
        /**
         * Search in inec data and add new districts
         * 
         */
        $districts = [];
        $provincesarray = $this->getProvincesArray();
        $districtsarray = $this->getDistrictsArray();

        foreach ($inecdata as $inec) {
            $key = $inec->province.$inec->district;

            if( !isset( $districtsarray[$key] ) ){
                $now = Carbon::now();

                $districts[] = [
                    'name' => $inec->district,
                    'provinces_id' => $provincesarray[$inec->province],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $districtsarray[$key] = $inec->id;

            }
        }

        if( isset($districts) )
            District::insert(
                $districts
            );
        
        /**
         * Search in inec data and add new corregimientos
         * 
         */
        $corregimientos = [];
        $districtsarray = $this->getDistrictsArray();
        $corregimientosarray = $this->getCorregimientosArray();
        
        foreach ($inecdata as $inec) {
            $key = $inec->province.$inec->district.$inec->corregimiento;

            if( !isset( $corregimientosarray[$key] ) ){
                $now = Carbon::now();

                $corregimientos[] = [
                    'name' => $inec->corregimiento,
                    'districts_id' => $districtsarray[$inec->province.$inec->district],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $corregimientosarray[$key] = $inec->id;
            }
        }

        if( isset($corregimientos) )
            Corregimiento::insert(
                $corregimientos
            );
        
        /**
         * Search in inec data and add new settlements
         * 
         */
        $settlementsarray = [];
       
        $settlements = Settlement::select(
                'settlements.id',
                'settlements.name',
                'provinces.name as province',
                'districts.name as district',
                'corregimientos.name as corregimiento'
            )
            ->join( 'corregimientos', 'settlements.corregimientos_id',  '=', 'corregimientos.id' )
            ->join( 'districts', 'corregimientos.districts_id',  '=', 'districts.id' )
            ->join( 'provinces', 'districts.provinces_id',  '=', 'provinces.id' )
            ->get()
        ;

        foreach ($settlements as $settlement) {
            $key = $settlement->province.$settlement->district.$settlement->corregimiento.$settlement->name;
            $settlementsarray[$key] = $settlement->id;
        }

        $inecstatuses = [];
        $settlements = [];
        $corregimientosarray = $this->getCorregimientosArray();

        foreach ($inecdata as $inec) {
            $key = $inec->province.$inec->district.$inec->corregimiento.$inec->settlement;
            if( !isset( $settlementsarray[$key] ) ){
                $now = Carbon::now();

                $settlements[] = [
                    'name' => $inec->settlement,
                    'corregimientos_id' => $corregimientosarray[$inec->province.$inec->district.$inec->corregimiento],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $settlementsarray[$key] = $inec->id;

                $inecadded[] = $inec->id;
            } else
                $inecrepeated[] = $inec->id;
        }

        if( isset($settlements) )
            Settlement::insert(
                $settlements
            );
        
        /**
         * Change the INEC data status so that it is not processed again
         * 
         */
            if( isset($inecadded) )
                INECData::whereIn('id', $inecadded)
                    ->update(['status' => 1])
                ;

            if( isset($inecrepeated) )
                INECData::whereIn('id', $inecrepeated)
                    ->update(['status' => 2])
                ;
        
        return 0;
    }

    private function getProvincesArray(){
        $provincesarray = [];

        foreach ( Province::get() as $province) {
            $provincesarray[$province->name] = $province->id;
        }

        return $provincesarray;
    }

    private function getDistrictsArray(){
        $districtsarray = [];

        $districts = District::select(
                'districts.id',
                'districts.name',
                'provinces.name as province'
            )
            ->join( 'provinces', 'districts.provinces_id',  '=', 'provinces.id' )
            ->get()
        ;

        foreach ($districts as $district) {
            $districtsarray[$district->province.$district->name] = $district->id;
        }

        return $districtsarray;
    }

    private function getCorregimientosArray (){
        $corregimientosarray = [];

        $corregimientos = Corregimiento::select(
                'corregimientos.id',
                'corregimientos.name',
                'provinces.name as province',
                'districts.name as district'
            )
            ->join( 'districts', 'corregimientos.districts_id',  '=', 'districts.id' )
            ->join( 'provinces', 'districts.provinces_id',  '=', 'provinces.id' )
            ->get()
        ;

        foreach ($corregimientos as $corregimiento) {
            $corregimientosarray[$corregimiento->province.$corregimiento->district.$corregimiento->name] = $corregimiento->id;
        }

        return $corregimientosarray;
    }
}
