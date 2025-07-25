<?php

namespace App\Models;

use Carbon\Carbon;
use Folklore\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    use LogsActivity;
    const METER_IMAGE = 'property/meter/image';
    const ASSESSMENT_IMAGE = 'property/assessment/image';
    const LANDLORD_IMAGE = 'property/landlord/image';
    const DELIVERED_IMAGE = 'property/delivered/image';
    const DOCUMENT_IMAGE = 'property/landownerdocuments/image';

    protected $assessmentTotalAmount;

    protected $paymentsInQuarter = [];
    protected static $logAttributesToIgnore = ['organization_tin'];
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property';
    protected $fillable = [
        'user_id',
        'street_number',
        'street_numbernew',
        'street_name',
        'ward',
        'constituency',
        'section',
        'chiefdom',
        'district',
        'province',
        'postcode',
        'organization_addresss',
        'assessment_area',
        'organization_tin',
        'organization_type',
        'organization_name',
        'is_organization',
        'is_completed',
        'is_admin_created',
        'is_property_inaccessible',
        'is_draft_delivered',
        'delivered_name',
        'delivered_number',
        'delivered_image',
        'created_from',
        'payment_migrate_from',
        // 'wall_material_percentage',
        // 'wall_material_type',
        // 'roof_material_percentage',
        // 'roof_material_type',
        // 'value_added_percentage',
        // 'value_added_type',
        // 'window_type_percentage',
        // 'window_type_type',
        'window_type_value',
        'random_id',
        'verified',
        'address_image',
        'temp_street_name',
        'temp_street_number',
        'temp_street_numbernew',
        'requested_by',
        'conveyance_image'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'is_organization' => 'boolean',
        'is_property_inaccessible' => 'boolean'
    ];

    protected $appends = [
        'delivered_image_path','address_image_path','conveyance_image_path'
    ];

    public function getDeliveredImagePathAttribute()
    {
        return $this->getDeliveredImagePath(800, 800);
    }

    public function getAddress()
    {
        $name = $this->organization_name ? $this->organization_name : optional($this->landlord)->getName();
        $geoRegistry = $this->geoRegistry;

        $digitalAddress = optional($geoRegistry)->digital_address;
        $latLong = optional($geoRegistry)->dor_lat_long;
        $lat = null;
        $long = null;

        if ($latLong) {
            $latLong = explode(', ', $latLong);

            $lat = $latLong[0];
            $long = $latLong[1];
        }
        // return '<strong>Property ID: </strong>' . $this->id . '<br><br><strong>Name: </strong>' . $name . '<br><br><strong>Open location code: </strong>' . $this->newDigitalAddress() . '<br><br><strong>Assessment Amount: </strong> NLe ' . number_format($this->assessment->property_rate_without_gst, 0, '', ',') . '<br><br><strong>Address: </strong>' . $this->street_number . ', ' . $this->street_name . ', ' . $this->ward . ', ' . $this->section . ', ' . $this->district . ', ' . $this->province . '<br><br><strong>Enumerator Name: </strong>' . $this->user->name . '<br><br><a target="_blank" href="https://www.google.com/maps/?q=' . $lat . ',' . $long . '">Go To Map</a> <a return false;" style="margin-left:20px;text-decoration:underline;" href="' . route("admin.properties.show",['property'=>$this->id ,'ref'=>'property-grid']) . '">Go to property detail</a>';

        return '<strong>Property ID: </strong>' . $this->id . '<br><br><strong>Name: </strong>' . $name . '<br><br><strong>Open location code: </strong>' . $this->newDigitalAddress() . '<br><br><strong>Assessment Amount: </strong> NLe ' . number_format($this->assessment->property_rate_without_gst, 0, '', ',') . '<br><br><strong>Address: </strong>' . $this->street_number . ', ' . $this->street_name . ', ' . $this->ward . ', ' . $this->section . ', ' . $this->section . ', ' . $this->district . ', ' . $this->province . '<br><br><strong>Enumerator Name: </strong>' . $this->user->name . '<br><br><a target="_blank" href="https://www.google.com/maps/?q=' . $lat . ',' . $long . '">Go To Map</a> <a  onclick="location.reload(); return false;" style="color:red;margin-left:20px;text-decoration:underline;" href="#">Go to property detail</a>';
    }

    public function getOnlyAddress()
    {
        return $this->street_number . ', ' . $this->street_name . ', ' . $this->ward . ', ' . $this->section . ', ' . $this->district . ', ' . $this->province;
    }

    public static function filters($filter = null)
    {
        $filters = ['' => 'Select', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'];

        return ($filter && isset($filters[$filter])) ? $filters[$filter] : $filters;
    }

    public function landlord()
    {
        return $this->hasOne(LandlordDetail::class);
    }

    public function districts()
    {
        return $this->hasOne(
            District::class,
            'name',
            'district'
        );
    }
    public function occupancy()
    {
        return $this->hasOne(OccupancyDetail::class);
    }

    public function assessment()
    {
        return $this->hasOne(PropertyAssessmentDetail::class)->latest();
    }

    public function assessments()
    {
        return $this->hasMany(PropertyAssessmentDetail::class);
    }

    public function assessmentHistory()
    {
        return $this->assessments()->whereYear('created_at', '<=', now()->format('Y'));
    }

    public function geoRegistry()
    {
        return $this->hasOne(PropertyGeoRegistry::class);
    }

    public function categories()
    {
        return $this->belongsToMany(PropertyCategory::class);
    }

    public function occupancies()
    {
        return $this->hasMany(PropertyOccupancy::class);
    }

    public function payments()
    {
        return $this->hasMany(PropertyPayment::class);
    }

    public function recentPayment()
    {
        return $this->hasOne(PropertyPayment::class)->latest();
    }

    public function getPaidAmount()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalance()
    {
        $balance = $this->getAssessment() - $this->getPaidAmount();

        return $balance;
    }

    public function getPrintableId()
    {
        return sprintf('%06d', $this->id);
    }

    // Past year dues
    public function getArrears()
    {
        return $this->assessments()->whereYear('created_at', '<', now()->year)->sum();
    }

    public function getPayingAmount()
    {
        $quarter = $this->getQuarter();

        if ($this->getPaidAmount() >= $this->getInstallmentAmount() * $quarter) {
            return 0;
        }

        return ($this->getInstallmentAmount() * $quarter) - $this->getPaidAmount();
    }

    public function getQuarter()
    {

        $from = Carbon::parse($this->created_at);
        //$from = Carbon::parse('2019-04-30 00:00:00');

        $to = Carbon::now();

        $diff = $to->diffInMonths($from);

        $subQuarter = intval(ceil($diff / 3));

        return $subQuarter <= 0 ? 1 : $subQuarter;
    }

    public function getInstallmentAmount()
    {
        return $this->getAssessment() / 4;
    }

    public function getAssessment()
    {
        if ($this->assessmentTotalAmount !== null) {
            return $this->assessmentTotalAmount;
        }
        $this->assessmentTotalAmount = $this->assessments()->sum('property_rate_without_gst');
        return $this->assessmentTotalAmount;
    }

    public function getPenalty()
    {
        $lastQuarter = $this->getQuarter() - 1;

        if ($lastQuarter <= 0) {
            return 0;
        }

        if (!$this->getPayingAmount()) {
            return 0;
        }

        if ($this->getPaidAmount() >= $this->getInstallmentAmount() * $lastQuarter) {
            return 0;
        }

        return ((($this->getInstallmentAmount() * $lastQuarter) - $this->getPaidAmount()) * 25) / 100;
    }

    public function getTotalAmount()
    {
        return $this->getPayingAmount() + $this->getPenalty();
    }

    public function types()
    {
        return $this->belongsToMany(PropertyType::class, 'property_property_type');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function registryMeters()
    {
        return $this->hasMany(RegistryMeter::class);
    }

    public function valueAdded()
    {
        return $this->belongsToMany(PropertyValueAdded::class, 'property_property_value_added');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'id' => 0
        ]);
    }

    public function getPaymentsInQuarter($year = null)
    {
        if (isset($this->paymentsInQuarter[$year])) {
            return $this->paymentsInQuarter[$year];
        }

        $year = !$year ? date('Y') : $year;

        $quarters = [
            [
                'start' => "01-01-{$year}",
                'end' => "31-03-{$year}"
            ],
            [
                'start' => "01-04-{$year}",
                'end' => "30-06-{$year}"
            ],
            [
                'start' => "01-07-{$year}",
                'end' => "30-09-{$year}"
            ],
            [
                'start' => "01-10-{$year}",
                'end' => "31-12-{$year}"
            ]
        ];

        $payments = $this->payments()->whereYear('created_at', $year)->get();

        $result = [];

        foreach ($quarters as $index => $quarter) {
            $result[$index + 1] = $payments->whereBetween('created_at', [
                Carbon::parse($quarter['start'])->startOfDay(),
                Carbon::parse($quarter['end'])->endOfDay(),
            ])->sum('amount');
        }

        $this->paymentsInQuarter[$year] = $result;

        return $this->paymentsInQuarter[$year];

        //        $paymentQuarters = [];
        //
        //        $payments = $this->payments()->whereYear('created_at', $year)->get();
        //
        //        if ($payments->count()) {
        //            foreach ($payments as $key => $payment) {
        //                $quarter = Carbon::parse($payment->created_at)->quarter;
        //
        //                if (isset($paymentQuarters[$quarter])) {
        //                    $paymentQuarters[$quarter] = $paymentQuarters[$quarter] + $payment->amount;
        //                } else {
        //                    $paymentQuarters[$quarter] = $payment->amount;
        //                }
        //            }
        //        }
        //
        //        return $paymentQuarters;
    }

    public function newDigitalAddress()
    {
        if (!empty($this->postcode) && !empty($this->geoRegistry->open_location_code)) {
            return "{$this->postcode} {$this->geoRegistry->open_location_code}";
        }
        return "";
    }

    public static function getLatLong($string = null)
    {
        if (!$string) {
            return null;
        }

        $string = explode(',', $string);

        return (isset($string[0]) ? ($string[0]) : '') . ',' . (isset($string[1]) ? ($string[1]) : '');
    }

    public function scopeWithAssessmentCalculation($query, $year)
    {
        $query->select('properties.*')->addSelect([
            \DB::raw("@current_year_payment := (
                SELECT COALESCE(SUM(amount), 0)
                from property_payments
                WHERE property_payments.property_id = properties.id and YEAR(created_at) = {$year}
            ) as current_year_payment"),
            \DB::raw("@past_years_payments := (
                SELECT COALESCE(SUM(amount), 0)
                from property_payments
                WHERE property_payments.property_id = properties.id and YEAR(created_at) < {$year}
            ) as past_years_payments"),
            \DB::raw("@previous_year_total_assessment := (SELECT COALESCE(SUM(property_rate_without_gst), 0) from property_assessment_details where property_assessment_details.property_id = properties.id and YEAR(created_at) < {$year}) as previous_year_total_assessment"),
            \DB::raw("@past_year_due := @previous_year_total_assessment - @past_years_payments as past_year_total_due "),
            \DB::raw("@penalty := GREATEST(@past_year_due * .25, 0) as penalty"),
            \DB::raw("@total_payable := @past_year_due + property_rate_without_gst + @penalty as total_payable"),
            \DB::raw("GREATEST(FLOOR(@total_payable - @current_year_payment), 0) as total_payable_due")
        ])->join('property_assessment_details as pad', function ($join) use ($year) {
            $join->on('pad.property_id', '=', 'properties.id')->on(\DB::raw('YEAR(pad.created_at)'), \DB::raw($year));
        });
    }

    public function propertyInaccessible()
    {
        return $this->belongsToMany(PropertyInaccessible::class, 'property_property_inaccessibles');
    }

    public function hasDeliveredImage()
    {
        return (bool) $this->delivered_image && file_exists($this->getDeliveredImage());
    }

    public function getDeliveredImage()
    {
        return storage_path('app/' . $this->delivered_image);
    }

    public function getDeliveredImagePath($width = 100, $height = 100)
    {
        return $this->hasDeliveredImage() ? url(Image::url($this->delivered_image, $width, $height, [])) : null;
    }

    public function generateAssessments()
    {
        $assessment = $this->assessment()->whereYear('created_at', Carbon::now('Y'))->first();

        if (!$assessment) {

            /* @var $assessment PropertyAssessmentDetail */
            /* @var $currentYearAssessment PropertyAssessmentDetail */

            $assessment = $this->assessment()->first();


            $currentYearAssessment = $assessment->replicate();

            if (Storage::has($assessment->assessment_images_1)) {

                $targetFile = Storage::path($assessment->assessment_images_1);
                $filename = Property::ASSESSMENT_IMAGE . "/copy_" . basename($targetFile);

                File::copy(Storage::path($assessment->assessment_images_1), Storage::path($filename));

                $currentYearAssessment->assessment_images_1 = $filename;
            }

            if (Storage::has($assessment->assessment_images_2)) {

                $targetFile = Storage::path($assessment->assessment_images_2);
                $filename = Property::ASSESSMENT_IMAGE . "/copy_" . basename($targetFile);

                File::copy(Storage::path($assessment->assessment_images_2), Storage::path($filename));

                $currentYearAssessment->assessment_images_2 = $filename;
            }

            $currentYearAssessment->save();

            $categories = getSyncArray($assessment->categories()->pluck('id'), ['property_id' => $this->id]);
            $currentYearAssessment->categories()->sync($categories);

            $types = getSyncArray($assessment->types()->pluck('id'), ['property_id' => $this->id]);
            $currentYearAssessment->types()->sync($types);

            $valuesAdded = getSyncArray($assessment->valuesAdded()->pluck('id'), ['property_id' => $this->id]);
            $currentYearAssessment->valuesAdded()->sync($valuesAdded);

            return $currentYearAssessment;
        }

        return $assessment;
    }

    public function boundryDelimetation()
    {
        return $this->belongsTo(BoundaryDelimitation::class, 'ward', 'ward');
    }



    //address verification images
    public function getAddressImagePathAttribute()
    {
        return $this->getAddressImagePath(800, 800);
    }
    public function getAddressImage()
    {
        return storage_path('app/' . $this->address_image);
    }
    public function hasAddressImage()
    {
        return (bool) $this->address_image && file_exists($this->getAddressImage());
    }
    public function getAddressImagePath($width = 800, $height = 800)
    {
        return $this->hasAddressImage() ? url(Image::url($this->address_image, $width, $height, [])) : null;   
    }



    //address verification conveyance images
    public function getConveyanceImagePathAttribute()
    {
        return $this->getConveyanceImagePath(800, 800);
    }
    public function getConveyanceImage()
    {
        return storage_path('app/' . $this->conveyance_image);
    }
    public function hasConveyanceImage()
    {
        return (bool) $this->conveyance_image && file_exists($this->getConveyanceImage());
    }
    public function getConveyanceImagePath($width = 800, $height = 800)
    {
        return $this->hasConveyanceImage() ? url(Image::url($this->conveyance_image, $width, $height, [])) : null;   
    }
}

