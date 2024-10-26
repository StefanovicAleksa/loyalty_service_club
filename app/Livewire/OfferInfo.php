<?php

namespace App\Livewire;

use Livewire\Component;

class OfferInfo extends Component
{
    public $offer;
    public string $class;

    public function mount($offer, string $class = '') 
    {
        $this->offer = $offer;
        $this->class = $class;
    }

    private function getDayName(?int $dayOfWeek): ?string
    {
        if ($dayOfWeek === null) return null;
        return [
            0 => __('global.sunday'),
            1 => __('global.monday'),
            2 => __('global.tuesday'),
            3 => __('global.wednesday'),
            4 => __('global.thursday'),
            5 => __('global.friday'),
            6 => __('global.saturday'),
        ][$dayOfWeek] ?? null;
    }

    public function render()
    {
        $periodicalDetails = $this->offer->periodicalDetails;
        $isPeriodic = in_array($this->offer->offerType->id, [3, 4]);

        return view('livewire.offer-info', [
            'validUntil' => $this->offer->validity->valid_until,
            'offerType' => $this->offer->offerType->name,
            'isPeriodic' => $isPeriodic,
            'periodicity' => $isPeriodic && $periodicalDetails ? $periodicalDetails->periodicity->name : null,
            'dayName' => $isPeriodic && $periodicalDetails ? $this->getDayName($periodicalDetails->day_of_week) : null,
            'timeRange' => $isPeriodic && $periodicalDetails && $periodicalDetails->time_of_day_start && $periodicalDetails->time_of_day_end 
                ? date('H:i', strtotime($periodicalDetails->time_of_day_start)) . ' - ' . date('H:i', strtotime($periodicalDetails->time_of_day_end))
                : null,
        ]);
    }
}